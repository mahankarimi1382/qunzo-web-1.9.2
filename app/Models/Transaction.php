<?php

namespace App\Models;

use App\Enums\TxnStatus;
use App\Enums\TxnType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Transaction extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $appends = ['day'];

    protected $searchable = [
        'amount',
        'tnx',
        'type',
        'method',
        'description',
        'status',
        'created_at',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($transaction) {
            $transaction->tnx = 'TRX'.strtoupper(Str::random(10));
        });
    }

    public function scopeStatus($query, $status)
    {
        if ($status && $status != 'all') {
            return $query->where('status', $status);
        }

        return $query;
    }

    public function scopeProfit($query)
    {
        return $query->whereIn('type', [TxnType::Referral]);
    }

    public function scopeAgentProfit($query)
    {
        return $query->whereIn('type', [TxnType::CashoutCommission, TxnType::CashInCommission]);
    }

    public function scopeCurrency($query, $currency)
    {
        if ($currency === 'default') {
            return $query->where('wallet_type', 'default');
        }

        return $query->whereRelation('userWallet', 'currency_id', $currency);
    }

    public function scopeSearch($query, $search)
    {
        if ($search) {
            return $query->where(function ($query) use ($search) {
                $query->whereHas('user', function ($query) use ($search) {
                    $query->where('username', 'like', '%'.$search.'%');
                })->orWhereAny([
                    'tnx',
                    'description',
                ], 'like', '%'.$search.'%');
            });
        }

        return $query;
    }

    public function scopeType($query, $type)
    {
        if ($type && $type != 'all') {
            return $query->where('type', $type);
        }

        return $query;
    }

    public function scopePending($query)
    {
        return $query->where('status', TxnStatus::Pending->value);
    }

    public function scopeRejected($query)
    {
        return $query->where('status', TxnStatus::Failed->value);
    }

    public function toSearchableArray(): array
    {
        return [
            'amount' => $this->amount,
            'tnx' => $this->tnx,
            'type' => $this->type,
            'method' => $this->method,
            'description' => $this->description,
            'status' => $this->status,
        ];
    }

    protected function createdAt(): Attribute
    {
        return Attribute::make(get: function () {
            return $this->attributes['created_at'] ? Carbon::parse($this->attributes['created_at'])->format('d M Y, h:i A') : '';
        });
    }

    protected function day(): Attribute
    {
        return Attribute::make(get: function () {
            return $this->attributes['created_at'] ? Carbon::parse($this->attributes['created_at'])->format('d M') : '';
        });
    }

    protected function currency(): Attribute
    {
        return Attribute::make(get: function () {
            return $this->wallet_type !== 'default' ? $this->userWallet?->currency : setting('site_currency');
        });
    }

    protected function tranCurrency(): Attribute
    {
        return Attribute::make(get: function () {
            return $this->wallet_type !== 'default' ? $this->userWallet?->currency?->code ?? '' : setting('site_currency');
        });
    }

    public function scopeTnx($query, $tnx)
    {
        return $query->where('tnx', $tnx)->first();
    }

    public function referral()
    {
        return $this->referrals()->where('type', '=', $this->target_type);
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class, 'id', 'invoice_id');
    }

    public function referrals()
    {
        return $this->hasMany(Referral::class, 'referral_target_id', 'target_id')->where('type', '=', $this->target_type);
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withDefault();
    }

    public function fromUser()
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    public function userWallet()
    {
        return $this->belongsTo(UserWallet::class, 'wallet_type')->with('currency');
    }

    public function profit_from_user()
    {
        return $this->belongsTo(User::class, 'profit_from');
    }

    public function totalDeposit()
    {
        return $this->where('status', TxnStatus::Success)->deposit();
    }

    /**
     * Scope a query to only include deposit
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDeposit($query)
    {
        return $query->whereIn('type', [TxnType::ManualDeposit, TxnType::Deposit]);
    }

    public function totalWithdraw()
    {
        return $this->where('status', TxnStatus::Success)->where(function ($query) {
            $query->where('type', TxnType::Withdraw)
                ->orWhere('type', TxnType::WithdrawAuto);
        });
    }

    protected function method(): Attribute
    {
        return new Attribute(
            get: fn ($value) => ucwords($value),
        );
    }

    protected function casts(): array
    {
        return [
            'type' => TxnType::class,
            'status' => TxnStatus::class,
            'manual_field_data' => 'json',
        ];
    }
}
