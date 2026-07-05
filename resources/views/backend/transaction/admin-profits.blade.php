@extends('backend.layouts.app')
@section('title')
    {{ __('Admin Profits') }}
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
                    <div class="site-card">
                        <div class="site-card-body p-0">
                            <div class="site-table table-responsive">
                                @include('backend.transaction.include.__filter', [
                                    'status' => true,
                                    'type' => true,
                                ])
                                <table class="table">
                                    <thead>
                                        <tr>
                                            @include('backend.filter.th', [
                                                'label' => 'Date',
                                                'field' => 'created_at',
                                            ])
                                            @include('backend.filter.th', [
                                                'label' => 'Description',
                                                'field' => 'description',
                                            ])
                                            @include('backend.filter.th', [
                                                'label' => 'User',
                                                'field' => 'user',
                                            ])
                                            @include('backend.filter.th', [
                                                'label' => 'Amount',
                                                'field' => 'final_amount',
                                            ])
                                            @include('backend.filter.th', [
                                                'label' => 'Type',
                                                'field' => 'type',
                                            ])
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($profits as $profit)
                                            <tr>
                                                <td>
                                                    {{ $profit->created_at }}
                                                </td>
                                                <td>
                                                    {{ safe($profit->description) }}
                                                </td>
                                                <td>
                                                    @include('backend.transaction.include.__user', [
                                                        'user' => $profit->user,
                                                    ])
                                                </td>
                                                <td>
                                                    {{ formatAmount($profit->final_amount, $profit->currency, true) }}
                                                </td>
                                                <td>
                                                    @include('backend.transaction.include.__txn_type', [
                                                        'txnType' => $profit->type->value,
                                                    ])
                                                </td>
                                            </tr>
                                        @empty
                                            <td colspan="6" class="text-center">{{ __('No Data Found!') }}</td>
                                        @endforelse
                                    </tbody>
                                </table>

                                {{ $profits->links('backend.include.__pagination') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
