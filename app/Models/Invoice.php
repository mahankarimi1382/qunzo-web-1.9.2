<?php

namespace App\Models;

use App\Enums\InvoiceType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Invoice extends Model
{
    protected $guarded = ['id'];

    public function transaction(): HasOne
    {
        return $this->hasOne(Transaction::class, 'invoice_id', 'id');
    }

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'currency', 'code');
    }

    public function markAsPaid(): self
    {
        $this->update([
            'is_paid' => true,
        ]);

        return $this;
    }

    protected function casts(): array
    {
        return [
            'items' => 'json',
            'issue_date' => 'date',
            'is_published' => 'boolean',
            'is_paid' => 'boolean',
            'type' => InvoiceType::class,
        ];
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($invoice) {
            if ($invoice->type == InvoiceType::PaymentLink) {
                $invoice->number = 'PL-'.date('Y').time();
            } else {
                $invoice->number = 'INV-'.date('Y').time();
            }
        });
    }
}
