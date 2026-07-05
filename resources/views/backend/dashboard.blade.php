@php use App\Enums\InvestStatus; @endphp
@extends('backend.layouts.app')

@section('title')
    {{ __('Admin Dashboard') }}
@endsection

@push('style')
    <style>
        .filter-select {
            border: 0px;
            box-shadow: 0px 0px 2px rgba(94, 63, 201, 0.4);
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            padding: 2px 15px;
            color: #080039;
            height: 26px;
            display: flex;
            outline: none;
        }
    </style>
@endpush

@section('content')
    <div class="main-content">
        <div class="page-title">
            <div class="container-fluid">
                <div class="row">
                    <div class="col">
                        <div class="title-content">
                            <h2 class="title">{{ setting('site_title', 'global') }} {{ __('Dashboard') }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="container-fluid">

            <div class="row">
                @include('backend.include.__action')
                @include('backend.include.__data_card')
                @can('site-statistics-chart')
                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                        <div class="site-chart">
                            <div class="site-card">
                                <div class="site-card-header">
                                    <h3 class="title">{{ __('App Statistics') }}</h3>
                                    <div class="card-header-links d-flex align-items-center flex-wrap gap-2">
                                        <select class="filter-select" id="currency">
                                            <option value="default" data-symbol="{{ $currencySymbol }}">
                                                {{ $currency }}</option>
                                            @foreach ($data['currencies'] as $currencyData)
                                                <option value="{{ $currencyData->id }}"
                                                    data-symbol="{{ $currencyData->symbol }}">
                                                    {{ $currencyData->code }}</option>
                                            @endforeach
                                        </select>
                                        <input class="card-header-input" type="text" name="site_daterange"
                                            value="{{ $data['start_date'] . ' - ' . $data['end_date'] }}" />
                                    </div>
                                </div>
                                <div class="site-card-body">
                                    <canvas id="statisticsChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                @endcan

                @can('top-country-statistics')
                    <div class="col-xl-4 col-lg-6 col-md-6 col-sm-6 col-12">
                        <div class="site-chart">
                            <div class="site-card">
                                <div class="site-card-header">
                                    <h3 class="title">{{ __('Top Country Statistics') }}</h3>
                                </div>
                                <div class="site-card-body">
                                    <canvas id="countryChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                @endcan

                @can('top-browser-statistics')
                    <div class="col-xl-4 col-lg-6 col-md-6 col-sm-6 col-12">
                        <div class="site-chart">
                            <div class="site-card">
                                <div class="site-card-header">
                                    <h3 class="title">{{ __('Top Browser Statistics') }}</h3>
                                </div>
                                <div class="site-card-body">
                                    <canvas id="browserChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                @endcan

                @can('top-os-statistics')
                    <div class="col-xl-4 col-lg-6 col-md-6 col-sm-6 col-12">
                        <div class="site-chart">
                            <div class="site-card">
                                <div class="site-card-header">
                                    <h3 class="title">{{ __('Top OS Statistics') }}</h3>
                                </div>
                                <div class="site-card-body">
                                    <canvas id="osChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                @endcan

                @can('latest-users')
                    <div class="col-xl-12">
                        <div class="site-card">
                            <div class="site-card-header">
                                <h3 class="title">{{ __('Latest Customers') }}</h3>
                            </div>
                            <div class="site-card-body table-responsive">
                                <div class="site-datatable">
                                    <table class="data-table mb-0">
                                        <thead>
                                            <tr>
                                                <th>{{ __('Avatar') }}</th>
                                                <th>{{ __('User') }}</th>
                                                <th>{{ __('Email') }}</th>
                                                <th>{{ __('Main Balance') }}</th>
                                                <th>{{ __('Status') }}</th>
                                                <th>{{ __('Action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($data['latest_user'] as $user)
                                                <tr>
                                                    <td>
                                                        @include('backend.user.include.__avatar', [
                                                            'avatar' => $user->avatar,
                                                            'first_name' => $user->first_name,
                                                            'last_name' => $user->last_name,
                                                        ])
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('admin.user.edit', $user->id) }}"
                                                            class="link">{{ Str::limit($user->username, 15) }}
                                                        </a>
                                                    </td>
                                                    <td>
                                                        <strong>{{ config('app.demo') ? safe($user->email) : Str::limit($user->email, 20) }}</strong>
                                                    </td>
                                                    <td>
                                                        {{ formatAmount($user->balance, $currency, true) }}
                                                    </td>
                                                    <td>
                                                        @if ($user->status == 1)
                                                            <div class="site-badge success">{{ __('Active') }}</div>
                                                        @else
                                                            <div class="site-badge danger">{{ __('Deactivated') }}</div>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @canany(['customer-basic-manage', 'customer-balance-add-or-subtract',
                                                            'customer-change-password', 'all-type-status'])
                                                            <a href="{{ route('admin.user.edit', $user->id) }}"
                                                                class="round-icon-btn primary-btn" data-bs-toggle="tooltip"
                                                                data-bs-original-title="{{ __('Edit User') }}"><i
                                                                    data-lucide="edit-3"></i>
                                                            </a>
                                                        @endcanany
                                                    </td>
                                                </tr>
                                            @empty
                                                <td colspan="7" class="text-center">
                                                    @if ($data['latest_user']->isEmpty())
                                                        {{ __('No Data Found') }}
                                                    @endif
                                                </td>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                @endcan
                @can('latest-merchants')
                    <div class="col-xl-12">
                        <div class="site-card">
                            <div class="site-card-header">
                                <h3 class="title">{{ __('Latest Merchants') }}</h3>
                            </div>
                            <div class="site-card-body table-responsive">
                                <div class="site-datatable">
                                    <table class="data-table mb-0">
                                        <thead>
                                            <tr>
                                                <th>{{ __('Avatar') }}</th>
                                                <th>{{ __('User') }}</th>
                                                <th>{{ __('Email') }}</th>
                                                <th>{{ __('Main Balance') }}</th>
                                                <th>{{ __('Status') }}</th>
                                                <th>{{ __('Action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($data['latest_merchants'] as $merchant)
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
                                                    <td>
                                                        <strong>{{ config('app.demo') ? safe($merchant->user?->email) : Str::limit($merchant->user?->email, 25) }}</strong>
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
                                                        @canany(['merchant-basic-manage', 'merchant-balance-add-or-subtract',
                                                            'merchant-change-password', 'merchant-all-type-status'])
                                                            <a href="{{ route('admin.merchant.edit', $merchant->id) }}"
                                                                class="round-icon-btn primary-btn" data-bs-toggle="tooltip"
                                                                data-bs-original-title="{{ __('Edit Merchant') }}"><i
                                                                    data-lucide="edit-3"></i>
                                                            </a>
                                                        @endcan
                                                    </td>
                                                </tr>
                                            @empty
                                                <td colspan="8" class="text-center">{{ __('No Data Found!') }}</td>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                @endcan
                @can('latest-agents')
                    <div class="col-xl-12">
                        <div class="site-card">
                            <div class="site-card-header">
                                <h3 class="title">{{ __('Latest Agents') }}</h3>
                            </div>
                            <div class="site-card-body table-responsive">
                                <div class="site-datatable">
                                    <table class="data-table mb-0">
                                        <thead>
                                            <tr>
                                                <th>{{ __('Avatar') }}</th>
                                                <th>{{ __('User') }}</th>
                                                <th>{{ __('Email') }}</th>
                                                <th>{{ __('Main Balance') }}</th>
                                                <th>{{ __('Status') }}</th>
                                                <th>{{ __('Action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($data['latest_agents'] as $agent)
                                                <tr>
                                                    <td>
                                                        @if (null != $agent->user?->avatar)
                                                            <img class="avatar avatar-round"
                                                                src="{{ asset($agent->user?->avatar) }}" alt=""
                                                                height="40" width="40">
                                                        @else
                                                            <span class="avatar-text">
                                                                {{ getShortName($agent->user?->full_name) }}</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('admin.agent.edit', $agent->id) }}"
                                                            class="link">{{ Str::limit($agent->user?->username, 15) }}</a>
                                                    </td>
                                                    <td>
                                                        <strong>{{ config('app.demo') ? safe($agent->user?->email) : Str::limit($agent->user?->email, 25) }}</strong>
                                                    </td>
                                                    <td>
                                                        {{ formatAmount($agent->user?->balance ?? 0, $currency, true) }}
                                                    </td>
                                                    <td>
                                                        @if ($agent->status == App\Enums\AgentStatus::Approved)
                                                            <div class="site-badge success">{{ __('Active') }}</div>
                                                        @elseif($agent->status == App\Enums\AgentStatus::Disabled)
                                                            <div class="site-badge danger">{{ __('Disabled') }}</div>
                                                        @elseif($agent->status == App\Enums\AgentStatus::Rejected)
                                                            <div class="site-badge danger">{{ __('Rejected') }}</div>
                                                        @elseif($agent->status == App\Enums\AgentStatus::Pending)
                                                            <div class="site-badge pending">{{ __('Pending') }}</div>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @can(['agent-edit'])
                                                            <a href="{{ route('admin.agent.edit', $agent->id) }}"
                                                                class="round-icon-btn primary-btn" data-bs-toggle="tooltip"
                                                                data-bs-original-title="{{ __('Edit Agent') }}"><i
                                                                    data-lucide="edit-3"></i>
                                                            </a>
                                                        @endcan
                                                    </td>
                                                </tr>
                                            @empty
                                                <td colspan="8" class="text-center">{{ __('No Data Found!') }}</td>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                @endcan
            </div>
        </div>
    </div>
@endsection

@section('script')
    @include('backend.include.__chartjs')
    <script>
        (function($) {
            'use strict'
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
        })(jQuery)
    </script>
@endsection
