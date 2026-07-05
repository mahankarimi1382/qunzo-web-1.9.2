<?php

namespace App\Services;

use App\Models\Beneficiary;
use App\Models\User;
use Illuminate\Support\Str;

class BeneficiaryService
{
    public function getBeneficiaries(int $userId, ?array $filters = [])
    {
        return Beneficiary::with('receiver')
            ->where('user_id', $userId)
            ->when(filled($filters['keyword'] ?? null), function ($query) use ($filters) {
                $keyword = $filters['keyword'];
                $query->where(function ($q) use ($keyword) {
                    $q->where('nickname', 'like', "%{$keyword}%")
                        ->orWhereHas('receiver', function ($qr) use ($keyword) {
                            $qr->where('first_name', 'like', "%{$keyword}%")
                                ->orWhere('last_name', 'like', "%{$keyword}%");
                        });
                });
            })
            ->when(filled($filters['type'] ?? null), function ($query) use ($filters) {
                $type = Str::title($filters['type']);
                $query->whereHas('receiver', function ($qr) use ($type) {
                    $qr->where('role', $type);
                });
            })
            ->latest()
            ->get();
    }

    public function getUserBeneficiary(int $beneficiaryId, int $userId): ?Beneficiary
    {
        return Beneficiary::with('receiver')
            ->where('user_id', $userId)
            ->find($beneficiaryId);
    }

    public function validateReceiver(User $user, string $accountNumber)
    {
        if ($user->account_number === $accountNumber) {
            return ['error' => __('You cannot add your own account as a beneficiary.'), 'code' => 422];
        }

        $receiver = User::where('account_number', $accountNumber)->first();

        if (! $receiver || ! $receiver->status) {
            return ['error' => __('Recipient account not found or inactive.'), 'code' => 422];
        }

        return $receiver;
    }

    public function existsDuplicate(int $userId, int $receiverId): bool
    {
        $query = Beneficiary::where('user_id', $userId)
            ->where('receiver_id', $receiverId);

        return $query->exists();
    }

    public function create(User $user, User $receiver, ?string $nickname = null): Beneficiary
    {
        return Beneficiary::create([
            'user_id' => $user->id,
            'receiver_id' => $receiver->id,
            'account_number' => $receiver->account_number,
            'nickname' => $nickname ?? $receiver->full_name,
        ]);
    }

    public function updateNickname(Beneficiary $beneficiary, ?string $nickname): Beneficiary
    {
        if ($nickname) {
            $beneficiary->nickname = $nickname;
            $beneficiary->save();
        }

        return $beneficiary;
    }
}
