<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TicketMessageResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'message' => $this->message,
            'attachments' => $this->getProcessedAttachments(),
            'user' => [
                'name' => $this->sender->name ?? $this->sender->full_name ?? null,
                'email' => $this->sender->email ?? null,
                'avatar' => $this->sender->avatar_path ?? null,
            ],
            'is_admin' => $this->model == 'admin',
            'created_at' => $this->created_at,
            'created_at_formatted' => $this->created_at ? $this->created_at->format('d M, Y h:i A') : null,
        ];
    }

    private function getProcessedAttachments()
    {
        $attachments = $this->attach ?? [];

        return collect($attachments)->map(function ($attachment) {
            $path = public_path($attachment);

            return [
                'name' => basename($attachment),
                'url' => asset($attachment),
                'size' => file_exists($path) ? filesize($path) : 0,
                'type' => pathinfo($attachment, PATHINFO_EXTENSION),
            ];
        })->toArray();
    }
}
