@extends('backend.layouts.app')
@section('title')
    {{ __('Trader Applications') }}
@endsection
@section('content')
    <div class="main-content">
        <div class="page-title">
            <div class="container-fluid">
                <div class="row">
                    <div class="col">
                        <div class="title-content">
                            <h2 class="title">{{ __('Trader Applications') }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid">
            <div class="row">
                <div class="col-xl-12 col-md-12">
                    <div class="site-table table-responsive">
                        <form action="{{ request()->url() }}" method="get" id="filterForm">
                            <div class="table-filter">
                                <div class="filter">
                                    <div class="search">
                                        <input type="text" id="search" name="search" value="{{ request('search') }}"
                                            placeholder="{{ __('Search...') }}" />
                                    </div>
                                    <button type="submit" class="apply-btn"><i data-lucide="search"></i>{{ __('Search') }}</button>
                                </div>
                                <div class="filter d-flex">
                                    <select class="form-select form-select-sm show" aria-label=".form-select-sm example"
                                        name="perPage" id="perPage">
                                        <option value="15" {{ request('perPage') == 15 ? 'selected' : '' }}>15</option>
                                        <option value="30" {{ request('perPage') == 30 ? 'selected' : '' }}>30</option>
                                        <option value="45" {{ request('perPage') == 45 ? 'selected' : '' }}>45</option>
                                        <option value="60" {{ request('perPage') == 60 ? 'selected' : '' }}>60</option>
                                    </select>
                                    <select class="form-select form-select-sm" aria-label=".form-select-sm example"
                                        name="status" id="status">
                                        <option value="all" {{ request('status', 'all') == 'all' ? 'selected' : '' }}>
                                            {{ __('All') }}</option>
                                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>
                                            {{ __('Pending') }}</option>
                                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>
                                            {{ __('Approved') }}</option>
                                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>
                                            {{ __('Rejected') }}</option>
                                    </select>
                                </div>
                            </div>
                        </form>

                        <table class="table">
                            <thead>
                                <tr>
                                    @include('backend.filter.th', [
                                        'label' => 'Date',
                                        'field' => 'created_at',
                                    ])
                                    <th>{{ __('User') }}</th>
                                    <th>{{ __('Type') }}</th>
                                    @include('backend.filter.th', [
                                        'label' => 'Status',
                                        'field' => 'status',
                                    ])
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($traderApplications as $application)
                                    <tr>
                                        <td>
                                            <span class="clock"> {{ $application->created_at->format('F d Y h:i') }} </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.user.edit', $application->user_id) }}" class="link">
                                                {{ safe(Str::limit($application->user?->username, 15)) }} ({{ $application->user?->role }})
                                            </a>
                                        </td>
                                        <td>
                                            <strong class="transaction">{{ $application->kyc?->name ?? __('Verified Trader') }}</strong>
                                        </td>
                                        <td>
                                            @switch($application->status)
                                                @case('approved')
                                                    <div class="site-badge success">{{ __('Approved') }}</div>
                                                @break

                                                @case('rejected')
                                                    <div class="site-badge danger">{{ __('Rejected') }}</div>
                                                @break

                                                @default
                                                    <div class="site-badge pending">{{ __('Pending') }}</div>
                                            @endswitch
                                        </td>
                                        <td>
                                            @can('kyc-action')
                                                <button class="round-icon-btn primary-btn" type="button" id="action-kyc"
                                                    data-id="{{ $application->user_id }}" data-bs-toggle="tooltip"
                                                    data-bs-placement="top"
                                                    data-bs-original-title="{{ __('View KYC Details') }}">
                                                    <i data-lucide="eye"></i>
                                                </button>
                                            @endcan
                                        </td>
                                    </tr>
                                @empty
                                    <td colspan="5" class="text-center">{{ __('No Data Found!') }}</td>
                                @endforelse
                            </tbody>
                        </table>

                        {{ $traderApplications->links('backend.include.__pagination') }}
                    </div>

                    @can('kyc-action')
                        @include('backend.kyc.include.__details_modal')
                    @endcan
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        "use strict";

        $('#perPage').on('change', function() {
            $('#filterForm').submit();
        });

        $('#status').on('change', function() {
            $('#filterForm').submit();
        });

        let loader =
            '<div class="text-center"><i data-lucide="loader-circle" class=" spining-icon"> </i> {{ __('Please wait..') }}</div>';

        $(document).on('click', '#action-kyc', function(e) {
            e.preventDefault();
            $('#kyc-action-data').html(loader);
            lucide.createIcons();

            const id = $(this).data('id');
            let url = '{{ route('admin.verification.action', ':id') }}';
            url = url.replace(':id', id) + '?for={{ \App\Enums\KycFor::VerifiedTrader->value }}';

            $.get(url, function(data) {
                $('#kyc-action-data').html(data);
            });

            $('#kyc-action-modal').modal('toggle');
        });
    </script>
@endsection
