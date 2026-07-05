<?php

namespace App\Http\Controllers\Api;

use App\Enums\BoardingStep;
use App\Enums\KYCStatus;
use App\Events\RegisterProcessCompleted;
use App\Http\Controllers\Controller;
use App\Models\Kyc;
use App\Models\UserKyc;
use App\Traits\ApiResponseTrait;
use App\Traits\ImageUpload;
use App\Traits\NotifyTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;

class KycController extends Controller
{
    use ApiResponseTrait, ImageUpload, NotifyTrait;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $kycs = Kyc::where('status', true)->when(request()->get('for'), function ($query) {
            $query->where('for', request()->get('for'));
        })->get();

        $kycs->map(function ($kyc) {
            $kyc->fields = collect(json_decode($kyc->fields, true))->values();
        });

        return $this->success($kycs, __('KYC documents'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kyc_id' => 'required',
            'fields' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->validationToError($validator, 422);
        }

        $user = $request->user();

        $kyc = Kyc::find($request->kyc_id);

        // validate fields
        foreach (json_decode($kyc->fields, true) as $key => $field) {
            if ($field['validation'] == 'required' && (! isset($request->fields[$field['name']]) || empty($request->fields[$field['name']]))) {
                return $this->error(__('The :field field is required.', ['field' => $field['name']]), 422);
            }
        }

        // Check if user has kyc pending or approved for the same kyc
        if (UserKyc::where('user_id', $user->id)->whereIn('status', ['pending', 'approved'])->exists()) {
            return $this->error(__('You have already submitted this KYC'), 422);
        }

        $newKycs = $request->fields;

        foreach ($newKycs as $key => $value) {
            if (is_file($value)) {
                $newKycs[$key] = self::imageUploadTrait($value);
            }
        }

        UserKyc::create([
            'user_id' => $user->id,
            'kyc_id' => $kyc->id,
            'data' => $newKycs,
            'is_valid' => true,
            'status' => 'pending',
        ]);

        $user->update([
            'kyc' => KYCStatus::Pending,
            'current_step' => BoardingStep::ID_VERIFICATION,
        ]);

        // Register submitted event
        RegisterProcessCompleted::dispatch($user->id);

        $shortcodes = [
            '[[full_name]]' => $user->full_name,
            '[[kyc_type]]' => $kyc->name,
            '[[email]]' => $user->email,
            '[[submitted_at]]' => now(),
            '[[kyc_status_link]]' => route('admin.verification.pending'),
            '[[site_title]]' => setting('site_title', 'global'),
        ];

        $this->sendNotify(setting('site_email', 'global'), 'admin_kyc_request', 'Admin', $shortcodes, $user->phone, $user->id, route('admin.verification.pending'));

        return $this->success([], __('KYC submitted successfully'));
    }

    public function histories()
    {
        $histories = UserKyc::with('kyc')->where('user_id', request()->user()->id)->latest()->get()->map(function ($history) {
            $history->submitted_data = collect($history->data)->map(function ($value) {
                if (file_exists(base_path('public/'.$value))) {
                    return asset($value);
                }

                return $value;
            });
            unset($history->data);

            $history->type = $history->kyc->name;

            return $history;
        });

        return $this->success($histories, __('KYC histories'));
    }

    public function rejectedData()
    {
        $rejectedData = UserKyc::where('user_id', request()->user()->id)
            ->where('status', 'rejected')
            ->latest()
            ->first();

        if (! $rejectedData) {
            return $this->error(__('No rejected data found'), 404);
        }

        $formattedData = collect(json_decode($rejectedData->kyc->fields, true))->map(function ($kycData, $key) use ($rejectedData) {
            $oldValue = Arr::get($rejectedData->data, $kycData['name']);

            return [
                ...$kycData,
                'value' => filled($oldValue) && file_exists(base_path('public/'.$oldValue)) ? asset($oldValue) : null,
            ];
        });

        $rejectedData->data = $formattedData->values()->toArray();

        return $this->success($rejectedData, __('Rejected data'));
    }
}
