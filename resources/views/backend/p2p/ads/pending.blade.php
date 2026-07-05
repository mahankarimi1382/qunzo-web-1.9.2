@extends('backend.layouts.app')
@section('title')
    {{ __('Pending P2P Ads') }}
@endsection
@section('content')
    <div class="main-content">
        <div class="page-title">
            <div class="container-fluid">
                <div class="row">
                    <div class="col">
                        <div class="title-content">
                            <h2 class="title">{{ __('Pending P2P Ads') }}</h2>
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
                                @include('backend.p2p.ads.include.__filter', ['showType' => false, 'showPerPage' => true])
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>{{ __('Date') }}</th>
                                            <th>{{ __('User') }}</th>
                                            <th>{{ __('Type') }}</th>
                                            <th>{{ __('Asset') }}</th>
                                            <th>{{ __('Fiat') }}</th>
                                            <th>{{ __('Price') }}</th>
                                            <th>{{ __('Total Amount') }}</th>
                                            <th>{{ __('Min/Max') }}</th>
                                            <th>{{ __('Status') }}</th>
                                            <th>{{ __('Action') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($ads as $ad)
                                            <tr>
                                                <td>{{ $ad->created_at->format('Y-m-d H:i:s') }}</td>
                                                <td>
                                                    @include('backend.transaction.include.__user', [
                                                        'user' => $ad->user
                                                    ])
                                                </td>
                                                <td>
                                                    <span class="site-badge {{ $ad->type->value == 'buy' ? 'success' : 'danger' }}">
                                                        {{ ucfirst($ad->type->value) }}
                                                    </span>
                                                </td>
                                                <td>{{ $ad->assetCurrency->code }}</td>
                                                <td>{{ $ad->fiatCurrency->code }}</td>
                                                <td>{{ formatAmount($ad->price, $ad->fiatCurrency->code, true) }}</td>
                                                <td>
                                                    @php
                                                        $totalDisplay = $ad->type->value === 'sell'
                                                            ? (float) ($ad->total_amount ?? $ad->max_amount) * (float) $ad->price
                                                            : (float) ($ad->total_amount ?? $ad->max_amount);
                                                        $totalCurrency = $ad->type->value === 'sell' ? $ad->fiatCurrency : $ad->assetCurrency;
                                                    @endphp
                                                    {{ formatAmount($totalDisplay, $totalCurrency->code, true) }}
                                                </td>
                                                <td>
                                                    {{ formatAmount($ad->min_amount, $ad->assetCurrency->code, true) }} /
                                                    {{ formatAmount($ad->max_amount, $ad->assetCurrency->code, true) }}
                                                </td>
                                                <td>
                                                    <span class="site-badge pending">{{ ucfirst($ad->status->value) }}</span>
                                                </td>
                                                <td>
                                                    @can('p2p-ads-approve')
                                                        @include('backend.p2p.ads.include.__action', ['id' => $ad->id])
                                                    @endcan
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="10" class="text-center">{{ __('No Data Found!') }}</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>

                                {{ $ads->links('backend.include.__pagination') }}
                            </div>
                        </div>
                    </div>
                    @can('p2p-ads-approve')
                        <div class="modal fade" id="ads-action-modal" tabindex="-1" aria-labelledby="adsActionModalLabel"
                            aria-hidden="true">
                            <div class="modal-dialog modal-md modal-dialog-centered">
                                <div class="modal-content site-table-modal">
                                    <div class="modal-body popup-body">
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        <div class="popup-body-text ads-action">

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endcan
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        (function($) {
            "use strict";

            let loader =
                '<div class="text-center"><i data-lucide="loader-circle" class=" spining-icon"> </i> {{ __('Please wait..') }}</div>';

            $('body').on('click', '#ads-action', function() {
                $('.ads-action').html(loader);

                lucide.createIcons();
                var id = $(this).data('id');
                var url = "{{ route('admin.p2p.ads.action', ':id') }}".replace(':id', id);

                $.get(url, function(data) {
                    $('.ads-action').html(data);
                    lucide.createIcons();
                });
            });
        })(jQuery);
    </script>
@endsection
