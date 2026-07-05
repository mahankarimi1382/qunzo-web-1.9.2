@extends('backend.layouts.app')
@section('title')
    {{ __('P2P Orders') }}
@endsection
@section('content')
    <div class="main-content">
        <div class="page-title">
            <div class="container-fluid">
                <div class="row">
                    <div class="col">
                        <div class="title-content">
                            <h2 class="title">{{ __('P2P Orders') }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid">
            <div class="row">
                <div class="col-xl-12 col-md-12">
                    <div class="site-card">
                        <div class="site-card-body">
                            <div class="site-table table-responsive">
                                @include('backend.p2p.orders.include.__filter')

                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>{{ __('Order') }}</th>
                                            <th>{{ __('Date') }}</th>
                                            <th>{{ __('Buyer') }}</th>
                                            <th>{{ __('Seller') }}</th>
                                            <th>{{ __('Asset Amount') }}</th>
                                            <th>{{ __('Fiat Amount') }}</th>
                                            <th>{{ __('Status') }}</th>
                                            <th>{{ __('Action') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($orders as $order)
                                            <tr>
                                                <td>#{{ $order->id }}</td>
                                                <td>{{ $order->created_at->format('Y-m-d H:i:s') }}</td>
                                                <td>@include('backend.transaction.include.__user', [
                                                    'user' => $order->buyer,
                                                ])</td>
                                                <td>@include('backend.transaction.include.__user', [
                                                    'user' => $order->seller,
                                                ])</td>
                                                <td>{{ formatAmount($order->asset_amount, $order->assetCurrency, true) }}
                                                </td>
                                                <td>{{ formatAmount($order->fiat_amount, $order->fiatCurrency, true) }}
                                                </td>
                                                <td>
                                                    <span
                                                        class="site-badge {{ in_array($order->status->value, ['completed'], true) ? 'success' : (in_array($order->status->value, ['pending_payment', 'paid'], true) ? 'pending' : 'danger') }}">
                                                        {{ ucfirst(str_replace('_', ' ', $order->status->value)) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="{{ route('admin.p2p.orders.show', $order->id) }}"
                                                        class="round-icon-btn primary-btn"><i data-lucide="eye"></i></a>
                                                    @can('p2p-orders-chat-manage')
                                                        <a href="{{ route('admin.p2p.orders.messages.index', $order->id) }}"
                                                            class="round-icon-btn primary-btn"><i
                                                                data-lucide="messages-square"></i></a>
                                                    @endcan
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center">{{ __('No Data Found!') }}</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>

                                {{ $orders->links('backend.include.__pagination') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
