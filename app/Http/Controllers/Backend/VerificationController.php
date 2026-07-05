<?php

namespace App\Http\Controllers\Backend;

use App\Enums\AgentStatus;
use App\Enums\BoardingStep;
use App\Enums\KycFor;
use App\Enums\KYCStatus;
use App\Enums\MerchantStatus;
use App\Enums\UserType;
use App\Http\Controllers\Controller;
use App\Models\Kyc;
use App\Models\User;
use App\Models\UserKyc;
use App\Traits\NotifyTrait;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Validator;

class VerificationController extends Controller implements HasMiddleware
{
    use NotifyTrait;

    public static function middleware()
    {
        return [
            new Middleware('permission:verification-form-manage', ['only' => ['create', 'store', 'show', 'edit', 'update', 'destroy']]),
            new Middleware('permission:verification-list', ['only' => ['pending', 'all', 'rejected', 'traderApplications']]),
            new Middleware('permission:verification-action', ['only' => ['verificationData', 'actionNow']]),
        ];
    }

    public function index()
    {
        $userKycs = Kyc::where('for', KycFor::User)->get();
        $agentKycs = Kyc::where('for', KycFor::Agent)->get();
        $merchantKycs = Kyc::where('for', KycFor::Merchant)->get();
        $verifiedTraderKycs = Kyc::where('for', KycFor::VerifiedTrader)->get();

        return view('backend.kyc.index', [
            'userKycs' => $userKycs,
            'agentKycs' => $agentKycs,
            'merchantKycs' => $merchantKycs,
            'verifiedTraderKycs' => $verifiedTraderKycs,
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'for' => 'required',
            'status' => 'required',
            'fields' => 'required',
        ]);

        if ($validator->fails()) {
            notify()->error($validator->errors()->first());

            return back();
        }

        // Verified Trader is only available for P2P Trading addon
        if ($request->for === KycFor::VerifiedTrader && addonActive('p2p-trading') === false) {
            notify()->error(__('Verified Trader is only available for P2P Trading addon'));

            return back();
        }

        // Check if the verified trader is already exists
        if ($request->for === KycFor::VerifiedTrader->value && Kyc::where('for', KycFor::VerifiedTrader)->exists()) {
            notify()->error(__('Verified Trader form already exists'));

            return back();
        }

        try {
            $data = [
                'name' => $request->name,
                'for' => $request->for,
                'status' => $request->status,
                'fields' => json_encode($request->fields),
            ];

            $kyc = Kyc::create($data);

            notify()->success($kyc->name . ' ' . __('Verification added successfully!'));

            return redirect()->route('admin.verification-form.index');
        } catch (\Exception $exception) {
            $status = 'error';
            $message = __('Sorry! Something went wrong.');

            notify()->$status($message, $status);

            return back();
        }
    }

    public function create()
    {
        return view('backend.kyc.create');
    }

    public function show(Kyc $kyc)
    {
        return view('backend.kyc.edit', ['kyc' => $kyc]);
    }

    public function edit($id)
    {
        $kyc = Kyc::find($id);

        return view('backend.kyc.edit', ['kyc' => $kyc]);
    }

    public function destroy($id)
    {
        try {
            Kyc::find($id)->delete();

            $status = 'success';
            $message = __('Verification deleted successfully!');
        } catch (\Exception $exception) {
            $status = 'error';
            $message = __('Sorry! Something went wrong.');
        }

        notify()->$status($message, $status);

        return redirect()->route('admin.verification-form.index');
    }

    public function pending(Request $request)
    {
        $perPage = $request->perPage ?? 15;
        $search = $request->search ?? null;
        $status = $request->status ?? null;

        $kycs = User::where('kyc', KYCStatus::Pending->value)
            ->search($search)
            ->when(in_array(request('sort_field'), ['updated_at', 'username', 'status']), function ($query) {
                $query->orderBy(request('sort_field'), request('sort_dir'));
            })
            ->status($status)
            ->latest('updated_at')
            ->paginate($perPage);

        return view('backend.kyc.pending', ['kycs' => $kycs]);
    }

    public function rejected(Request $request)
    {
        $perPage = $request->integer('perPage') ?? 15;
        $search = $request->search ?? null;
        $status = $request->status ?? null;

        $kycs = User::where('kyc', KYCStatus::Failed->value)
            ->search($search)
            ->status($status)
            ->when(in_array(request('sort_field'), ['updated_at', 'username', 'status']), function ($query) {
                $query->orderBy(request('sort_field'), request('sort_dir'));
            })
            ->latest('updated_at')
            ->paginate($perPage);

        return view('backend.kyc.rejected', ['kycs' => $kycs]);
    }

    public function verificationData(Request $request, $id)
    {
        $user = User::find($id);
        $for = $request->string('for')->toString();

        $kycsQuery = UserKyc::where('user_id', $user->id)->where('status', '!=', 'pending');
        $waitingKycsQuery = UserKyc::where('user_id', $user->id)->where('status', 'pending');

        if ($for === KycFor::VerifiedTrader->value) {
            $kycsQuery->whereHas('kyc', fn($query) => $query->where('for', KycFor::VerifiedTrader));
            $waitingKycsQuery->whereHas('kyc', fn($query) => $query->where('for', KycFor::VerifiedTrader));
        }

        $kycs = $kycsQuery->latest()->get();
        $waiting_kycs = $waitingKycsQuery->get();

        $kycStatus = $user->kyc;

        return view('backend.kyc.include.__kyc_data', ['kycs' => $kycs, 'id' => $id, 'waiting_kycs' => $waiting_kycs, 'kycStatus' => $kycStatus])->render();
    }

    public function actionNow(Request $request)
    {
        try {
            $userKyc = UserKyc::find($request->integer('id'));

            $userKyc->message = $request->get('message');
            $userKyc->status = $request->status;
            $userKyc->is_valid = $request->status == 'approved';
            $userKyc->save();

            if ($request->status == 'approved') {
                if ($userKyc->kyc->for === KycFor::VerifiedTrader) {
                    $userKyc->user->update([
                        'is_p2p_verified' => true,
                    ]);
                } else {
                    $userKyc->user->update([
                        'kyc' => KYCStatus::Verified,
                        'current_step' => BoardingStep::COMPLETED,
                    ]);

                    if ($userKyc->user->role == UserType::Agent) {
                        $userKyc->user->agent->update([
                            'status' => AgentStatus::Approved,
                        ]);
                    } elseif ($userKyc->user->role == UserType::Merchant) {
                        $userKyc->user->merchant->update([
                            'status' => MerchantStatus::Approved,
                        ]);
                    }
                }
            } else {
                if ($userKyc->kyc->for === KycFor::VerifiedTrader) {
                    $userKyc->user->update([
                        'is_p2p_verified' => false,
                    ]);
                } else {
                    $userKyc->user->update([
                        'kyc' => KYCStatus::Failed,
                        'current_step' => BoardingStep::ID_VERIFICATION,
                    ]);

                    if ($userKyc->user->role == UserType::Agent) {
                        $userKyc->user->agent->update([
                            'status' => AgentStatus::Rejected,
                        ]);
                    } elseif ($userKyc->user->role == UserType::Merchant) {
                        $userKyc->user->merchant->update([
                            'status' => MerchantStatus::Rejected,
                        ]);
                    }
                }
            }

            $user = $userKyc->user;

            $templateCode = 'kyc_action';
            if ($userKyc->kyc->for === KycFor::VerifiedTrader) {
                $templateCode = $request->status == 'approved' ? 'p2p_trader_approved' : 'p2p_trader_rejected';
            }

            $shortcodes = [
                '[[full_name]]' => $user->full_name,
                '[[status]]' => $request->status,
                '[[message]]' => $request->message,
                '[[kyc_status_link]]' => '',
                '[[p2p_link]]' => url('/p2p'),
                '[[reapply_link]]' => url('/p2p/verified-trader-application'),
                '[[site_title]]' => setting('site_title', 'global'),
            ];

            $this->sendNotify($user->email, $templateCode, 'User', $shortcodes, $user->phone, $user->id, '');

            notify()->success(__('Verification Updated Successfully'));

            $redirectRoute = $userKyc->kyc->for === KycFor::VerifiedTrader
                ? 'admin.verification.trader-applications'
                : 'admin.verification.all';

            return redirect()->route($redirectRoute);
        } catch (\Exception $exception) {

            notify()->warning(__('Sorry, something is wrong!'));

            return back();
        }
    }

    public function update(Request $request, $id)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'name' => 'required',
            'status' => 'required',
            'for' => 'required',
            'fields' => 'required',
        ]);

        if ($validator->fails()) {
            notify()->error($validator->errors()->first());

            return redirect()->back();
        }

        $data = [
            'name' => $input['name'],
            'for' => $input['for'],
            'status' => $input['status'],
            'fields' => json_encode($input['fields']),
        ];

        $kyc = Kyc::find($id);
        $kyc->update($data);

        notify()->success(__(':name Verification Updated', ['name' => $kyc->name]));

        return redirect()->route('admin.verification-form.index');
    }

    public function all(Request $request)
    {
        $perPage = $request->perPage ?? 15;
        $search = $request->search ?? null;
        $status = $request->status ?? 'all';

        $kycs = User::query()
            ->has('kycs')
            ->when(in_array(request('sort_field'), ['updated_at', 'username', 'status']), function ($query) {
                $query->orderBy(request('sort_field'), request('sort_dir'));
            })
            ->search($search)
            ->status($status)
            ->latest('updated_at')
            ->paginate($perPage);

        return view('backend.kyc.all', ['kycs' => $kycs]);
    }

    public function traderApplications(Request $request)
    {
        $perPage = $request->integer('perPage') ?? 15;
        $search = $request->search;
        $status = $request->status ?? 'all';

        $traderApplications = UserKyc::query()
            ->with(['user', 'kyc'])
            ->whereHas('kyc', fn($query) => $query->where('for', KycFor::VerifiedTrader))
            ->when($search, function ($query) use ($search) {
                $query->whereHas('user', function ($userQuery) use ($search) {
                    $userQuery->whereAny(['first_name', 'last_name', 'username', 'email', 'phone', 'account_number'], 'like', '%'.$search.'%');
                });
            })
            ->when(in_array($status, ['pending', 'approved', 'rejected']), fn($query) => $query->where('status', $status))
            ->when(in_array($request->get('sort_field'), ['created_at', 'status']), function ($query) use ($request) {
                $query->orderBy($request->get('sort_field'), $request->get('sort_dir', 'desc'));
            })
            ->latest('created_at')
            ->paginate($perPage);

        return view('backend.kyc.trader_applications', [
            'traderApplications' => $traderApplications,
        ]);
    }
}
