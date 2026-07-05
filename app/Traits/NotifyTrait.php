<?php

namespace App\Traits;

use App\Events\NotificationEvent;
use App\Mail\MailSend;
use App\Models\Notification;
use App\Models\Template;
use App\Models\UserDevice;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

trait NotifyTrait
{
    use FcmTrait, SmsTrait;

    public function sendNotify($email, $code, $for, $shortcodes, $phone, $userId, $action = '/')
    {
        $template = Template::where('for', $for)->where('code', $code)->first();

        if (! $template) {
            return null;
        }

        if ($template->email_status) {
            if ($template->code == 'email_verification') {
                return $this->mailNotify($email, $template, $shortcodes);
            }

            $this->mailNotify($email, $template, $shortcodes);
        }
        if ($template->notification_status) {
            $this->pushNotify($template, $shortcodes, $action, $userId);
        }
        if ($template->sms_status) {
            $this->smsNotify($template, $shortcodes, $phone);
        }
    }

    private function mailNotify($email, $template, $shortcodes = null)
    {
        try {
            if ($template) {
                $find = array_keys($shortcodes);
                $replace = array_values($shortcodes);
                $details = [
                    'subject' => str_replace($find, $replace, $template->subject),
                    'banner' => asset($template->banner),
                    'title' => str_replace($find, $replace, $template->title),
                    'salutation' => str_replace($find, $replace, $template->salutation),
                    'email_body' => str_replace($find, $replace, $template->email_body),
                    'button_level' => $template->button_level,
                    'button_link' => str_replace($find, $replace, $template->button_link),
                    'footer_status' => $template->footer_status,
                    'footer_body' => str_replace($find, $replace, $template->footer_body),
                    'bottom_status' => $template->bottom_status,
                    'bottom_title' => str_replace($find, $replace, $template->bottom_title),
                    'bottom_body' => str_replace($find, $replace, $template->bottom_body),
                    'site_logo' => asset(setting('site_logo', 'global')),
                    'site_title' => setting('site_title', 'global'),
                    'site_link' => '#',
                ];

                return Mail::to($email)->send(new MailSend($details));
            }
        } catch (Exception $exception) {
            Log::error('Email notification error: '.$exception->getMessage());
        }

        return null;
    }

    private function pushNotify($template, $shortcodes, $action, $userId)
    {
        try {
            if ($template) {
                $find = array_keys($shortcodes);
                $replace = array_values($shortcodes);

                $data = [
                    'icon' => $template->icon,
                    'type' => $template->code,
                    'user_id' => $userId,
                    'for' => Str::snake($template->for),
                    'title' => str_replace($find, $replace, $template->title),
                    'notice' => strip_tags(str_replace($find, $replace, $template->notification_body)),
                    'action_url' => $action,
                ];

                // Create notification record
                $notification = Notification::create($data);

                // Send push notification
                if ($notification->for != 'admin') {
                    $this->fcmNotify($notification, $userId);
                }

                // Dispatch event
                $userIdForChannel = $template->for == 'admin' ? '' : $userId;
                event(new NotificationEvent($template->for, $data, $userIdForChannel));
            }
        } catch (Exception $e) {
        }
    }

    private function smsNotify($template, $shortcodes, $phone)
    {
        if (! config('sms.default') && ! $phone) {
            return null;
        }

        try {
            if ($template) {
                $find = array_keys($shortcodes);
                $replace = array_values($shortcodes);

                $message = [
                    'sms_body' => str_replace($find, $replace, $template->sms_body),
                ];
                self::sendSms($phone, $message);
            }
        } catch (Exception $exception) {
        }

        return null;
    }

    private function fcmNotify($notification, $userId)
    {
        try {
            $title = $notification->title;
            $body = $notification->notice;

            // Get user device tokens
            $token = UserDevice::where('user_id', $userId)->first()?->fcm_token;

            if ($token == null) {
                return;
            }

            $data = [
                'user_id' => $userId,
                'for' => strtolower($notification->for),
                'title' => $title,
                'notice' => $body,
            ];

            $this->sendFcmNotification($token, $title, $body, $data);
        } catch (Exception $e) {
            // Silent fail
        }
    }
}
