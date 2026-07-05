<?php

namespace App\Models;

use App\Enums\TicketStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ticket extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function scopeUuid($query, $uuid)
    {
        return $query->where('uuid', $uuid)->firstOrFail();
    }

    public function scopeOrder($query, string $order)
    {
        return $query->orderBy('id', $order);
    }

    public function scopeClosed($query)
    {
        return $query->where('status', TicketStatus::CLOSED->value);
    }

    public function scopeOpened($query)
    {
        return $query->where('status', TicketStatus::OPEN->value);
    }

    public function scopeSearch($query, $search)
    {
        if ($search) {
            return $query->where(function ($query) use ($search) {
                $query->whereHas('user', function ($query) use ($search) {
                    $query->where('username', 'like', '%'.$search.'%');
                })->orWhere('uuid', 'like', '%'.$search.'%')
                    ->orWhere('title', 'like', '%'.$search.'%');
            });
        }

        return $query;
    }

    public function scopeStatus($query, $status)
    {
        if ($status && $status != 'all') {
            $query->where('status', $status);
        }

        return $query;
    }

    public function isOpen(): bool
    {
        return $this->status == TicketStatus::OPEN->value;
    }

    public function isClosed(): bool
    {
        return ! $this->isOpen();
    }

    public function close(): self
    {
        $this->update([
            'status' => TicketStatus::CLOSED->value,
        ]);

        return $this;
    }

    public function reopen(): self
    {
        $this->update([
            'status' => TicketStatus::OPEN,
        ]);

        return $this;
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class, 'ticket_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected function casts(): array
    {
        return [
            'attachments' => 'json',
            'created_at' => 'datetime',
        ];
    }
}
