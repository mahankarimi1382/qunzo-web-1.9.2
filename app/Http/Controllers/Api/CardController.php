<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Card;
use App\Models\CardHolder;
use App\Services\CardService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CardController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        private CardService $cardService
    ) {}

    public function index()
    {
        $cards = Card::currentUser()->with('cardHolder')->latest()->get();

        return $this->success($cards, __('Cards'));
    }

    public function store(Request $request)
    {
        try {

            $this->cardService->validate($request);

            $this->cardService->process($request);

            return $this->successWithoutData(__('Card created successfully'));
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function show(string $id)
    {
        $card = Card::with('cardHolder')->currentUser()->findOrFail($id);

        return $this->success($card, __('Card details'));
    }

    public function updateStatus($card_id)
    {
        try {
            $card = Card::currentUser()->findOrFail($card_id);

            $this->cardService->updateStatus($card);

            return $this->successWithoutData(__('Card status updated successfully'));
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function topupBalance(Request $request, $id)
    {
        try {
            $card = Card::currentUser()->findOrFail($id);

            $this->cardService->addCardBalance($card, $request);

            return $this->successWithoutData(__('Card balance added successfully'));
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function transactions(Request $request, $card_id)
    {
        // Check cache first
        $cacheKey = 'card_transactions_'.$card_id;

        // Forget cache if sync is requested
        if ($request->boolean('sync') && Cache::has($cacheKey)) {
            Cache::forget($cacheKey);
        }

        if (Cache::has($cacheKey)) {
            $transactions = Cache::get($cacheKey);
        } else {
            // Get transactions from service
            $transactions = $this->cardService->transactions($card_id);

            // Cache transactions for 1 hour if they exist
            if ($transactions && count($transactions)) {
                Cache::put($cacheKey, $transactions, now()->addHour());
            }
        }

        return $this->success($transactions, __('Card transactions'));
    }

    public function cardholders()
    {
        $cardholders = CardHolder::currentUser()->get();

        return $this->success($cardholders, __('Cardholders'));
    }
}
