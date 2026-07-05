@extends('backend.layouts.app')
@section('title')
    {{ __(':status Merchants', ['status' => ucfirst(request('status'))]) }}
@endsection
@section('content')
    <div class="main-content">
        <div class="page-title">
            <div class="container-fluid">
                <div class="row">
                    <div class="col">
                        <div class="title-content">
                            <h2 class="title">@yield('title')</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="container-fluid">
            <div class="row">
                <div class="col-xl-12">
                    <div class="site-table table-responsive">
                        <form action="{{ request()->url() }}" method="get">
                            <div class="table-filter">
                                <div class="filter">
                                    <div class="search">
                                        <input type="text" id="search" name="query" value="{{ request('query') }}"
                                            placeholder="{{ __('Search') }}" />
                                    </div>
                                    <select name="status" id="status" class="form-select form-select-sm">
                                        <option value="">{{ __('All Status') }}</option>
                                        @foreach (App\Enums\MerchantStatus::cases() as $status)
                                            <option value="{{ $status->value }}"
                                                @if ($status->value == request('status')) selected @endif>
                                                {{ $status->name }}</option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="apply-btn">
                                        <i data-lucide="search"></i>{{ __('Search') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>{{ __('Avatar') }}</th>
                                    @include('backend.filter.th', [
                                        'label' => 'User',
                                        'field' => 'username',
                                    ])
                                    @include('backend.filter.th', ['label' => 'Email', 'field' => 'email'])
                                    @include('backend.filter.th', [
                                        'label' => 'Main Balance',
                                        'field' => 'balance',
                                    ])
                                    @include('backend.filter.th', [
                                        'label' => 'Status',
                                        'field' => 'status',
                                    ])
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($merchants as $merchant)
                                    <tr>
                                        <td>
                                            @if (null != $merchant->user?->avatar)
                                                <img class="avatar avatar-round"
                                                    src="{{ asset($merchant->user?->avatar) }}" alt=""
                                                    height="40" width="40">
                                            @else
                                                <span class="avatar-text">
                                                    {{ getShortName($merchant->user?->full_name) }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.merchant.edit', $merchant->id) }}"
                                                class="link">{{ Str::limit($merchant->user?->username, 15) }}</a>
                                        </td>
                                        <td>{{ config('app.demo') ? safe($merchant->user?->email) : Str::limit($merchant->user?->email, 25) }}
                                        </td>
                                        <td>
                                            {{ formatAmount($merchant->user?->balance, $currency, true) }}
                                        </td>
                                        <td>
                                            @if ($merchant->status == App\Enums\MerchantStatus::Approved)
                                                <div class="site-badge success">{{ __('Active') }}</div>
                                            @elseif($merchant->status == App\Enums\MerchantStatus::Disabled)
                                                <div class="site-badge danger">{{ __('Disabled') }}</div>
                                            @elseif($merchant->status == App\Enums\MerchantStatus::Rejected)
                                                <div class="site-badge danger">{{ __('Rejected') }}</div>
                                            @elseif($merchant->status == App\Enums\MerchantStatus::Pending)
                                                <div class="site-badge pending">{{ __('Pending') }}</div>
                                            @endif
                                        </td>
                                        <td>
                                            @can('merchant-send-mail')
                                                <button type="button" data-id="{{ $merchant->id }}"
                                                    data-name="{{ $merchant->user->full_name }}"
                                                    class="send-mail round-icon-btn blue-btn" data-bs-toggle="tooltip"
                                                    data-bs-original-title="{{ __('Send Email') }}">
                                                    <i data-lucide="mail"></i>
                                                </button>
                                            @endcan

                                            @canany(['merchant-basic-manage', 'merchant-balance-add-or-subtract',
                                                'merchant-change-password', 'merchant-all-type-status'])
                                                <a href="{{ route('admin.merchant.edit', $merchant->id) }}"
                                                    class="round-icon-btn primary-btn" data-bs-toggle="tooltip"
                                                    data-bs-original-title="{{ __('Edit Merchant') }}"><i
                                                        data-lucide="edit-3"></i>
                                                </a>
                                            @endcan
                                            @can(['merchant-delete'])
                                                <button type="button" class="round-icon-btn red-btn" id="deleteModal"
                                                    data-id="{{ $merchant->user_id }}" data-bs-toggle="tooltip"
                                                    data-bs-original-title="{{ __('Delete Merchant') }}">
                                                    <i data-lucide="trash-2"></i>
                                                </button>
                                            @endcan
                                        </td>
                                    </tr>
                                @empty
                                    <td colspan="8" class="text-center">{{ __('No Data Found!') }}</td>
                                @endforelse
                            </tbody>
                        </table>
                        {{ $merchants->links('backend.include.__pagination') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    @can('merchant-send-mail')
        @include('backend.user.include.__mail_send')
    @endcan
    @can('merchant-delete')
        @include('backend.user.include.__delete_popup')
    @endcan
@endsection

@section('script')
    <script>
        (function($) {
            "use strict";

            //send mail modal form open
            $('body').on('click', '.send-mail', function() {
                var id = $(this).data('id');
                var name = $(this).data('name');
                $('#name').html(name);
                $('#userId').val(id);
                $('#sendEmail').modal('toggle')
            })

            // Delete
            $('body').on('click', '#deleteModal', function() {
                var id = $(this).data('id');
                var name = $(this).data('name');

                $('#data-name').html(name);
                var url = '{{ route('admin.user.destroy', ':id') }}';
                url = url.replace(':id', id);
                $('#deleteForm').attr('action', url);
                $('#delete').modal('toggle')
            });

        })(jQuery);
    </script>
@endsection
