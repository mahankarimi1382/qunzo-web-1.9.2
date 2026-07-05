@extends('backend.layouts.app')
@section('title')
    {{ __('Merchant Details') }}
@endsection
@section('content')
    <div class="main-content">
        <div class="page-title">
            <div class="container-fluid">
                <div class="row">
                    <div class="col">
                        <div class="title-content">
                            <h2 class="title">{{ __('Details of') . ' ' . $user->first_name . ' ' . $user->last_name }}
                            </h2>
                            <a href="{{ url()->previous() }}" class="title-btn"><i
                                    data-lucide="corner-down-left"></i>{{ __('Back') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-xxl-3 col-xl-6 col-lg-8 col-md-6 col-sm-12">
                    <div class="profile-card">
                        <div class="top">
                            <div class="avatar">
                                <div class="avatar-face">
                                    @if (null != $user->avatar)
                                        <div class="avatar-face">
                                            <img class="avatar-img" src="{{ asset($user->avatar) }}"
                                                alt="{{ $user->full_name }}" />
                                        </div>
                                    @else
                                        <div class="avatar-text">{{ $user->first_name[0] . $user->last_name[0] }}</div>
                                    @endif
                                </div>
                            </div>
                            <div class="title-des">
                                <h4>{{ $user->first_name . ' ' . $user->last_name }}</h4>
                                <p>{{ __('MID') }}: <strong>{{ $user->account_number }}</strong></p>
                                <p>{{ ucwords($user->city) }}@if ($user->city != '')
                                        ,
                                    @endif{{ $user->country }}</p>
                                @if ($user->activities->count() > 0)
                                    @php
                                        $lastLogin = $user->activities->sortByDesc('created_at')->first();
                                        $lastLoginDateTime = optional($lastLogin)->created_at->format('d-m-Y H:i:s');
                                    @endphp
                                    <p>{{ __('Last Login:') }} {{ $lastLoginDateTime }}</p>
                                @else
                                    <p>{{ __('This user has not logged in yet.') }}</p>
                                @endif
                            </div>
                            <div class="btns">
                                @can('merchant-send-mail')
                                    <span type="button" data-bs-toggle="modal" data-bs-target="#sendEmail"><a
                                            href="javascript:void(0);" class="site-btn-round blue-btn" data-bs-toggle="tooltip"
                                            title="" data-bs-original-title="{{ __('Send Email') }}"><i
                                                data-lucide="mail"></i></a></span>
                                @endcan
                                @can('merchant-balance-add-or-subtract')
                                    <span data-bs-toggle="modal" data-bs-target="#addSubBal">
                                        <a href="javascript:void(0);" type="button" class="site-btn-round primary-btn"
                                            data-bs-toggle="tooltip" title="" data-bs-placement="top"
                                            data-bs-original-title="{{ __('Fund Add or Subtract') }}">
                                            <i data-lucide="wallet"></i>
                                        </a>
                                    </span>
                                @endcan
                                @can('merchant-delete')
                                    <a href="#" class="site-btn-round red-btn" id="deleteModal" data-bs-toggle="modal"
                                        data-bs-target="#delete" title="{{ __('Delete User') }}"><i
                                            data-lucide="trash-2"></i></a>

                                    <!-- Modal for Popup Box -->
                                    @include('backend.agent.include.__delete_popup', ['id' => $user->id])
                                    <!-- Modal for Popup Box End-->
                                @endcan
                            </div>
                        </div>
                        <div class="site-card">
                            <div class="site-card-body">
                                <div class="row">
                                    <div class="col-xl-12">
                                        <div class="admin-user-balance-card">
                                            <div class="wallet-name">
                                                <div class="name">{{ __('Main Wallet') }}</div>
                                                <div class="chip-icon">
                                                    <img class="chip" src="{{ asset('backend/materials/chip.png') }}"
                                                        alt="" />
                                                </div>
                                            </div>
                                            <div class="wallet-info">
                                                <div class="wallet-id">{{ $currency }}</div>
                                                <div class="balance">
                                                    {{ $currencySymbol . formatAmount($user->balance, $currency) }}
                                                </div>
                                            </div>
                                        </div>
                                        @foreach ($user->wallets as $wallet)
                                            <div class="admin-user-balance-card">
                                                <div class="wallet-name">
                                                    <div class="name">{{ $wallet->currency?->name }}</div>
                                                    <div class="chip-icon">
                                                        <img class="chip" src="{{ asset('backend/materials/chip.png') }}"
                                                            alt="" />
                                                    </div>
                                                </div>
                                                <div class="wallet-info">
                                                    <div class="wallet-id">{{ $wallet->currency?->code }}</div>
                                                    <div class="balance">
                                                        {{ $wallet->currency?->symbol . formatAmount($wallet->balance, $wallet->currency) }}
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        @can('merchant-all-type-status')
                            <!-- User Status Update -->
                            @include('backend.merchant.include.__status_update')
                            <!-- User Status Update End-->
                        @endcan
                    </div>
                </div>

                <div class="col-xxl-9 col-xl-12 col-lg-12 col-md-12 col-sm-12">
                    <div class="row">
                        <div class="col-xl-4 col-lg-4 col-md-6 col-sm-6">
                            <div class="data-card">
                                <div class="icon">
                                    <i data-lucide="box"></i>
                                </div>
                                <div class="content">
                                    <h4><span class="count">{{ $statistics['total_withdraw'] }}</span> </h4>
                                    <p>{{ __('Total Withdraw') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4 col-lg-4 col-md-6 col-sm-6">
                            <div class="data-card">
                                <div class="icon">
                                    <i data-lucide="russian-ruble"></i>
                                </div>
                                <div class="content">
                                    <h4 class="count">{{ $statistics['total_payments'] }}</h4>
                                    <p>{{ __('Total Payments') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4 col-lg-4 col-md-6 col-sm-6">
                            <div class="data-card">
                                <div class="icon">
                                    <i data-lucide="message-circle"></i>
                                </div>
                                <div class="content">
                                    <h4 class="count">{{ $statistics['total_tickets'] }}</h4>
                                    <p>{{ __('Total Tickets') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="site-tab-bars">
                        <ul class="nav nav-pills" id="pills-tab" role="tablist">
                            @canany(['merchant-basic-manage', 'merchant-change-password'])
                                <li class="nav-item" role="presentation">
                                    <a href="{{ route('admin.merchant.edit', $merchant->id) }}"
                                        class="nav-link {{ !request()->has('tab') ? 'active' : '' }}"><i
                                            data-lucide="user"></i>{{ __('Information') }}</a>
                                </li>
                            @endcanany
                            @can('transaction-list')
                                <li class="nav-item" role="presentation">
                                    <a href="{{ route('admin.merchant.edit', ['id' => $merchant->id, 'tab' => 'transactions']) }}"
                                        class="nav-link {{ request('tab') == 'transactions' ? 'active' : '' }}"><i
                                            data-lucide="cast"></i>{{ __('Transactions') }}</a>
                                </li>
                            @endcan
                            @canany(['support-ticket-list', 'support-ticket-action'])
                                <li class="nav-item" role="presentation">
                                    <a href="{{ route('admin.merchant.edit', ['id' => $merchant->id, 'tab' => 'ticket']) }}"
                                        class="nav-link {{ request('tab') == 'ticket' ? 'active' : '' }}"><i
                                            data-lucide="wrench"></i>{{ __('Ticket') }}</a>
                                </li>
                            @endcanany
                        </ul>
                    </div>

                    <div class="tab-content" id="pills-tabContent">
                        <!-- basic Info -->
                        @canany(['merchant-basic-manage', 'merchant-change-password'])
                            @include('backend.merchant.include.__basic_info')
                        @endcanany

                        <!-- transaction -->
                        @can('transaction-list')
                            @include('backend.user.include.__transactions')
                        @endcan

                        <!-- ticket -->
                        @canany(['support-ticket-list', 'support-ticket-action'])
                            @include('backend.user.include.__ticket')
                        @endcan
                    </div>
                </div>
            </div>

            <!-- Modal for Send Email -->
            @can('mail-send')
                @include('backend.user.include.__mail_send', [
                    'name' => $user->first_name . ' ' . $user->last_name,
                    'id' => $user->id,
                ])
            @endcan
            <!-- Modal for Send Email-->

            <!-- Modal for balance -->
            @include('backend.user.include.__balance', ['id' => $user->id])
            <!-- Modal for balance End-->
        @endsection

        @section('script')
            <script>
                "use strict";

                $('#country').select2();

                // Delete
                $('body').on('click', '#deleteModal', function() {
                    var id = "{{ $user->id }}";

                    var url = '{{ route('admin.user.destroy', ':id') }}';
                    url = url.replace(':id', id);
                    $('#deleteForm').attr('action', url);
                    $('#delete').modal('toggle')
                });

                // Wallet wise currency change
                $('select[name=wallet_type]').on('change', function() {
                    var currency = $(this).find(':selected').data('currency');
                    $('.balance-add-sub-currency').text(currency);
                });
            </script>
        @endsection
