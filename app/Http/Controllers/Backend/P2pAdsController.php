<?php

namespace App\Http\Controllers\Backend;

use Addons\P2PTrading\Models\Ads;
use Addons\P2PTrading\Services\P2pAdsService;
use App\Http\Controllers\Controller;
use App\Traits\NotifyTrait;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Validator;

class P2pAdsController extends Controller implements HasMiddleware
{
    use NotifyTrait;

    public function __construct(
        protected P2pAdsService $p2pAdsService
    ) {
        abort_if(! addonActive('p2p-trading'), 404);
    }

    public static function middleware()
    {
        return [
            new Middleware('permission:p2p-ads-manage', ['only' => ['index', 'pending']]),
            new Middleware('permission:p2p-ads-approve', ['only' => ['action', 'actionNow']]),
        ];
    }

    public function index(Request $request)
    {
        $filters = array_filter([
            'status' => $request->status,
            'type' => $request->type,
            'fiat_currency' => $request->fiat_currency,
            'asset_currency' => $request->asset_currency,
            'search' => $request->search,
        ]) + ['per_page' => $request->integer('per_page', 15)];

        $ads = $this->p2pAdsService->listAds($filters);

        return view('backend.p2p.ads.index', [
            'ads' => $ads,
        ]);
    }

    public function pending(Request $request)
    {
        $filters = array_filter([
            'search' => $request->search,
        ]) + ['per_page' => $request->integer('per_page', 15)];

        $ads = $this->p2pAdsService->listPendingAds($filters);

        return view('backend.p2p.ads.pending', [
            'ads' => $ads,
        ]);
    }

    public function action($id)
    {
        $ad = $this->p2pAdsService->getAdForApproval($id);

        return view('backend.p2p.ads.include.__ads_action', [
            'ad' => $ad,
            'id' => $id,
        ])->render();
    }

    public function actionNow(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:p2p_ads,id',
            'approve' => 'nullable|boolean',
            'reject' => 'nullable|boolean',
            'message' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            notify()->error($validator->errors()->first());
            return back();
        }

        try {
            $ad = Ads::findOrFail($request->id);

            if ($request->has('approve') && $request->approve) {
                $result = $this->p2pAdsService->approveAd($ad, $request->message);

                $this->sendNotify(
                    $result['ad']->user->email,
                    $result['template'],
                    'User',
                    $result['shortcodes'],
                    $result['ad']->user->phone,
                    $result['ad']->user->id,
                    ''
                );

                notify()->success(__('Ad approved successfully!'));
            } elseif ($request->has('reject') && $request->reject) {
                $result = $this->p2pAdsService->rejectAd($ad, $request->message);

                $this->sendNotify(
                    $result['ad']->user->email,
                    $result['template'],
                    'User',
                    $result['shortcodes'],
                    $result['ad']->user->phone,
                    $result['ad']->user->id,
                    ''
                );

                notify()->success(__('Ad rejected successfully!'));
            } else {
                notify()->error(__('Invalid action!'));
                return back();
            }

            return back();
        } catch (\Exception $e) {
            notify()->error(__('Sorry! Something went wrong.'));
            return back();
        }
    }
}
