<?php

namespace App\Http\Controllers\Backend;

use Addons\P2PTrading\Models\AdsOrder;
use Addons\P2PTrading\Services\OrderService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class P2pOrderMessageController extends Controller implements HasMiddleware
{
    public function __construct(
        protected OrderService $orderService
    ) {
        abort_if(! addonActive('p2p-trading'), 404);
    }

    public static function middleware()
    {
        return [
            new Middleware('permission:p2p-orders-chat-manage'),
        ];
    }

    public function index($id)
    {
        $order = $this->orderService->getOrder($id);
        $messages = $this->orderService->listOrderMessages($order);

        return view('backend.p2p.orders.messages', [
            'order' => $order,
            'messages' => $messages,
        ]);
    }

    public function store(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'nullable|string|max:5000',
            'attachment' => 'nullable|file|max:5120|mimes:jpeg,png,jpg,gif,svg,webp,pdf,doc,docx',
        ]);

        if ($validator->fails()) {
            notify()->error($validator->errors()->first());
            return back()->withInput();
        }

        try {
            $order = AdsOrder::findOrFail($id);

            $this->orderService->postAdminMessage(
                order: $order,
                admin: Auth::user(),
                message: $request->message,
                attachment: $request->file('attachment')
            );

            notify()->success(__('Message sent successfully.'));
            return back();
        } catch (\Exception $exception) {
            notify()->error($exception->getMessage());
            return back()->withInput();
        }
    }
}
