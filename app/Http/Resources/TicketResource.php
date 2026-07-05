<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TicketResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'title' => $this->title,
            'priority' => $this->priority,
            'status' => $this->status,
            'message' => $this->message,
            'attachments' => $this->getProcessedAttachments(),
            'messages_count' => $this->whenLoaded('messages', function () {
                return $this->messages->count();
            }) ?? $this->messages_count ?? 0,
            'is_closed' => $this->isClosed(),
            'can_reply' => ! $this->isClosed(),
            'user' => $this->whenLoaded('user', function () {
                $user = $this->user;

                return [
                    'id' => $user->id,
                    'name' => $user->full_name,
                    'avatar' => asset($user->avatar_path),
                    'email' => $user->email,
                ];
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    private function getProcessedAttachments()
    {
        $attachments = $this->attachments ?? [];

        return collect($attachments)->map(function ($attachment) {
            return [
                'name' => basename($attachment),
                'url' => asset($attachment),
                'size' => file_exists(public_path($attachment)) ? filesize(public_path($attachment)) : 0,
                'type' => pathinfo($attachment, PATHINFO_EXTENSION),
            ];
        })->toArray();
    }
}
