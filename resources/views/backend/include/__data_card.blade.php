@can('total-users')
    <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
        <div class="data-card">
            <div class="icon">
                <i data-lucide="users"></i>
            </div>
            <div class="content">
                <h4 class="count">{{ $data['register_user'] }}</h4>
                <p>{{ __('All Users') }}</p>
            </div>
            <a class="link" href="{{ route('admin.user.index') }}"><i data-lucide="external-link"></i></a>
        </div>
    </div>
@endcan
@if (merchantSystemEnabled())
    @can('total-merchants')
        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
            <div class="data-card">
                <div class="icon">
                    <i data-lucide="store"></i>
                </div>
                <div class="content">
                    <h4 class="count">{{ $data['total_merchants'] }}</h4>
                    <p>{{ __('All Merchants') }}</p>
                </div>
                <a class="link" href="{{ route('admin.merchant.index') }}"><i data-lucide="external-link"></i></a>
            </div>
        </div>
    @endcan
@endif
@if (agentSystemEnabled())
    @can('total-agents')
        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
            <div class="data-card">
                <div class="icon">
                    <i data-lucide="user-square"></i>
                </div>
                <div class="content">
                    <h4 class="count">{{ $data['total_agents'] }}</h4>
                    <p>{{ __('All Agents') }}</p>
                </div>
                <a class="link" href="{{ route('admin.agent.index') }}"><i data-lucide="external-link"></i></a>
            </div>
        </div>
    @endcan
@endif
@can('all-deposits')
    <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
        <div class="data-card">
            <div class="icon">
                <i data-lucide="dollar-sign"></i>
            </div>
            <div class="content">
                <h4 class="count">{{ $data['all_deposits'] }}</h4>
                <p>{{ __('All Deposits') }}</p>
            </div>
            <a class="link" href="{{ route('admin.deposit.history') }}"><i data-lucide="external-link"></i></a>
        </div>
    </div>
@endcan
@can('all-currencies')
    <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
        <div class="data-card">
            <div class="icon">
                <i data-lucide="dollar-sign"></i>
            </div>
            <div class="content">
                <h4 class="count">{{ $data['all_currencies'] }}</h4>
                <p>{{ __('All Currencies') }}</p>
            </div>
            <a class="link" href="{{ route('admin.currency.index') }}"><i data-lucide="external-link"></i></a>
        </div>
    </div>
@endcan
@can('total-staff')
    <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
        <div class="data-card">
            <div class="icon">
                <i data-lucide="user-cog"></i>
            </div>
            <div class="content">
                <h4 class="count">{{ $data['total_staff'] }}</h4>
                <p>{{ __('All Staff') }}</p>
            </div>
            <a class="link" href="{{ route('admin.staff.index') }}"><i data-lucide="external-link"></i></a>
        </div>
    </div>
@endcan
@if (merchantSystemEnabled())
    @can('total-payments')
        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
            <div class="data-card">
                <div class="icon">
                    <i data-lucide="credit-card"></i>
                </div>
                <div class="content">
                    <h4 class="count">{{ $data['total_payments'] }}</h4>
                    <p>{{ __('All Payments') }}</p>
                </div>
                <a class="link" href="{{ route('admin.transactions', ['type' => 'Payment']) }}"><i
                        data-lucide="external-link"></i></a>
            </div>
        </div>
    @endcan
@endif
@can('total-withdraw')
    <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
        <div class="data-card">
            <div class="icon">
                <i data-lucide="wallet"></i>
            </div>
            <div class="content">
                <h4 class="count">{{ $data['total_withdraw'] }}</h4>
                <p>{{ __('All Withdraw') }}</p>
            </div>
            <a class="link" href="{{ route('admin.withdraw.history') }}"><i data-lucide="external-link"></i></a>
        </div>
    </div>
@endcan
@if (agentSystemEnabled())
    @can('total-cashout')
        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
            <div class="data-card">
                <div class="icon">
                    <i data-lucide="banknote"></i>
                </div>
                <div class="content">
                    <h4 class="count">{{ $data['total_cashout'] }}</h4>
                    <p>{{ __('All Cashout') }}</p>
                </div>
                <a class="link" href="{{ route('admin.transactions', ['type' => 'Cash Out']) }}"><i
                        data-lucide="external-link"></i></a>
            </div>
        </div>
    @endcan
@endif

@can('total-transfer')
    <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
        <div class="data-card">
            <div class="icon">
                <i data-lucide="send"></i>
            </div>
            <div class="content">
                <h4 class="count">{{ $data['total_transfer'] }}</h4>
                <p>{{ __('All Transfer') }}</p>
            </div>
            <a class="link" href="{{ route('admin.transactions', ['type' => 'Send Money']) }}"><i
                    data-lucide="external-link"></i></a>
        </div>
    </div>
@endcan
@can('total-automatic-gateway')
    <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
        <div class="data-card">
            <div class="icon">
                <i data-lucide="webhook"></i>
            </div>
            <div class="content">
                <h4 class="count">{{ $data['total_gateway'] }}</h4>
                <p>{{ __('All Automatic Gateways') }}</p>
            </div>
            <a class="link" href="{{ route('admin.gateway.automatic') }}"><i data-lucide="external-link"></i></a>
        </div>
    </div>
@endcan
@can('total-ticket')
    <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
        <div class="data-card">
            <div class="icon">
                <i data-lucide="help-circle"></i>
            </div>
            <div class="content">
                <h4 class="count">{{ $data['total_ticket'] }}</h4>
                <p>{{ __('All Ticket') }}</p>
            </div>
            <a class="link" href="{{ route('admin.ticket.index') }}"><i data-lucide="external-link"></i></a>
        </div>
    </div>
@endcan
