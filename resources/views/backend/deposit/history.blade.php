@extends('backend.deposit.index')
@section('title')
    {{ __('Deposit History') }}
@endsection
@section('deposit_content')
    <div class="col-xl-12 col-md-12">
        <div class="site-card-body table-responsive">
            <div class="site-table table-responsive">
                @include('backend.deposit.include.__filter', ['status' => true])
                <table class="table">
                    <thead>
                        <tr>
                            @include('backend.filter.th', ['label' => 'Date', 'field' => 'created_at'])
                            @include('backend.filter.th', ['label' => 'User', 'field' => 'user'])
                            @include('backend.filter.th', ['label' => 'Transaction ID', 'field' => 'tnx'])
                            @include('backend.filter.th', ['label' => 'Amount', 'field' => 'amount'])
                            @include('backend.filter.th', ['label' => 'Charge', 'field' => 'charge'])
                            @include('backend.filter.th', ['label' => 'Gateway', 'field' => 'method'])
                            @include('backend.filter.th', ['label' => 'Wallet', 'field' => 'wallet'])
                            @include('backend.filter.th', ['label' => 'Status', 'field' => 'status'])
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($deposits as $deposit)
                            <tr>
                                <td>
                                    {{ $deposit->created_at }}
                                </td>
                                <td>
                                    @include('backend.transaction.include.__user', [
                                        'user' => $deposit->user
                                    ])
                                </td>
                                <td>{{ safe($deposit->tnx) }}</td>
                                <td>
                                    {{ formatAmount($deposit->final_amount, $deposit->currency, true) }}
                                </td>
                                <td>
                                    {{ formatAmount($deposit->charge, $deposit->currency, true) }}
                                </td>
                                <td>
                                    {{ safe($deposit->method) }}
                                </td>
                                <td>
                                    @if ($deposit->wallet_type == null || $deposit->wallet_type == 'default')
                                        {{ __('Main Wallet') }}
                                    @else
                                        {{ $deposit?->userWallet?->currency?->name }}
                                    @endif
                                </td>
                                <td>
                                    @include('backend.transaction.include.__txn_status', [
                                        'status' => $deposit->status->value,
                                    ])
                                </td>
                            </tr>
                        @empty
                            <td colspan="7" class="text-center">{{ __('No Data Found!') }}</td>
                        @endforelse
                    </tbody>
                </table>

                {{ $deposits->links('backend.include.__pagination') }}
            </div>
        </div>

    </div>
@endsection
