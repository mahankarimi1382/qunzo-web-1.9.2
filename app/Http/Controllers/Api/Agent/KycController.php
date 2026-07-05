<?php

namespace App\Http\Controllers\Api\Agent;

use App\Enums\KYCStatus;
use App\Http\Controllers\Controller;
use App\Models\Kyc;
use App\Models\UserKyc;
use App\Traits\ApiResponseTrait;
use App\Traits\ImageUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class KycController extends Controller
{
    use ApiResponseTrait, ImageUpload;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $userKycIds = UserKyc::whereIn('status', ['pending', 'approved'])->where('user_id', auth()->id())->where('is_valid', true)->pluck('kyc_id');

        $kycs = Kyc::where('status', true)->whereNotIn('id', $userKycIds)->get();

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

        $kyc = Kyc::find($request->kyc_id);

        // validate fields

        foreach (json_decode($kyc->fields, true) as $key => $field) {
            if ($field['validation'] == 'required' && (! isset($request->fields[$field['name']]) || empty($request->fields[$field['name']]))) {
                return $this->error(__('The :field field is required.', ['field' => $field['name']]), 422);
            }
        }
        $user = auth()->user();

        $newKycs = $request->fields;

        foreach ($newKycs as $key => $value) {
            if (is_file($value)) {
                $newKycs[$key] = self::imageUploadTrait($value);
            }
        }

        UserKyc::create([
            'user_id' => $user->id,
            'kyc_id' => $kyc->id,
            'type' => $kyc->name,
            'data' => $newKycs,
            'is_valid' => true,
            'status' => 'pending',
        ]);

        $pendingCount = UserKyc::where('user_id', $user->id)->whereIn('status', ['pending', 'approved'])->where('is_valid', true)->count();
        $isPending = Kyc::where('status', true)->count() == $pendingCount ? true : false;

        $user->update([
            'kyc' => $isPending ? KYCStatus::Pending : KYCStatus::NOT_SUBMITTED,
        ]);

        return $this->success([], __('KYC submitted successfully'));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $kyc = UserKyc::find($id);

        $kyc->data = collect($kyc->data)->map(function ($value) {
            if (file_exists(base_path('public/'.$value))) {
                return asset($value);
            }

            return $value;
        });

        return $this->success($kyc, __('KYC details'));
    }

    public function histories()
    {
        $histories = UserKyc::where('user_id', auth()->id())->latest()->get()->map(function ($history) {
            $history->data = collect($history->data)->map(function ($value) {
                if (file_exists(base_path('assets/'.$value))) {
                    return asset($value);
                }

                return $value;
            });

            return $history;
        });

        return $this->success($histories, __('KYC histories'));
    }
}
