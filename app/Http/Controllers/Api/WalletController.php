<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\WalletResource;
use App\Models\Transaction;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WalletController extends Controller
{
    use ApiResponseTrait;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = request()->user();
        $wallets = $user->wallets->load('currency');

        return $this->success([
            'wallets' => (new WalletResource($wallets))->withDefaultWallet($request),
        ], __('Wallets'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'currency_id' => 'required|exists:currencies,id',
        ]);

        if ($validator->fails()) {
            return $this->validationToError($validator, 422);
        }

        $user = request()->user();

        // Check if wallet already exists for this currency
        if ($user->wallets()->where('currency_id', $request->currency_id)->exists()) {
            return $this->error(__('Wallet already exists for this currency'), 422);
        }

        $user->wallets()->create([
            'currency_id' => $request->currency_id,
        ]);

        return $this->success([
            'wallet' => new WalletResource($user->wallets()->latest()->first()),
        ], __('Wallet created successfully'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = request()->user();
        $wallet = $user->wallets()->findOrFail($id);

        if ($wallet->balance > 0) {
            return $this->error(__('You have a balance in your wallet. You cannot delete your wallet.'));
        }

        Transaction::where('user_id', $user->id)->where('wallet_type', (string) $wallet->id)->delete();

        $wallet->delete();

        return $this->successWithoutData(__('Wallet deleted successfully'));
    }
}
