@extends('backend.layouts.app')

@section('title')
    {{ __('Payment Methods') }}
@endsection

@section('content')
    <div class="main-content">
        <div class="container-fluid">
            <div class="page-title">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col">
                            <div class="title-content">
                                <h2 class="title">{{ __('Payment Methods') }}</h2>
                                <div>
                                    <a href="{{ route('admin.p2p.payment-method.create') }}" class="title-btn">
                                        <i icon-name="plus-circle"></i>
                                        {{ __('Add New') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xl-12">
                    <div class="site-card">
                        <div class="site-card-body table-responsive">
                            <div class="site-datatable">
                                <table id="dataTable" class="display data-table">
                                    <thead>
                                        <tr>
                                            <th>{{ __('SL No') }}</th>
                                            <th>{{ __('Name') }}</th>
                                            <th>{{ __('Currency') }}</th>
                                            <th>{{ __('Action') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($paymentMethods as $key => $paymentMethod)
                                            <tr>
                                                <td>{{ $key + $paymentMethods->firstItem() }}</td>
                                                <td>{{ $paymentMethod->name }}</td>
                                                <td>{{ $paymentMethod->currency?->name ?? __('N/A') }}
                                                    ({{ $paymentMethod->currency?->code ?? '-' }})</td>
                                                <td>
                                                    <a href="{{ route('admin.p2p.payment-method.edit', $paymentMethod->id) }}"
                                                        class="round-icon-btn primary-btn" data-bs-toggle="tooltip"
                                                        title="{{ __('Edit') }}"
                                                        data-bs-original-title="{{ __('Edit') }}">
                                                        <i data-lucide="edit-3"></i>
                                                    </a>
                                                    <span type="button">
                                                        <button class="round-icon-btn red-btn deleteData"
                                                            data-bs-toggle="tooltip"
                                                            data-routeurl="{{ route('admin.p2p.payment-method.delete', $paymentMethod->id) }}"
                                                            title="{{ __('Delete') }}"
                                                            data-bs-original-title="{{ __('Delete') }}">
                                                            <i data-lucide="trash-2"></i>
                                                        </button>
                                                    </span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center">{{ __('No Payment Method Found!') }}
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            @if ($paymentMethods->hasPages())
                                <div class="mt-3">
                                    {{ $paymentMethods->links() }}
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="modal fade" id="deletePaymentMethod" tabindex="-1"
                        aria-labelledby="deletePaymentMethodModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-md modal-dialog-centered">
                            <div class="modal-content site-table-modal">
                                <div class="modal-body popup-body">
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                    <div class="popup-body-text centered">
                                        <div class="info-icon">
                                            <i icon-name="alert-triangle"></i>
                                        </div>
                                        <div class="title">
                                            <h4>{{ __('Are you sure?') }}</h4>
                                        </div>
                                        <p>
                                            {{ __('You want to delete this payment method') }}
                                        </p>
                                        <div class="action-btns">
                                            <form id="deletePaymentMethodForm" method="post">
                                                @csrf
                                                <button type="submit" class="site-btn-sm primary-btn me-2">
                                                    <i icon-name="check"></i>
                                                    {{ __('Confirm') }}
                                                </button>
                                                <a href="javascript:void(0)" class="site-btn-sm red-btn" type="button"
                                                    data-bs-dismiss="modal" aria-label="Close">
                                                    <i icon-name="x"></i>
                                                    {{ __('Cancel') }}
                                                </a>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        "use strict";
        $(".deleteData").on('click', function() {
            var routeurl = $(this).data('routeurl');
            $('#deletePaymentMethodForm').attr('action', routeurl);
            $("#deletePaymentMethod").modal('show');
        });
    </script>
@endsection
