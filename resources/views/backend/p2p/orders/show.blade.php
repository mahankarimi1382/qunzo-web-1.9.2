@extends('backend.layouts.app')
@section('title')
    {{ __('P2P Order Details') }}
@endsection
@section('content')
    <div class="main-content">
        <div class="page-title">
            <div class="container-fluid">
                <div class="row">
                    <div class="col">
                        <div class="title-content">
                            <h2 class="title">{{ __('P2P Order') }} #{{ $order->id }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid">
            <div class="row">
                <div class="col-xl-12">
                    @php
                        $adPaymentMethod = $order->ad?->paymentMethods?->firstWhere(
                            'payment_method_id',
                            $order->payment_method_id,
                        );
                        $paymentAccountFields = collect($adPaymentMethod?->fields ?? [])
                            ->filter(function ($field) {
                                return !empty(data_get($field, 'value'));
                            })
                            ->values();
                    @endphp

                    <div class="site-card">
                        <div class="site-card-header">
                            <h3 class="title">{{ __('Order Summary') }}</h3>
                            @can('p2p-orders-chat-manage')
                                <div class="card-header-links">
                                    <a class="card-header-link"
                                        href="{{ route('admin.p2p.orders.messages.index', $order->id) }}">
                                        {{ __('Open Chat') }}
                                    </a>
                                </div>
                            @endcan
                        </div>
                        <div class="site-card-body">
                            <ul class="list-group mb-0">
                                <li class="list-group-item">
                                    {{ __('Order ID:') }}
                                    <strong>#{{ $order->id }}</strong>
                                </li>
                                <li class="list-group-item">
                                    {{ __('Ad ID:') }}
                                    <strong>#{{ $order->ads_id }}</strong>
                                </li>
                                <li class="list-group-item">
                                    {{ __('Ad Owner:') }}
                                    <strong>{{ $order->ad?->user?->username ?? '#' . $order->ad_owner_id }}</strong>
                                </li>
                                <li class="list-group-item">
                                    {{ __('Order Creator:') }}
                                    <strong>{{ $order->creator?->username ?? '#' . $order->order_creator_id }}</strong>
                                </li>
                                <li class="list-group-item">
                                    {{ __('Status:') }}
                                    <strong>{{ ucfirst(str_replace('_', ' ', $order->status->value)) }}</strong>
                                </li>
                                <li class="list-group-item">
                                    {{ __('Buyer:') }}
                                    <strong>{{ $order->buyer?->username ?? '-' }}</strong>
                                </li>
                                <li class="list-group-item">
                                    {{ __('Seller:') }}
                                    <strong>{{ $order->seller?->username ?? '-' }}</strong>
                                </li>
                                <li class="list-group-item">
                                    {{ __('Payment Method:') }}
                                    <strong>{{ $order->paymentMethod?->name ?? '-' }}</strong>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-xl-6">
                            <div class="site-card">
                                <div class="site-card-header">
                                    <h3 class="title">{{ __('Order Amount Details') }}</h3>
                                </div>
                                <div class="site-card-body">
                                    <ul class="list-group mb-0">
                                        <li class="list-group-item">
                                            {{ __('Asset Amount:') }}
                                            <strong>{{ formatAmount($order->asset_amount, $order->assetCurrency, true) }}</strong>
                                        </li>
                                        <li class="list-group-item">
                                            {{ __('Fiat Amount:') }}
                                            <strong>{{ formatAmount($order->fiat_amount, $order->fiatCurrency, true) }}</strong>
                                        </li>
                                        <li class="list-group-item">
                                            {{ __('Price Rate:') }}
                                            <strong>{{ formatAmount($order->price, $order->fiatCurrency, true) }}</strong>
                                        </li>
                                        <li class="list-group-item">
                                            {{ __('Locked:') }}
                                            <strong>{{ formatAmount($order->seller_locked_asset, $order->assetCurrency, true) }}</strong>
                                        </li>
                                        <li class="list-group-item">
                                            {{ __('Fee Percent:') }}
                                            <strong>{{ rtrim(rtrim(number_format($order->fee_percent, 4, '.', ''), '0'), '.') }}%</strong>
                                        </li>
                                        <li class="list-group-item">
                                            {{ __('Fee Amount:') }}
                                            <strong>{{ formatAmount($order->fee_asset_amount, $order->assetCurrency, true) }}</strong>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-6">
                            <div class="site-card">
                                <div class="site-card-header">
                                    <h3 class="title">{{ __('Timeline') }}</h3>
                                </div>
                                <div class="site-card-body">
                                    <ul class="list-group mb-0">
                                        <li class="list-group-item">
                                            {{ __('Created At:') }}
                                            <strong>{{ $order->created_at?->format('d M Y, h:i A') ?? '-' }}</strong>
                                        </li>
                                        <li class="list-group-item">
                                            {{ __('Updated At:') }}
                                            <strong>{{ $order->updated_at?->format('d M Y, h:i A') ?? '-' }}</strong>
                                        </li>
                                        <li class="list-group-item">
                                            {{ __('Deadline:') }}
                                            <strong>{{ $order->payment_deadline_at?->format('d M Y, h:i A') ?? '-' }}</strong>
                                        </li>
                                        <li class="list-group-item">
                                            {{ __('Marked Paid At:') }}
                                            <strong>{{ $order->marked_paid_at?->format('d M Y, h:i A') ?? '-' }}</strong>
                                        </li>
                                        <li class="list-group-item">
                                            {{ __('Completed At:') }}
                                            <strong>{{ $order->completed_at?->format('d M Y, h:i A') ?? '-' }}</strong>
                                        </li>
                                        <li class="list-group-item">
                                            {{ __('Cancelled At:') }}
                                            <strong>{{ $order->cancelled_at?->format('d M Y, h:i A') ?? '-' }}</strong>
                                        </li>
                                        <li class="list-group-item">
                                            {{ __('Expired At:') }}
                                            <strong>{{ $order->expired_at?->format('d M Y, h:i A') ?? '-' }}</strong>
                                        </li>
                                        <li class="list-group-item">
                                            {{ __('Disputed At:') }}
                                            <strong>{{ $order->disputed_at?->format('d M Y, h:i A') ?? '-' }}</strong>
                                        </li>
                                        <li class="list-group-item">
                                            {{ __('Resolved At:') }}
                                            <strong>{{ $order->resolved_at?->format('d M Y, h:i A') ?? '-' }}</strong>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        @if ($paymentAccountFields->isNotEmpty())
                            <div class="col-xl-6">
                                <div class="site-card">
                                    <div class="site-card-header">
                                        <h3 class="title">{{ __('Seller Payment Account Details') }}</h3>
                                    </div>
                                    <div class="site-card-body">
                                        <ul class="list-group mb-0">
                                            @foreach ($paymentAccountFields as $field)
                                                @php
                                                    $label = data_get(
                                                        $field,
                                                        'field_label',
                                                        data_get($field, 'name', 'Field'),
                                                    );
                                                    $value = data_get($field, 'value');
                                                    $type = data_get($field, 'type');
                                                @endphp
                                                <li class="list-group-item">
                                                    <strong>{{ $label }}:</strong>
                                                    @if ($type === 'file' && is_string($value) && file_exists(public_path($value)))
                                                        <a href="{{ asset($value) }}"
                                                            target="_blank">{{ __('View File') }}</a>
                                                    @else
                                                        {{ is_array($value) ? json_encode($value) : $value }}
                                                    @endif
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="col-xl-6">
                            <div class="site-card">
                                <div class="site-card-header">
                                    <h3 class="title">{{ __('Dispute & Resolution') }}</h3>
                                </div>
                                <div class="site-card-body">
                                    <ul class="list-group mb-0">
                                        <li class="list-group-item">
                                            {{ __('Dispute Reason:') }}
                                            <strong>{{ $order->dispute_reason ?: '-' }}</strong>
                                        </li>
                                        <li class="list-group-item">
                                            {{ __('Resolution Note:') }}
                                            <strong>{{ $order->resolution_note ?: '-' }}</strong>
                                        </li>
                                        <li class="list-group-item">
                                            {{ __('Resolved By Admin:') }}
                                            <strong>{{ $order->resolver?->name ?? ($order->resolved_by_admin_id ? '#' . $order->resolved_by_admin_id : '-') }}</strong>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    @can('p2p-orders-resolve')
                        @if (in_array($order->status->value, ['paid', 'disputed'], true))
                            <div class="site-card mt-4">
                                <div class="site-card-header">
                                    <h3 class="title">{{ __('Resolve Order') }}</h3>
                                </div>
                                <div class="site-card-body">
                                    <form action="{{ route('admin.p2p.orders.resolve', $order->id) }}" method="POST">
                                        @csrf
                                        <ul class="list-group mb-4">
                                            <li class="list-group-item">
                                                {{ __('Order ID:') }} <strong>#{{ $order->id }}</strong>
                                            </li>
                                            <li class="list-group-item">
                                                {{ __('Status:') }}
                                                <strong>{{ ucfirst(str_replace('_', ' ', $order->status->value)) }}</strong>
                                            </li>
                                            <li class="list-group-item">
                                                {{ __('Buyer:') }} <strong>{{ $order->buyer?->username ?? '-' }}</strong>
                                            </li>
                                            <li class="list-group-item">
                                                {{ __('Seller:') }} <strong>{{ $order->seller?->username ?? '-' }}</strong>
                                            </li>
                                            <li class="list-group-item">
                                                {{ __('Asset Amount:') }}
                                                <strong>{{ formatAmount($order->asset_amount, $order->assetCurrency, true) }}</strong>
                                            </li>
                                            <li class="list-group-item">
                                                {{ __('Fiat Amount:') }}
                                                <strong>{{ formatAmount($order->fiat_amount, $order->fiatCurrency, true) }}</strong>
                                            </li>
                                        </ul>

                                        <div class="site-input-groups">
                                            <label class="box-input-label">{{ __('Details Message(Optional)') }}</label>
                                            <textarea name="note" class="form-textarea mb-0" placeholder="{{ __('Details Message') }}">{{ old('note') }}</textarea>
                                        </div>

                                        <div class="action-btns">
                                            <button type="submit" name="action" value="release_to_buyer"
                                                class="site-btn-sm primary-btn me-2">
                                                <i data-lucide="check"></i>
                                                {{ __('Release To Buyer') }}
                                            </button>
                                            <button type="submit" name="action" value="refund_to_seller"
                                                class="site-btn-sm red-btn">
                                                <i data-lucide="x"></i>
                                                {{ __('Refund To Seller') }}
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @endif
                    @endcan

                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        'use strict';
        lucide.createIcons();
    </script>
@endsection
