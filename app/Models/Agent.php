<?php

namespace App\Models;

use App\Enums\AgentStatus;
use Illuminate\Database\Eloquent\Model;

class Agent extends Model
{
    protected $guarded = ['id'];

    public function scopeSearch($query, $search)
    {
        if ($search != null) {
            return $query->whereHas('user', function ($q) use ($search) {
                $q->where('first_name', 'LIKE', '%'.$search.'%')
                    ->orWhere('last_name', 'LIKE', '%'.$search.'%')
                    ->orWhere('username', 'LIKE', '%'.$search.'%')
                    ->orWhere('email', 'LIKE', '%'.$search.'%')
                    ->orWhere('phone', 'LIKE', '%'.$search.'%');
            });
        }

        return $query;
    }

    public function scopeStatus($query, $status)
    {
        if ($status != null && $status != 'all') {
            return $query->where('status', $status);
        }

        return $query;
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    protected function casts()
    {
        return [
            'status' => AgentStatus::class,
            'data' => 'json',
        ];
    }
}
