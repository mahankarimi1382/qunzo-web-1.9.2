@extends('backend.layouts.app')

@section('title')
    {{ __(':status Agents', ['status' => ucfirst(request('status'))]) }}
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
                                        @foreach (App\Enums\AgentStatus::cases() as $status)
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
                                @forelse($agents as $key => $agent)
                                    <tr>
                                        <td>
                                            @if (null != $agent->user?->avatar)
                                                <img class="avatar avatar-round" src="{{ asset($agent->user?->avatar) }}"
                                                    alt="" height="40" width="40">
                                            @else
                                                <span class="avatar-text">
                                                    {{ getShortName($agent->user?->full_name) }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.agent.edit', $agent->id) }}"
                                                class="link">{{ Str::limit($agent->user?->username, 15) }}</a>
                                        </td>
                                        <td>{{ config('app.demo') ? safe($agent->user?->email) : Str::limit($agent->user?->email, 25) }}</td>
                                        <td>
                                            {{ formatAmount($agent->user->balance, $currency, true) }}
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
                                            @can('agent-send-mail')
                                                <button type="button" data-id="{{ $agent->id }}"
                                                    data-name="{{ $agent->user->full_name }}"
                                                    class="send-mail round-icon-btn blue-btn" data-bs-toggle="tooltip"
                                                    data-bs-original-title="{{ __('Send Email') }}">
                                                    <i data-lucide="mail"></i>
                                                </button>
                                            @endcan

                                            @can(['agent-delete'])
                                                <button type="button" class="round-icon-btn red-btn" id="deleteModal"
                                                    data-id="{{ $agent->user_id }}" data-bs-toggle="tooltip"
                                                    data-bs-original-title="{{ __('Delete Agent') }}">
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
                        {{ $agents->links('backend.include.__pagination') }}
                    </div>
                </div>
            </div>
        </div>
        @can('agent-send-mail')
            @include('backend.user.include.__mail_send')
        @endcan
        @can(['agent-delete'])
            @include('backend.user.include.__delete_popup')
        @endcan
    </div>
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
