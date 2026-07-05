<h3 class="title mb-4">
    {{ __('P2P Ad Approval Action') }}
</h3>
<ul class="list-group mb-4">
    <li class="list-group-item">
        {{ __('User Name :') }}
        <strong>
            {{ $ad->user->first_name . ' ' . $ad->user->last_name }}
        </strong>
    </li>
    <li class="list-group-item">
        {{ __('Username :') }}
        <strong>{{ $ad->user->username }}</strong>
    </li>
    <li class="list-group-item">
        {{ __('Email :') }}
        <strong>{{ $ad->user->email }}</strong>
    </li>
    <li class="list-group-item">
        {{ __('Ad Type :') }}
        <strong>
            <span class="site-badge {{ $ad->type->value == 'buy' ? 'success' : 'danger' }}">
                {{ ucfirst($ad->type->value) }}
            </span>
        </strong>
    </li>
    <li class="list-group-item">
        {{ __('Asset Currency :') }}
        <strong>{{ $ad->assetCurrency->name }} ({{ $ad->assetCurrency->code }})</strong>
    </li>
    <li class="list-group-item">
        {{ __('Fiat Currency :') }}
        <strong>{{ $ad->fiatCurrency->name }} ({{ $ad->fiatCurrency->code }})</strong>
    </li>
    <li class="list-group-item">
        {{ __('Price :') }}
        <strong>{{ formatAmount($ad->price, $ad->fiatCurrency->code, true) }}</strong>
    </li>
    <li class="list-group-item">
        {{ __('Min Amount :') }}
        <strong>{{ formatAmount($ad->min_amount, $ad->assetCurrency->code, true) }}</strong>
    </li>
    <li class="list-group-item">
        {{ __('Max Amount :') }}
        <strong>{{ formatAmount($ad->max_amount, $ad->assetCurrency->code, true) }}</strong>
    </li>
    <li class="list-group-item">
        {{ __('Payment Duration :') }}
        <strong>{{ $ad->payment_duration }} {{ __('minutes') }}</strong>
    </li>
    <li class="list-group-item">
        {{ __('Description :') }}
        <div class="mt-2">{{ $ad->description }}</div>
    </li>
    @if ($ad->auto_response_message)
        <li class="list-group-item">
            {{ __('Auto Response Message :') }}
            <div class="mt-2">{{ $ad->auto_response_message }}</div>
        </li>
    @endif
    <li class="list-group-item">
        {{ __('Payment Methods :') }}
        <div class="mt-2">
            <strong>{{ $ad->paymentMethods->pluck('paymentMethod.name')->implode(',') }}</strong>
        </div>
    </li>
</ul>

<form action="{{ route('admin.p2p.ads.action.now') }}" method="post">
    @csrf
    <input type="hidden" name="id" value="{{ $id }}">

    <div class="site-input-groups">
        <label for="" class="box-input-label">{{ __('Details Message (Optional)') }}</label>
        <textarea name="message" class="form-textarea mb-0" placeholder="{{ __('Details Message') }}"></textarea>
    </div>

    <div class="action-btns">
        <button type="submit" name="approve" value="1" class="site-btn-sm primary-btn me-2">
            <i data-lucide="check"></i>
            {{ __('Approve') }}
        </button>
        <button type="submit" name="reject" value="1" class="site-btn-sm red-btn">
            <i data-lucide="x"></i>
            {{ __('Reject') }}
        </button>
    </div>

</form>
