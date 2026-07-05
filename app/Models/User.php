<?php

namespace App\Models;

use App\Enums\BoardingStep;
use App\Enums\KYCStatus;
use App\Enums\TxnStatus;
use App\Enums\TxnType;
use App\Enums\UserType;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, HasRoles, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    protected $appends = [
        'full_name',
        'kyc_type',
        'total_profit',
        'avatar_path',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'google2fa_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'current_step' => BoardingStep::class,
            'role' => UserType::class,
            'kyc_time' => 'datetime',
            'email_verified_at' => 'datetime',
            'two_fa' => 'boolean',
            'phone_verified' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /*
     * Scope Declaration
     * */

    public function scopeSearch($query, $search)
    {
        if ($search != null) {
            return $query->where(function ($query) use ($search) {
                $query->whereAny([
                    'first_name',
                    'last_name',
                    'username',
                    'email',
                    'phone',
                    'account_number',
                ], 'like', '%'.$search.'%');
            });
        }

        return $query;
    }

    public function scopeStatus($query, $status)
    {
        if ($status != 'all' && $status != null) {
            $status = $status == 'pending' ? KYCStatus::Pending : ($status === 'rejected' ? KYCStatus::Failed : KYCStatus::Verified);

            return $query->where('kyc', $status);
        }

        return null;
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function scopeRole($query, $role)
    {
        return $query->where('role', $role);
    }

    public function scopeClosed($query)
    {
        return $query->where('status', 2);
    }

    public function scopeDisabled($query)
    {
        return $query->where('status', 0);
    }

    public function scopeAuthor($query)
    {
        return $query->where('role', 'author');
    }

    public function scopeUser($query)
    {
        return $query->where('role', UserType::User);
    }

    protected static function booted(): void
    {
        static::creating(function ($user) {
            $user->account_number = generateAccountNumber();
            $user->referral_code = generateReferralCode();
            $user->current_step = BoardingStep::PERSONAL_INFO;
        });
    }

    /**
     * Get the avatar path.
     *
     * @param  string  $value
     * @return string
     */
    public function getAvatarPathAttribute($value)
    {
        if ($this->avatar !== null) {
            $image_exists = file_exists(public_path($this->avatar));

            if ($image_exists) {
                return asset($this->avatar);
            }
        }

        return asset('global/materials/user.png');
    }

    protected function username(): Attribute
    {
        return Attribute::make(get: function () {
            return strtolower($this->attributes['username']);
        });
    }

    protected function fullName(): Attribute
    {
        return Attribute::make(get: function () {
            return ucwords($this->first_name.' '.$this->last_name);
        });
    }

    protected function kycType(): Attribute
    {
        return Attribute::make(get: function () {
            $kycs = UserKyc::where('user_id', $this->attributes['id'])->pluck('kyc_id');

            return Kyc::whereIn('id', $kycs)->pluck('name')->implode(',');
        });
    }

    public function getTotalProfitAttribute(): string
    {
        return $this->totalProfit();
    }

    public function getTotalWithdrawAttribute(): string
    {
        return $this->totalWithdraw();
    }

    public function isKycVerified()
    {
        return $this->kyc == KYCStatus::Verified->value;
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function totalDeposit()
    {
        $sum = $this->transaction()->where('wallet_type', 'default')->where('status', TxnStatus::Success)->where(function ($query) {
            $query->whereIn('type', [TxnType::Deposit, TxnType::ManualDeposit]);
        });

        $sum = $sum->sum('amount');

        return round($sum, 2);
    }

    public function totalWithdraw()
    {
        $sum = $this->transaction()->where('wallet_type', 'default')->where('status', TxnStatus::Success)->where(function ($query) {
            $query->whereIn('type', [TxnType::Withdraw, TxnType::WithdrawAuto]);
        });

        $sum = $sum->sum('amount');

        return round($sum, 2);
    }

    public function totalProfit($days = null)
    {
        $sum = $this->transaction()->where('status', TxnStatus::Success)->where(function ($query) {
            $query->whereIn('type', [TxnType::Referral]);
        });

        if ($days != null) {
            $sum->where('created_at', '>=', now()->subDays((int) $days));
        }

        $sum = $sum->sum('amount');

        return round($sum, 2);
    }

    public function rejectedKycs()
    {
        return $this->kycs()->where('status', 'rejected');
    }

    public function kycs()
    {
        return $this->hasMany(UserKyc::class, 'user_id', 'id');
    }

    public function agent()
    {
        return $this->hasOne(Agent::class, 'user_id', 'id');
    }

    public function merchant()
    {
        return $this->hasOne(Merchant::class, 'user_id', 'id');
    }

    public function transaction()
    {
        return $this->hasMany(Transaction::class, 'user_id');
    }

    public function referrals()
    {
        return $this->hasMany(User::class, 'ref_id');
    }

    public function getTotalDepositAttribute(): string
    {
        return $this->totalDeposit();
    }

    public function referralTree()
    {
        return $this->referrals()->with('referralTree');
    }

    public function totalDepositCount()
    {
        $sum = $this->transaction()->where('status', TxnStatus::Success)->where(function ($query) {
            $query->where('type', TxnType::Deposit)
                ->orWhere('type', TxnType::ManualDeposit);
        })->count();

        return $sum;
    }

    public function totalCashinCount()
    {
        $total = $this->transaction()->where('status', TxnStatus::Success)->where(function ($query) {
            $query->where('type', TxnType::CashIn);
        })->count();

        return $total;
    }

    public function totalCashReceivedCount()
    {
        $total = $this->transaction()->where('status', TxnStatus::Success)->where(function ($query) {
            $query->where('type', TxnType::CashReceived);
        })->count();

        return $total;
    }

    public function totalDepositBonus()
    {
        $sum = $this->transaction()->where('status', TxnStatus::Success)->where(function ($query) {
            $query->where('target_id', '!=', null)
                ->where('target_type', 'deposit')
                ->where('type', TxnType::Referral);
        })->sum('amount');

        return round($sum, 2);
    }

    public function totalWithdrawCount()
    {
        $sum = $this->transaction()->where('status', TxnStatus::Success)->where(function ($query) {
            $query->where('type', TxnType::Withdraw)
                ->orWhere('type', TxnType::WithdrawAuto);
        })->count();

        return $sum;
    }

    public function totalReferralProfit()
    {
        $sum = $this->transaction()->where('status', TxnStatus::Success)->where(function ($query) {
            $query->where('type', TxnType::Referral);
        })->sum('amount');

        return round($sum, 2);
    }

    public function totalPaymentsCount()
    {
        $sum = $this->transaction()->where('status', TxnStatus::Success)->where(function ($query) {
            $query->where('type', TxnType::Payment);
        })->count();

        return $sum;
    }

    public function totalCashoutCount()
    {
        $sum = $this->transaction()->where('status', TxnStatus::Success)->where(function ($query) {
            $query->where('type', TxnType::CashOut);
        })->count();

        return $sum;
    }

    public function ticket()
    {
        return $this->hasMany(Ticket::class);
    }

    protected function google2faSecret(): Attribute
    {
        return new Attribute(
            get: fn ($value) => $value != null ? decrypt($value) : $value,
            set: fn ($value) => encrypt($value),
        );
    }

    public function activities()
    {
        return $this->hasMany(LoginActivities::class);
    }

    public function wallets()
    {
        return $this->hasMany(UserWallet::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function withdrawAccounts()
    {
        return $this->hasMany(WithdrawAccount::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }
}
