<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BillHistoryResource;
use App\Http\Resources\BillServiceResource;
use App\Models\Bill;
use App\Models\BillService as BillServiceModel;
use App\Services\BillService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class BillController extends Controller
{
    use ApiResponseTrait;

    public function history()
    {
        $bills = Bill::with('service')->latest()->paginate();

        return $this->success([
            'bills' => BillHistoryResource::collection($bills),
            'meta' => [
                'current_page' => $bills->currentPage(),
                'last_page' => $bills->lastPage(),
                'per_page' => $bills->perPage(),
                'total' => $bills->total(),
            ],
        ]);
    }

    public function getServices($country, $type)
    {
        $services = BillServiceModel::where('country', $country)->type($type)->get();

        return $this->success(BillServiceResource::collection($services));
    }

    public function payNow(Request $request)
    {
        try {
            $service = BillServiceModel::find($request->service_id);

            $billService = new BillService;

            $billService->validate($request);
            $billService->pay($request, $service);

            return $this->successWithoutData(__('Bill payment successful!'));
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }
}
