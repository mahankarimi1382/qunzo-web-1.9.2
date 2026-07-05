<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subscriber;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SubscriberController extends Controller
{
    use ApiResponseTrait;

    public function __invoke(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string', 'email', 'max:255', 'unique:subscribers'],
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }

        Subscriber::create([
            'email' => $request->email,
        ]);

        return $this->success(__('Subscribed Successfully'));
    }
}
