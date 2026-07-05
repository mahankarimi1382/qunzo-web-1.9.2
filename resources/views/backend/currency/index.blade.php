@extends('backend.setting.index')

@section('title')
    {{ __('Currencies') }}
@endsection
@section('setting-title')
    {{ __('Currencies') }}
@endsection

@section('setting-content')
    <div class="main-content pt-0">
        <div class="page-title pt-0">
            <div class="row">
                <div class="col">
                    <div class="title-content">
                        <h2 class="title"></h2>
                        <div>
                            <a href="{{ route('admin.currency.create') }}" class="title-btn">
                                <i icon-name="plus-circle"></i>
                                {{ __('Add New') }}
                            </a>
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
                                        <th>{{ __('Icon') }}</th>
                                        <th>{{ __('Name') }}</th>
                                        <th>{{ __('Type') }}</th>
                                        <th>{{ __('Code') }}</th>
                                        <th>{{ __('Symbol') }}</th>
                                        <th>{{ __('Conversion Rate') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th>{{ __('Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($currencies as $key => $currencyInfo)
                                        <tr>
                                            <td>{{ $key + $currencies->firstItem() }}</td>
                                            <td>
                                                @if ($currencyInfo->icon !== null)
                                                    <img src="{{ asset($currencyInfo->icon) }}"
                                                        alt="{{ $currencyInfo->name }}" width="50" height="50">
                                                @else
                                                    {{ __('N/A') }}
                                                @endif
                                            </td>
                                            <td>{{ $currencyInfo->name }}</td>
                                            <td>{{ ucfirst($currencyInfo->type->value) }}</td>
                                            <td>{{ $currencyInfo->code }}</td>
                                            <td>{{ $currencyInfo->symbol }}</td>
                                            <td>
                                                {{ '1 ' . $currency . ' = ' . formatAmount($currencyInfo->conversion_rate, $currencyInfo, true) }}
                                            </td>
                                            <td>
                                                @if ($currencyInfo->status == App\Enums\CurrencyStatus::Active)
                                                    <div class="site-badge success">{{ __('Active') }}</div>
                                                @else
                                                    <div class="site-badge pending">{{ __('Inactive') }}</div>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.currency.edit', $currencyInfo->id) }}"
                                                    class="round-icon-btn primary-btn" data-bs-toggle="tooltip"
                                                    title="{{ __('Edit Currency') }}"
                                                    data-bs-original-title="{{ __('Edit Currency') }}">
                                                    <i data-lucide="edit-3"></i>
                                                </a>
                                                <span type="button" id="deleteModal">
                                                    <button class="round-icon-btn red-btn deleteData"
                                                        data-bs-toggle="tooltip"
                                                        data-routeUrl="{{ route('admin.currency.delete', $currencyInfo->id) }}"
                                                        title="{{ __('Delete Currency') }}"
                                                        data-bs-original-title="{{ __('Delete Currency') }}">
                                                        <i data-lucide="trash-2"></i>
                                                    </button>
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <td colspan="9" class="text-center">{{ __('No Currency Found!') }}</td>
                                    @endforelse
                                </tbody>
                            </table>
                            {{ $currencies->links('backend.include.__pagination') }}
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="deleteCurrency" tabindex="-1" aria-labelledby="deleteCurrencyModalLabel"
                    aria-hidden="true">
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
                                        {{ __('You want to delete this currency') }}
                                    </p>
                                    <div class="action-btns">
                                        <form id="deleteCurrencyForm" method="post">
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
@endsection

@section('script')
    <script>
        "use strict";
        $(".deleteData").on('click', function() {
            var routeurl = $(this).data('routeurl');
            $('#deleteCurrencyForm').attr('action', routeurl);
            $("#deleteCurrency").modal('show');
        });
    </script>
@endsection
