<?php

namespace App\Facades\Notification;

class Notify
{
    /**
     * Flash a notification to the session.
     */
    protected function flash(string $type, string $message, ?string $title = null): void
    {
        $defaultTitle = match ($type) {
            'info' => __('Info'),
            'success' => __('Success'),
            'error' => __('Error'),
            'warning' => __('Warning'),
            default => __('Notification'),
        };

        session()->flash('notify', [
            'type' => $type,
            'message' => $message,
            'title' => $title ?? $defaultTitle,
        ]);
    }

    public function info(string $message, ?string $title = null): void
    {
        $this->flash('info', $message, $title);
    }

    public function success(string $message, ?string $title = null): void
    {
        $this->flash('success', $message, $title);
    }

    public function error(string $message, ?string $title = null): void
    {
        $this->flash('error', $message, $title);
    }

    public function warning(string $message, ?string $title = null): void
    {
        $this->flash('warning', $message, $title);
    }
}
