<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Beneficiary\StoreBeneficiaryRequest;
use App\Http\Requests\Beneficiary\UpdateBeneficiaryRequest;
use App\Http\Resources\BeneficiaryResource;
use App\Services\BeneficiaryService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class BeneficiaryController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        private BeneficiaryService $beneficiaryService
    ) {}

    public function index(Request $request)
    {
        $filters = [
            'keyword' => $request->input('keyword'),
            'type' => $request->input('type'),
        ];

        $beneficiaries = $this->beneficiaryService->getBeneficiaries(auth()->id(), $filters);

        return $this->success([
            'beneficiaries' => BeneficiaryResource::collection($beneficiaries),
        ], __('Beneficiaries list fetched successfully.'));
    }

    public function store(StoreBeneficiaryRequest $request)
    {
        $validated = $request->validated();
        $user = auth()->user();

        $receiver = $this->beneficiaryService->validateReceiver($user, $validated['account_number']);
        if (is_array($receiver)) {
            return $this->error($receiver['error'], $receiver['code']);
        }

        if ($this->beneficiaryService->existsDuplicate($user->id, $receiver->id)) {
            return $this->error(__('This beneficiary already exists.'), 422);
        }

        $beneficiary = $this->beneficiaryService->create($user, $receiver, $validated['nickname'] ?? null);
        if (! $beneficiary) {
            return $this->error(__('Beneficiary couldn\'t be created'), 422);
        }

        return $this->success(
            new BeneficiaryResource($beneficiary->load('receiver')),
            __('Beneficiary added successfully!')
        );
    }

    public function show($id)
    {
        $beneficiary = $this->beneficiaryService->getUserBeneficiary($id, auth()->id());
        if (! $beneficiary) {
            return $this->error(__('Beneficiary not found'), 404);
        }

        return $this->success(new BeneficiaryResource($beneficiary), __('Beneficiary details'));
    }

    public function update(UpdateBeneficiaryRequest $request, $id)
    {
        $validated = $request->validated();
        $beneficiary = $this->beneficiaryService->getUserBeneficiary($id, auth()->id());
        if (! $beneficiary) {
            return $this->error(__('Beneficiary not found'), 404);
        }

        $updated = $this->beneficiaryService->updateNickname($beneficiary, $validated['nickname']);
        if (! $updated) {
            return $this->error(__('Beneficiary couldn\'t be updated'), 422);
        }

        return $this->success(
            new BeneficiaryResource($updated->load('receiver')),
            __('Beneficiary updated successfully!')
        );
    }

    public function destroy($id)
    {
        $beneficiary = $this->beneficiaryService->getUserBeneficiary($id, auth()->id());
        if (! $beneficiary) {
            return $this->error(__('Beneficiary not found'), 404);
        }

        $beneficiary->delete();

        return $this->success([], __('Beneficiary deleted successfully.'));
    }
}
