<?php

namespace App\Facades\Txn;

use App\Enums\TxnStatus;
use App\Enums\TxnType;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserWallet;
use App\Traits\NotifyTrait;
use Illuminate\Support\Facades\Auth;

class Txn
{
    use NotifyTrait;

    public function new($amount, $charge, $final_amount, $userWallet, $method, $description, string|TxnType $type, string|TxnStatus $status = TxnStatus::Pending, $payCurrency = null, $payAmount = null, $userID = null, $relatedUserID = null, $relatedModel = 'User', array $manualFieldData = [], string $approvalCause = 'none', $targetId = null, $targetType = null, $isLevel = false): Transaction
    {
        if ($type === 'withdraw') {
            self::withdrawBalance($amount);
        }

        $transaction = new Transaction;
        $transaction->user_id = $userID ?? Auth::user()->id;
        $transaction->from_user_id = $relatedUserID;
        $transaction->from_model = $relatedModel;
        $transaction->wallet_type = $userWallet;
        $transaction->description = $description;
        $transaction->amount = $amount;
        $transaction->type = $type;
        $transaction->charge = $charge;
        $transaction->final_amount = $final_amount;
        $transaction->method = $method;
        $transaction->pay_currency = $payCurrency;
        $transaction->manual_field_data = $manualFieldData;
        $transaction->approval_cause = $approvalCause;
        $transaction->target_id = $targetId;
        $transaction->target_type = $targetType;
        $transaction->is_level = $isLevel;
        $transaction->status = $status;

        $transaction->save();

        return $transaction;
    }

    private function withdrawBalance($amount): void
    {
        User::find(Auth::user()->id)->removeMoney($amount);
    }

    public function update($tnx, $status, $userid = null, $approvalCause = null)
    {

        $transaction = Transaction::tnx($tnx);
        $user = User::find($transaction->user_id);

        if ($status == TxnStatus::Success && ($transaction->type == TxnType::Deposit || $transaction->type == TxnType::ManualDeposit)) {
            $amount = $transaction->amount;

            if ($transaction->wallet_type == null || $transaction->wallet_type == 'default') {
                $user->increment('balance', $amount);
            } else {
                $user_wallet = UserWallet::find($transaction->wallet_type);

                if ($user_wallet) {
                    $user_wallet->increment('balance', $amount);
                }
            }
        }

        $data = [
            'status' => $status,
            'approval_cause' => $approvalCause,
        ];

        return $transaction->update($data);
    }
}
