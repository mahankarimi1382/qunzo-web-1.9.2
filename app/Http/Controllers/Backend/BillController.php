<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class BillController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('permission:pending-bills', ['only' => ['pending']]),
            new Middleware('permission:complete-bills', ['only' => ['complete']]),
            new Middleware('permission:return-bills', ['only' => ['returned']]),
            new Middleware('permission:all-bills', ['only' => ['all']]),
        ];
    }

    public function all(Request $request)
    {
        $bill = Bill::with(['service', 'user'])
            ->latest()
            ->paginate();

        $statusForFrontend = __('All');

        return view('backend.bill.history', compact('bill', 'statusForFrontend'));
    }

    public function pending(Request $request)
    {
        $bill = Bill::with(['service', 'user'])
            ->pending()
            ->latest()
            ->paginate();

        $statusForFrontend = __('Pending');

        return view('backend.bill.history', compact('bill', 'statusForFrontend'));
    }

    public function complete(Request $request)
    {
        $bill = Bill::with(['service', 'user'])
            ->completed()
            ->latest()
            ->paginate();

        $statusForFrontend = __('Completed');

        return view('backend.bill.history', compact('bill', 'statusForFrontend'));
    }

    public function returned(Request $request)
    {
        $bill = Bill::with(['service', 'user'])
            ->returned()
            ->latest()
            ->paginate();

        $statusForFrontend = __('Returned');

        return view('backend.bill.history', compact('bill', 'statusForFrontend'));
    }
}
