<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BeneficiaryResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'nickname' => $this->nickname,
            'account_number' => $this->account_number,
            'receiver' => $this->receiver ? [
                'id' => $this->receiver?->id,
                'name' => $this->receiver?->full_name,
                'email' => $this->receiver?->email,
                'avatar' => $this->receiver?->avatar_path,
            ] : null,
            'created_at' => $this->created_at,
        ];
    }
}
