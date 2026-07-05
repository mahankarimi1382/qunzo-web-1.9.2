<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\SetTune;
use App\Models\User;
use App\Traits\NotifyTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Validator;

class NotificationController extends Controller implements HasMiddleware
{
    use NotifyTrait;

    public static function middleware()
    {
        return [
            new Middleware('permission:push-notification-template', ['only' => ['template', 'editTemplate', 'updateTemplate']]),
            new Middleware('permission:mail-send-all', ['only' => ['mailSendAll', 'mailSend']]),
        ];
    }

    public function latestNotification()
    {
        return true;
    }

    public function all()
    {
        $notifications = Notification::where('for', 'admin')->latest()->paginate(10);

        return view('backend.notification.index', ['notifications' => $notifications]);
    }

    public function status($id)
    {
        try {
            $set_tune = SetTune::find($id);

            if ($set_tune->status == 0) {
                $set_tune->status = 1;
                $set_tune->save();

                SetTune::whereNot('id', $id)->update(['status' => false]);

                notify()->success(__('Settings has been saved'));

                return back();
            }

            $set_tune->status = 0;
            $set_tune->save();

            SetTune::where('id', SetTune::first()->id)->update(['status' => true]);

            $status = 'success';
            $message = __('Settings has been saved');
        } catch (\Exception $exception) {
            $status = 'warning';
            $message = __('something is wrong: ') . $exception->getMessage();
        }

        notify()->$status($message, $status);

        return back();
    }

    public function readNotification($id)
    {
        if ($id == 0) {
            Notification::where('for', 'admin')->update(['read' => 1]);

            return back();
        }

        $notification = Notification::find($id);
        if ($notification->read == 0) {
            $notification->read = 1;
            $notification->save();
        }

        return redirect()->to($notification->action_url);
    }

    public function mailSendAll()
    {
        return view('backend.notification-send.all');
    }

    public function mailSend(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subject' => 'required',
            'message' => 'required',
            'id' => 'nullable|exists:users,id',
            'user_types' => 'required_without:id|array',
        ]);

        if ($validator->fails()) {
            notify()->error($validator->errors()->first());

            return back();
        }

        try {

            $input = [
                'subject' => $request->subject,
                'message' => $request->message,
            ];

            $shortcodes = [
                '[[site_url]]' => '#',
                '[[site_title]]' => setting('site_title', 'global'),
                '[[subject]]' => $input['subject'],
                '[[message]]' => $input['message'],
            ];

            if ($request->id !== null) {
                $user = User::find($request->id);

                $shortcodes = array_merge($shortcodes, ['[[full_name]]' => $user->full_name]);

                $this->sendNotify($user->email, 'user_mail', 'User', $shortcodes, $user->phone, $user->id, '#');
            } else {
                $users = User::where('status', 1)->whereIn('role', $request->user_types)->get();

                foreach ($users as $user) {
                    $shortcodes = array_merge($shortcodes, ['[[full_name]]' => $user->full_name]);

                    $this->sendNotify($user->email, 'user_mail', 'User', $shortcodes, $user->phone, $user->id, '#');
                }
            }

            $status = 'Success';
            $message = __('Mail Send Successfully');
        } catch (Exception $exception) {

            throw $exception;
            $status = 'warning';
            $message = __('Sorry, something is wrong');
        }

        notify()->$status($message, $status);

        return back();
    }
}
