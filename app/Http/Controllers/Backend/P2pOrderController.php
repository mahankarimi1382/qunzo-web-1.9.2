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

class P2pOrderController extends Controller implements HasMiddleware
{
    public function __construct(
        protected OrderService $orderService
    ) {
        abort_if(! addonActive('p2p-trading'), 404);
    }

    public static function middleware()
    {
        return [
            new Middleware('permission:p2p-orders-manage', ['only' => ['index', 'show']]),
            new Middleware('permission:p2p-orders-resolve', ['only' => ['resolve']]),
        ];
    }

    public function index(Request $request)
    {
        $filters = array_filter([
            'status' => $request->status,
            'search' => $request->search,
        ]) + ['per_page' => $request->integer('per_page', 15)];

        $orders = $this->orderService->listOrders($filters);

        return view('backend.p2p.orders.index', [
            'orders' => $orders,
        ]);
    }

    public function show($id)
    {
        $order = $this->orderService->getOrder($id);
        $messages = $this->orderService->listOrderMessages($order);

        return view('backend.p2p.orders.show', [
            'order' => $order,
            'messages' => $messages,
        ]);
    }

    public function resolve(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'action' => 'required|in:release_to_buyer,refund_to_seller',
            'note' => 'nullable|string|max:2000',
        ]);

        if ($validator->fails()) {
            notify()->error($validator->errors()->first());
            return back()->withInput();
        }

        try {
            $order = AdsOrder::findOrFail($id);
            $admin = Auth::user();

            $this->orderService->resolveDispute(
                order: $order,
                admin: $admin,
                action: $request->action,
                note: $request->note
            );

            notify()->success(__('Order resolved successfully.'));
            return back();
        } catch (\Exception $exception) {
            notify()->error($exception->getMessage());
            return back()->withInput();
        }
    }
}
