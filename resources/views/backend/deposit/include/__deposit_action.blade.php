<h3 class="title mb-4">
    {{ __('Deposit Approval Action') }}
</h3>
<ul class="list-group mb-4">
    <li class="list-group-item">
        {{ $data?->user?->role->value . ' Name :' }}
        <strong>
            {{ $data?->user?->first_name . ' ' . $data?->user?->last_name }}
        </strong>
    </li>
    <li class="list-group-item">
        {{ __('Amount :') }} <strong>{{ formatAmount($data->amount, $data->currency, true) }}</strong>
    </li>
    <li class="list-group-item">
        {{ __('Charge :') }} <strong>{{ formatAmount($data->charge, $data->currency, true) }}</strong>
    </li>
    <li class="list-group-item">
        {{ __('Total amount :') }} <strong>
            {{ formatAmount($data->final_amount, $data->currency, true) }}</strong>
    </li>
    <li class="list-group-item">
        {{ __('Wallet :') }}
        <strong>
            @if ($data?->wallet_type == null || $data?->wallet_type == 'default')
                {{ __('Main Wallet') }}
            @else
                {{ $data?->userWallet?->currency?->name }}
            @endif
        </strong>
    </li>
</ul>

<ul class="list-group mb-4">
    @foreach ($data->manual_field_data as $key => $value)
        <li class="list-group-item">
            {{ $key }}:
            @if ($value != new stdClass())
                @if (file_exists(public_path($value)))
                    <a target="__blank" href="{{ asset($value) }}">
                        <img src="{{ asset($value) }}" alt="{{ $key }}" />
                    </a>
                @else
                    <strong>{{ $value }}</strong>
                @endif
            @endif
        </li>
    @endforeach
</ul>
<form action="{{ route('admin.deposit.action.now') }}" method="post">
    @csrf
    <input type="hidden" name="id" value="{{ $id }}">

    <div class="site-input-groups">
        <label for="" class="box-input-label">{{ __('Details Message(Optional)') }}</label>
        <textarea name="message" class="form-textarea mb-0" placeholder="{{ __('Details Message') }}"></textarea>
    </div>

    <div class="action-btns">
        <button type="submit" name="approve" value="yes" class="site-btn-sm primary-btn me-2">
            <i data-lucide="check"></i>
            {{ __('Approve') }}
        </button>
        <button type="submit" name="reject" value="yes" class="site-btn-sm red-btn">
            <i data-lucide="x"></i>
            {{ __('Reject') }}
        </button>
    </div>
</form>
<script>
    'use strict';
    lucide.createIcons();
</script>
