<?php

namespace App\Http\Controllers\Backend;

use Addons\VirtualCards\CardService;
use App\Enums\BoardingStep;
use App\Enums\TxnStatus;
use App\Enums\TxnType;
use App\Enums\UserType;
use App\Http\Controllers\Controller;
use App\Models\Card;
use App\Models\Ticket;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserKyc;
use App\Models\UserWallet;
use App\Traits\ImageUpload;
use App\Traits\NotifyTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller implements HasMiddleware
{
    use ImageUpload;
    use NotifyTrait;

    public static function middleware()
    {
        return [
            new Middleware('permission:customer-list|customer-mail-send|customer-basic-manage|customer-change-password|all-type-status|customer-balance-add-or-subtract', ['only' => ['index', 'activeUser', 'disabled', 'mailSendAll', 'mailSend']]),
            new Middleware('permission:customer-mail-send', ['only' => ['mailSendAll', 'mailSend']]),
            new Middleware('permission:customer-basic-manage', ['only' => ['update']]),
            new Middleware('permission:customer-change-password', ['only' => ['passwordUpdate']]),
            new Middleware('permission:all-type-status', ['only' => ['statusUpdate']]),
            new Middleware('permission:customer-balance-add-or-subtract', ['only' => ['balanceUpdate']]),
            new Middleware('permission:virtual-card-status-change', ['only' => ['updateCardStatus']]),
            new Middleware('permission:virtual-card-topup', ['only' => ['cardBalanceUpdate']]),
        ];
    }

    public function __construct() {}

    public function index(Request $request)
    {
        $search = $request->query('query') ?? null;

        $users = User::query()
            ->role(UserType::User)
            ->when(! blank(request('email_status')), function ($query) {
                if (request('email_status')) {
                    $query->whereNotNull('email_verified_at');
                } else {
                    $query->whereNull('email_verified_at');
                }
            })
            ->when(! blank(request('kyc_status')), function ($query) {
                $query->where('kyc', request('kyc_status'));
            })
            ->when(! blank(request('status')), function ($query) {
                $query->where('status', request('status'));
            })
            ->when(! blank(request('sort_field')), function ($query) {
                $query->orderBy(request('sort_field'), request('sort_dir'));
            })
            ->when(! request()->has('sort_field'), function ($query) {
                $query->latest();
            })
            ->search($search)
            ->paginate();

        $title = __('All Customers');

        return view('backend.user.index', ['users' => $users, 'title' => $title]);
    }

    public function activeUser(Request $request)
    {

        $search = $request->query('query') ?? null;

        $users = User::active()
            ->role(UserType::User)
            ->when(! blank(request('email_status')), function ($query) {
                if (request('email_status')) {
                    $query->whereNotNull('email_verified_at');
                } else {
                    $query->whereNull('email_verified_at');
                }
            })
            ->when(! blank(request('kyc_status')), function ($query) {
                $query->where('kyc', request('kyc_status'));
            })
            ->when(! blank(request('status')), function ($query) {
                $query->where('status', request('status'));
            })
            ->when(! blank(request('sort_field')), function ($query) {
                $query->orderBy(request('sort_field'), request('sort_dir'));
            })
            ->when(! request()->has('sort_field'), function ($query) {
                $query->latest();
            })
            ->search($search)
            ->paginate();

        $title = __('Active Customers');

        return view('backend.user.index', ['users' => $users, 'title' => $title]);
    }

    public function disabled(Request $request)
    {
        $search = $request->query('query') ?? null;

        $users = User::disabled()
            ->role(UserType::User)
            ->when(! blank(request('email_status')), function ($query) {
                if (request('email_status')) {
                    $query->whereNotNull('email_verified_at');
                } else {
                    $query->whereNull('email_verified_at');
                }
            })
            ->when(! blank(request('kyc_status')), function ($query) {
                $query->where('kyc', request('kyc_status'));
            })
            ->when(! blank(request('status')), function ($query) {
                $query->where('status', request('status'));
            })
            ->when(! blank(request('sort_field')), function ($query) {
                $query->orderBy(request('sort_field'), request('sort_dir'));
            })
            ->when(! request()->has('sort_field'), function ($query) {
                $query->latest();
            })
            ->search($search)
            ->paginate();

        $title = __('Disabled Customers');

        return view('backend.user.index', ['users' => $users, 'title' => $title]);
    }

    public function closed(Request $request)
    {
        $search = $request->query('query') ?? null;

        $users = User::closed()
            ->role(UserType::User)
            ->when(! blank(request('email_status')), function ($query) {
                if (request('email_status')) {
                    $query->whereNotNull('email_verified_at');
                } else {
                    $query->whereNull('email_verified_at');
                }
            })
            ->when(! blank(request('kyc_status')), function ($query) {
                $query->where('kyc', request('kyc_status'));
            })
            ->when(! blank(request('status')), function ($query) {
                $query->where('status', request('status'));
            })
            ->when(! blank(request('sort_field')), function ($query) {
                $query->orderBy(request('sort_field'), request('sort_dir'));
            })
            ->when(! request()->has('sort_field'), function ($query) {
                $query->latest();
            })
            ->search($search)
            ->paginate();

        $title = __('Closed Customers');

        return view('backend.user.index', ['users' => $users, 'title' => $title]);
    }

    public function edit($id)
    {
        $user = User::role(UserType::User)->findOrFail($id);

        $transactions = null;
        $tickets = null;

        if (request('tab') == 'transactions') {
            $transactions = Transaction::with('userWallet.currency')->where('user_id', $id)
                ->search(request('query'))
                ->when(request('type') != null, function ($query) {
                    $query->where('type', request('type'));
                })
                ->when(request('sort_field') != null, function ($query) {
                    $query->orderBy(request('sort_field'), request('sort_dir'));
                })
                ->when(! request()->has('sort_field'), function ($query) {
                    $query->latest();
                })
                ->paginate()
                ->withQueryString();
        } elseif (request('tab') == 'ticket') {
            $tickets = Ticket::where('user_id', $id)
                ->when(request('query') != null, function ($query) {
                    $query->where('title', 'LIKE', '%' . request('query') . '%');
                })
                ->when(in_array(request('sort_field'), ['created_at', 'title', 'status']), function ($query) {
                    $query->orderBy(request('sort_field'), request('sort_dir'));
                })
                ->when(! request()->has('sort_field'), function ($query) {
                    $query->latest();
                })
                ->paginate()
                ->withQueryString();
        }

        $statistics = [
            'total_withdraw' => $user->totalWithdrawCount(),
            'total_deposit' => $user->totalDepositCount(),
            'total_payments' => $user->totalPaymentsCount(),
            'total_cashout' => $user->totalCashoutCount(),
            'total_tickets' => $user->tickets()->count(),
            'total_referral' => $user->referrals()->count(),
        ];

        return view('backend.user.edit', [
            'user' => $user,
            'statistics' => $statistics,
            'transactions' => $transactions,
            'tickets' => $tickets,
        ]);
    }

    public function statusUpdate($id, Request $request)
    {
        DB::beginTransaction();

        try {
            $data = [
                'status' => $request->status,
                'kyc' => $request->kyc,
                'current_step' => $request->kyc ? BoardingStep::COMPLETED : BoardingStep::ID_VERIFICATION,
                'two_fa' => $request->two_fa,
                'withdraw_status' => $request->withdraw_status,
                'otp_status' => $request->otp_status,
                'payment_status' => $request->payment_status ?? 0,
                'email_verified_at' => $request->email_verified == 1 ? now() : null,
            ];

            $user = User::find($id);

            if ($user->status != $request->status && ! $request->status) {

                $shortcodes = [
                    '[[full_name]]' => $user->full_name,
                    '[[site_title]]' => setting('site_title', 'global'),
                    '[[site_url]]' => '#',
                ];

                $this->mailNotify($user->email, 'user_account_disabled', $shortcodes);
                $this->smsNotify('user_account_disabled', $shortcodes, $user->phone);
            }

            User::find($id)->update($data);

            if (! $request->kyc) {
                $this->markAsUnverified($id);
            }

            DB::commit();

            $status = 'success';
            $message = __('Status Updated Successfully');
        } catch (Exception $exception) {
            DB::rollBack();

            $status = 'warning';
            $message = __('something is wrong: ') . $exception->getMessage();
        }

        notify()->$status($message, $status);

        return back();
    }

    public function balanceUpdate($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric',
            'type' => 'required',
        ]);

        if ($validator->fails()) {
            notify()->error($validator->errors()->first(), 'Error');

            return redirect()->back();
        }

        try {
            $amount = (float) $request->amount;
            $type = $request->type;
            $user = User::find($id);
            $adminUser = Auth::user();
            $wallet_type = $request->wallet_type;
            $user_wallet = UserWallet::find($wallet_type);

            if ($wallet_type == 'default') {
                $wallet_name = 'Main';
            } else {
                $wallet_name = $user_wallet?->currency?->name;
            }

            if ($type == 'add') {
                if ($wallet_type == 'default') {
                    $user->balance += $amount;
                    $user->save();
                } else {
                    $user_wallet->balance += $amount;
                    $user_wallet->save();
                }

                Transaction::create([
                    'user_id' => $id,
                    'from_user_id' => $adminUser->id,
                    'wallet_type' => $wallet_type,
                    'from_model' => 'Admin',
                    'description' => 'Money added in ' . ucwords($wallet_name) . ' Wallet from System',
                    'type' => TxnType::Credit,
                    'amount' => $amount,
                    'charge' => 0,
                    'final_amount' => $amount,
                    'method' => 'System',
                    'status' => TxnStatus::Success,
                ]);

                $message = __('Balance added successfully!');
            } elseif ($type == 'subtract') {
                if ($wallet_type == 'default' && $user->balance < $amount) {
                    notify()->error(__('Insufficient balance in Main Wallet!'));

                    return redirect()->back();
                }

                if ($wallet_type != 'default') {
                    if (! $user_wallet) {
                        notify()->error(__('Wallet not found!'));

                        return redirect()->back();
                    }

                    if ($user_wallet->balance < $amount) {
                        notify()->error(__('Insufficient balance in :wallet wallet!', ['wallet' => ucwords($wallet_name)]));

                        return redirect()->back();
                    }
                }

                if ($wallet_type == 'default') {
                    $user->balance -= $amount;
                    $user->save();
                } else {
                    $user_wallet->balance -= $amount;
                    $user_wallet->save();
                }

                Transaction::create([
                    'user_id' => $id,
                    'from_user_id' => $adminUser->id,
                    'wallet_type' => $wallet_type,
                    'from_model' => 'Admin',
                    'description' => 'Money subtract in ' . ucwords($wallet_name) . ' Wallet from System',
                    'type' => TxnType::Debit,
                    'amount' => $amount,
                    'charge' => 0,
                    'final_amount' => $amount,
                    'method' => 'System',
                    'status' => TxnStatus::Success,
                ]);

                $message = __('Balance subtracted successfully!');
            }

            notify()->success($message);

            return redirect()->back();
        } catch (Exception $e) {
            notify()->warning(__('Sorry, something is wrong'));

            return back();
        }
    }

    protected function markAsUnverified($user_id)
    {
        UserKyc::where('user_id', $user_id)->where('is_valid', true)->update([
            'is_valid' => false,
        ]);
    }

    public function update($id, Request $request)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'first_name' => 'required',
            'last_name' => 'required',
            'username' => 'required|max:12|regex:/^[A-Za-z0-9_]+$/|unique:users,username,' . $id,
        ]);

        if ($validator->fails()) {
            notify()->error($validator->errors()->first());

            return back();
        }

        $input['date_of_birth'] = $request->date('date_of_birth');

        try {
            User::find($id)->update($input);

            $status = 'success';
            $message = __('User Info Updated Successfully');
        } catch (Exception $exception) {
            $status = 'warning';
            $message = __('something is wrong: ') . $exception->getMessage();
        }

        notify()->$status($message, $status);

        return back();
    }

    public function passwordUpdate($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'new_password' => ['required'],
            'new_confirm_password' => ['same:new_password'],
        ]);

        if ($validator->fails()) {
            notify()->error($validator->errors()->first());

            return back();
        }

        try {
            $password = $validator->validated();

            User::find($id)->update([
                'password' => bcrypt($password['new_password']),
            ]);

            $status = 'success';
            $message = __('User Password Updated Successfully');
        } catch (Exception $exception) {
            $status = 'warning';
            $message = __('something is wrong: ') . $exception->getMessage();
        }

        notify()->$status($message, $status);

        return back();
    }

    public function destroy($id)
    {
        if (config('app.demo')) {
            notify()->error(__('This action is disabled in demo!'));

            return back();
        }

        try {

            DB::beginTransaction();

            $user = User::find($id);
            $user->kycs()->delete();
            $user->invoices()->delete();
            $user->agent()->delete();
            $user->merchant()->delete();
            $user->transaction()->delete();
            $user->tickets()->delete();
            $user->activities()->delete();
            $user->notifications()->delete();
            $user->withdrawAccounts()->delete();
            $user->wallets()->delete();
            $user->delete();

            DB::commit();

            notify()->success(__('User deleted successfully'));

            return back();
        } catch (\Throwable $throwable) {
            DB::rollBack();

            notify()->error(__('Sorry, something went wrong!'));

            return back();
        }
    }

    public function updateCardStatus($card_id)
    {

        $card = Card::where('card_id', $card_id)->firstOrFail();

        try {
            // update card status
            (new CardService)->cardProviderMap($card->provider)->updateCardStatus($card);

            // Notify user and redirect back
            notify()->success(__('Card status updated successfully'));

            return back();
        } catch (\Throwable $th) {
            notify()->error($th->getMessage());

            return back();
        }
    }

    public function cardBalanceUpdate(Request $request, Card $card)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            notify()->error($validator->errors()->first());

            return back();
        }

        try {
            // Validate request data
            $balance_amount = $card->amount + $request->amount;

            // update stripe card balance
            (new CardService)->cardProviderMap($card->provider)->addCardBalance($card, $balance_amount);

            // Notify user and redirect back
            notify()->success(__('Card balance updated successfully'));

            return back();
        } catch (\Throwable $th) {
            notify()->error($th->getMessage());

            return back();
        }
    }
}
