<div class="side-nav">
    <div class="side-nav-inside">
        <ul class="side-nav-menu">
            <li class="side-nav-item {{ isActive('admin.dashboard') }}">
                <a href="{{ route('admin.dashboard') }}">
                    <i data-lucide="layout-dashboard"></i>
                    <span>
                        {{ __('Dashboard') }}
                    </span>
                </a>
            </li>

            @canany(['customer-list', 'customer-basic-manage', 'customer-balance-add-or-subtract',
                'customer-change-password', 'all-type-status'])
                <li class="side-nav-item category-title">
                    <span>
                        {{ __('Users Management') }}
                    </span>
                </li>
                <li
                    class="side-nav-item side-nav-dropdown {{ isActive([
                        'admin.user.index',
                        'admin.user.active',
                        'admin.user.disabled',
                        'admin.user.new',
                        'admin.user.mail-send.all',
                        'admin.notification.all',
                    ]) }}">
                    <a href="javascript:void(0);" class="dropdown-link">
                        <i data-lucide="users"></i>
                        <span>
                            {{ __('Customers') }}
                        </span>
                        <span class="right-arrow">
                            <i data-lucide="chevron-down"></i>
                        </span>
                    </a>
                    <ul class="dropdown-items">
                        @canany(['customer-list', 'customer-basic-manage', 'customer-balance-add-or-subtract',
                            'customer-change-password', 'all-type-status'])
                            <li class="{{ isActive('admin.user.index') }}">
                                <a href="{{ route('admin.user.index') }}">
                                    <i data-lucide="users"></i>
                                    {{ __('All Customers') }}
                                </a>
                            </li>
                            <li class="{{ isActive('admin.user.active') }}">
                                <a href="{{ route('admin.user.active') }}">
                                    <i data-lucide="user-check"></i>
                                    {{ __('Active Customers') }}
                                </a>
                            </li>
                            <li class="{{ isActive('admin.user.disabled') }}">
                                <a href="{{ route('admin.user.disabled') }}">
                                    <i data-lucide="user-x"></i>
                                    {{ __('Disabled Customers') }}
                                </a>
                            </li>
                        @endcanany
                    </ul>
                </li>
            @endcanany

            @if (merchantSystemEnabled())
                @canany(['merchant-list', 'merchant-requests'])
                    <li class="side-nav-item side-nav-dropdown {{ isActive(['admin.merchant.*']) }}">
                        <a href="javascript:void(0);" class="dropdown-link">
                            <i data-lucide="contact-round"></i>
                            <span>
                                {{ __('Merchants') }}
                            </span>
                            <span class="right-arrow">
                                <i data-lucide="chevron-down"></i>
                            </span>
                        </a>
                        <ul class="dropdown-items">
                            @canany(['merchant-list'])
                                @can('merchant-list')
                                    <li class="{{ isActive('admin.merchant.index', 'all') }}">
                                        <a href="{{ route('admin.merchant.index', 'all') }}">
                                            <i data-lucide="users"></i>
                                            {{ __('All Merchants') }}
                                        </a>
                                    </li>
                                    <li class="{{ isActive('admin.merchant.index', 'pending') }}">
                                        <a href="{{ route('admin.merchant.index', 'pending') }}">
                                            <i data-lucide="user-plus"></i>
                                            {{ __('Pending Merchants') }}
                                        </a>
                                    </li>
                                    <li class="{{ isActive('admin.merchant.index', 'approved') }}">
                                        <a href="{{ route('admin.merchant.index', 'approved') }}">
                                            <i data-lucide="user-check"></i>
                                            {{ __('Approved Merchants') }}
                                        </a>
                                    </li>
                                    <li class="{{ isActive('admin.merchant.index', 'rejected') }}">
                                        <a href="{{ route('admin.merchant.index', 'rejected') }}">
                                            <i data-lucide="x-circle"></i>
                                            {{ __('Rejected Merchants') }}
                                        </a>
                                    </li>
                                @endcan
                            @endcanany
                        </ul>
                    </li>
                @endcanany
            @endif

            @if (agentSystemEnabled())
                @canany(['agent-list', 'agent-requests'])
                    <li class="side-nav-item side-nav-dropdown {{ isActive(['admin.agent.*']) }}">
                        <a href="javascript:void(0);" class="dropdown-link">
                            <i data-lucide="contact-round"></i>
                            <span>
                                {{ __('Agents') }}
                            </span>
                            <span class="right-arrow">
                                <i data-lucide="chevron-down"></i>
                            </span>
                        </a>
                        @canany(['agent-list', 'agent-requests'])
                            <ul class="dropdown-items">
                                @can('agent-list')
                                    <li class="{{ isActive('admin.agent.index', 'all') }}">
                                        <a href="{{ route('admin.agent.index', 'all') }}">
                                            <i data-lucide="users"></i>
                                            {{ __('All Agents') }}
                                        </a>
                                    </li>
                                    <li class="{{ isActive('admin.agent.index', 'approved') }}">
                                        <a href="{{ route('admin.agent.index', 'approved') }}">
                                            <i data-lucide="user-check"></i>
                                            {{ __('Approved Agents') }}
                                        </a>
                                    </li>
                                    <li class="{{ isActive('admin.agent.index', 'pending') }}">
                                        <a href="{{ route('admin.agent.index', 'pending') }}">
                                            <i data-lucide="user-plus"></i>
                                            {{ __('Pending Agents') }}
                                        </a>
                                    </li>
                                    <li class="{{ isActive('admin.agent.index', 'rejected') }}">
                                        <a href="{{ route('admin.agent.index', 'rejected') }}">
                                            <i data-lucide="x-circle"></i>
                                            {{ __('Rejected Agents') }}
                                        </a>
                                    </li>
                                @endcan
                            </ul>
                        @endcanany
                    </li>
                @endcanany
            @endif

            @canany(['verification-list', 'verification-action', 'verification-form-manage'])
                <li class="side-nav-item category-title">
                    <span>
                        {{ __('Verification Management') }}
                    </span>
                </li>
                <li class="side-nav-item side-nav-dropdown {{ isActive(['admin.verification*']) }}">
                    <a href="javascript:void(0);" class="dropdown-link">
                        <i data-lucide="check-square"></i>
                        <span>
                            {{ __('Verification Center') }}
                        </span>
                        <span class="right-arrow">
                            <i data-lucide="chevron-down"></i>
                        </span>
                    </a>
                    <ul class="dropdown-items">
                        @canany(['verification-list', 'verification-action'])
                            <li class="{{ isActive('admin.verification.pending') }}">
                                <a href="{{ route('admin.verification.pending') }}">
                                    <i data-lucide="airplay"></i>
                                    {{ __('Pending Verifications') }}
                                </a>
                            </li>
                            <li class="{{ isActive('admin.verification.rejected') }}">
                                <a href="{{ route('admin.verification.rejected') }}">
                                    <i data-lucide="file-warning"></i>
                                    {{ __('Rejected Verifications') }}
                                </a>
                            </li>
                            <li class="{{ isActive('admin.verification.all') }}">
                                <a href="{{ route('admin.verification.all') }}">
                                    <i data-lucide="contact"></i>
                                    {{ __('All Verifications') }}
                                </a>
                            </li>
                            <li class="{{ isActive('admin.verification.trader-applications') }}">
                                <a href="{{ route('admin.verification.trader-applications') }}">
                                    <i data-lucide="badge-check"></i>
                                    {{ __('Trader Applications') }}
                                </a>
                            </li>
                        @endcanany
                        @can('verification-form-manage')
                            <li class="{{ isActive('admin.verification-form*') }}">
                                <a href="{{ route('admin.verification-form.index') }}">
                                    <i data-lucide="check-square"></i>
                                    {{ __('Verification Forms') }}
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcanany

            @canany(['role-list', 'role-create', 'role-edit', 'staff-list', 'staff-create', 'staff-edit'])
                <li class="side-nav-item category-title">
                    <span>
                        {{ __('Access Management') }}
                    </span>
                </li>
                <li class="side-nav-item side-nav-dropdown {{ isActive(['admin.roles*', 'admin.staff*']) }}">
                    <a href="javascript:void(0);" class="dropdown-link">
                        <i data-lucide="users"></i>
                        <span>
                            {{ __('System Access') }}
                        </span>
                        <span class="right-arrow">
                            <i data-lucide="chevron-down"></i>
                        </span>
                    </a>
                    <ul class="dropdown-items">
                        @canany(['role-list', 'role-create', 'role-edit'])
                            <li class="{{ isActive('admin.roles*') }}">
                                <a href="{{ route('admin.roles.index') }}">
                                    <i data-lucide="contact"></i>
                                    <span>
                                        {{ __('Manage Roles') }}
                                    </span>
                                </a>
                            </li>
                        @endcanany
                        @canany(['staff-list', 'staff-create', 'staff-edit'])
                            <li class="{{ isActive('admin.staff*') }}">
                                <a href="{{ route('admin.staff.index') }}">
                                    <i data-lucide="user-cog"></i>
                                    <span>
                                        {{ __('Manage Staffs') }}
                                    </span>
                                </a>
                            </li>
                        @endcanany
                    </ul>
                </li>
            @endcanany

            @canany(['automatic-gateway-manage', 'transaction-list', 'manual-gateway-manage', 'deposit-list',
                'deposit-action', 'withdraw-list', 'withdraw-method-manage', 'withdraw-action', 'referral-create',
                'manage-referral', 'referral-edit', 'referral-delete'])

                <li class="side-nav-item category-title">
                    <span>
                        {{ __('Essentials') }}
                    </span>
                </li>
                @can('transaction-list')
                    <li class="side-nav-item {{ isActive('admin.transactions') }}">
                        <a href="{{ route('admin.transactions') }}">
                            <i data-lucide="cast"></i>
                            <span>
                                {{ __('Transactions') }}
                            </span>
                        </a>
                    </li>
                @endcan
                @can('admin-profits')
                    <li class="side-nav-item {{ isActive('admin.profits') }}">
                        <a href="{{ route('admin.profits') }}">
                            <i data-lucide="dollar-sign"></i>
                            <span>
                                {{ __('Admin Profits') }}
                            </span>
                        </a>
                    </li>
                @endcan
                @if (addonActive('virtual-cards'))
                    @can('virtual-card-list')
                        <li class="side-nav-item {{ isActive('admin.virtual.cards') }}">
                            <a href="{{ route('admin.virtual.cards') }}">
                                <i data-lucide="credit-card"></i>

                                <span>{{ __('Virtual Cards') }} </span>
                                @if (config('app.demo'))
                                    <span class="site-badge primary">Addon</span>
                                @endif
                            </a>
                        </li>
                    @endcan
                @endif
                @can('automatic-gateway-manage')
                    <li class="side-nav-item {{ isActive('admin.gateway*') }}">
                        <a href="{{ route('admin.gateway.automatic') }}">
                            <i data-lucide="door-open"></i>
                            <span>
                                {{ __('Automatic Gateways') }}
                            </span>
                        </a>
                    </li>
                @endcan

                @canany(['automatic-gateway-manage', 'manual-gateway-manage', 'deposit-list', 'deposit-action'])
                    <li class="side-nav-item side-nav-dropdown {{ isActive(['admin.deposit*']) }}">
                        <a href="javascript:void(0);" class="dropdown-link">
                            <i data-lucide="arrow-down-circle"></i>
                            <span>
                                {{ __('Deposits') }}
                            </span>
                            <span class="right-arrow">
                                <i data-lucide="chevron-down"></i>
                            </span>
                        </a>
                        <ul class="dropdown-items">
                            @can('automatic-gateway-manage')
                                <li class="{{ isActive('admin.deposit.method.list', 'auto') }}">
                                    <a href="{{ route('admin.deposit.method.list', 'auto') }}">
                                        <i data-lucide="workflow"></i>
                                        {{ __('Automatic Methods') }}
                                    </a>
                                </li>
                            @endcan

                            @can('manual-gateway-manage')
                                <li class="{{ isActive('admin.deposit.method.list', 'manual') }}">
                                    <a href="{{ route('admin.deposit.method.list', 'manual') }}">
                                        <i data-lucide="compass"></i>
                                        {{ __('Manual Methods') }}
                                    </a>
                                </li>
                            @endcan

                            @canany(['deposit-list', 'deposit-action'])
                                <li class="{{ isActive('admin.deposit.manual.pending') }}">
                                    <a href="{{ route('admin.deposit.manual.pending') }}">
                                        <i data-lucide="columns"></i>
                                        {{ __('Pending Manual Deposits') }}
                                    </a>
                                </li>
                                <li class="{{ isActive('admin.deposit.history') }}">
                                    <a href="{{ route('admin.deposit.history') }}">
                                        <i data-lucide="clipboard-check"></i>
                                        {{ __('Deposit History') }}
                                    </a>
                                </li>
                            @endcanany
                        </ul>
                    </li>
                @endcanany

                @canany(['withdraw-list', 'withdraw-method-manage', 'withdraw-action', 'withdraw-schedule'])
                    <li class="side-nav-item side-nav-dropdown  {{ isActive(['admin.withdraw*']) }}">
                        <a href="javascript:void(0);" class="dropdown-link">
                            <i data-lucide="landmark"></i>
                            <span>
                                {{ __('Withdraw') }}
                            </span>
                            <span class="right-arrow">
                                <i data-lucide="chevron-down"></i>
                            </span>
                        </a>
                        <ul class="dropdown-items">
                            @can('withdraw-method-manage')
                                <li class="{{ isActive('admin.withdraw.method.list', 'auto') }}">
                                    <a href="{{ route('admin.withdraw.method.list', 'auto') }}">
                                        <i data-lucide="workflow"></i>
                                        {{ __('Automatic Methods') }}
                                    </a>
                                </li>
                                <li class="{{ isActive('admin.withdraw.method.list', 'manual') }}">
                                    <a href="{{ route('admin.withdraw.method.list', 'manual') }}">
                                        <i data-lucide="compass"></i>
                                        {{ __('Manual Methods') }}
                                    </a>
                                </li>
                            @endcan
                            @canany(['withdraw-list', 'withdraw-action'])
                                <li class="{{ isActive('admin.withdraw.pending') }}">
                                    <a href="{{ route('admin.withdraw.pending') }}">
                                        <i data-lucide="wallet"></i>
                                        {{ __('Pending Withdraws') }}
                                    </a>
                                </li>
                            @endcanany
                            @can('withdraw-schedule')
                                <li class="{{ isActive('admin.withdraw.schedule') }}">
                                    <a href="{{ route('admin.withdraw.schedule') }}">
                                        <i data-lucide="alarm-clock"></i>
                                        {{ __('Withdraw Schedule') }}
                                    </a>
                                </li>
                            @endcan
                            @can('withdraw-list')
                                <li class="{{ isActive('admin.withdraw.history') }}">
                                    <a href="{{ route('admin.withdraw.history') }}">
                                        <i data-lucide="piggy-bank"></i>
                                        {{ __('Withdraw History') }}
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcanany

                @canany(['referral-create', 'manage-referral', 'referral-edit', 'referral-delete'])
                    <li class="side-nav-item {{ isActive('admin.referral.*') }}">
                        <a href="{{ route('admin.referral.index') }}">
                            <i data-lucide="align-end-horizontal"></i>
                            <span>
                                {{ __('Referral') }}
                            </span>
                        </a>
                    </li>
                @endcanany

                @if (addonActive('p2p-trading'))
                    @canany(['p2p-payment-method-manage', 'p2p-ads-manage', 'p2p-orders-manage'])
                        <li class="side-nav-item category-title">
                            <span>
                                {{ __('P2P Trade') }}
                            </span>
                        </li>

                        <li class="side-nav-item side-nav-dropdown {{ isActive(['admin.p2p.*']) }}">
                            <a href="javascript:void(0);" class="dropdown-link">
                                <i data-lucide="arrow-left-right"></i>
                                <span>
                                    {{ __('P2P Trade') }} @if (config('app.demo'))
                                        <span class="site-badge primary">Addon</span>
                                    @endif
                                </span>
                                <span class="right-arrow">
                                    <i data-lucide="chevron-down"></i>
                                </span>
                            </a>
                            <ul class="dropdown-items">
                                @can('p2p-payment-method-manage')
                                    <li class="{{ isActive('admin.p2p.payment-method.*') }}">
                                        <a href="{{ route('admin.p2p.payment-method.index') }}">
                                            <i data-lucide="credit-card"></i>
                                            {{ __('Payment Methods') }}
                                        </a>
                                    </li>
                                @endcan
                                @can('p2p-ads-manage')
                                    <li class="{{ isActive('admin.p2p.ads.index') }}">
                                        <a href="{{ route('admin.p2p.ads.index') }}">
                                            <i data-lucide="file-text"></i>
                                            {{ __('Ads') }}
                                        </a>
                                    </li>
                                @endcan
                                @can('p2p-ads-manage')
                                    <li class="{{ isActive('admin.p2p.ads.pending') }}">
                                        <a href="{{ route('admin.p2p.ads.pending') }}">
                                            <i data-lucide="clock"></i>
                                            {{ __('Pending Ads') }}
                                        </a>
                                    </li>
                                @endcan
                                @can('p2p-orders-manage')
                                    <li class="{{ isActive('admin.p2p.orders.*') && request('status') !== 'disputed' }}">
                                        <a href="{{ route('admin.p2p.orders.index') }}">
                                            <i data-lucide="shopping-cart"></i>
                                            {{ __('Orders') }}
                                        </a>
                                    </li>
                                    <li class="{{ request('status') === 'disputed' ? 'active' : '' }}">
                                        <a href="{{ route('admin.p2p.orders.index', ['status' => 'disputed']) }}">
                                            <i data-lucide="scale"></i>
                                            {{ __('Disputed Orders') }}
                                        </a>
                                    </li>
                                @endcan
                            </ul>
                        </li>
                    @endcanany
                @endif

            @endcanany

            @canany(['bill-service-import', 'bill-convert-rate', 'bill-service-list', 'all-bills', 'pending-bills',
                'complete-bills', 'return-bills'])
                <li class="side-nav-item category-title">
                    <span>{{ __('Bill Management') }}</span>
                </li>

                @canany(['bill-service-import', 'bill-convert-rate', 'bill-service-list'])
                    <li
                        class="side-nav-item side-nav-dropdown {{ isActive(['admin.bill.import.services', 'admin.bill.convert.rate', 'admin.bill.service*']) }}">
                        <a href="javascript:void(0);" class="dropdown-link"><i data-lucide="file"></i>
                            <span>{{ __('Bill Management') }}</span>
                            <span class="right-arrow"><i data-lucide="chevron-down"></i></span>
                        </a>
                        <ul class="dropdown-items">
                            @can('bill-service-import')
                                <li class="side-nav-item {{ isActive('admin.bill.import.services') }}">
                                    <a href="{{ route('admin.bill.import.services') }}"><i
                                            data-lucide="download"></i><span>{{ __('Import Services') }}</span></a>
                                </li>
                            @endcan
                            @can('bill-convert-rate')
                                <li class="side-nav-item {{ isActive('admin.bill.convert.rate') }}">
                                    <a href="{{ route('admin.bill.convert.rate') }}"><i
                                            data-lucide="git-pull-request"></i><span>{{ __('Convert Rate') }}</span></a>
                                </li>
                            @endcan
                            @can('bill-service-list')
                                <li class="side-nav-item {{ isActive('admin.bill.service*') }}">
                                    <a href="{{ route('admin.bill.service.index') }}"><i
                                            data-lucide="list"></i><span>{{ __('Bill Service List') }}</span></a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcanany

                @canany(['all-bills', 'pending-bills', 'complete-bills', 'return-bills'])
                    <li class="side-nav-item side-nav-dropdown {{ isActive(['admin.bill.history*']) }}">
                        <a href="javascript:void(0);" class="dropdown-link"><i
                                data-lucide="arrow-down-circle"></i><span>{{ __('Bill History') }}</span><span
                                class="right-arrow"><i data-lucide="chevron-down"></i></span></a>
                        <ul class="dropdown-items">
                            @can('pending-bills')
                                <li class="{{ isActive('admin.bill.history.pending') }}"><a
                                        href="{{ route('admin.bill.history.pending') }}"><i
                                            data-lucide="workflow"></i>{{ __('Pending Bill') }}</a></li>
                            @endcan

                            @can('complete-bills')
                                <li class="{{ isActive('admin.bill.history.complete') }}"><a
                                        href="{{ route('admin.bill.history.complete') }}"><i
                                            data-lucide="compass"></i>{{ __('Complete Bill') }}</a></li>
                            @endcan
                            @can('return-bills')
                                <li class="{{ isActive('admin.bill.history.returned') }}"><a
                                        href="{{ route('admin.bill.history.returned') }}"><i
                                            data-lucide="compass"></i>{{ __('Returned Bill') }}</a></li>
                            @endcan
                            @can('all-bills')
                                <li class="{{ isActive('admin.bill.history.all') }}"><a
                                        href="{{ route('admin.bill.history.all') }}"><i
                                            data-lucide="compass"></i>{{ __('All Bill') }}</a></li>
                            @endcan
                        </ul>
                    </li>
                @endcanany

            @endcanany

            @canany(['site-setting', 'email-setting', 'plugin-setting', 'page-manage', 'language-setting',
                'notification-tune-setting', 'site-theme-manage', 'landing-page-manage', 'footer-manage',
                'navigation-manage', 'custom-css'])
                <li class="side-nav-item category-title">
                    <span>
                        {{ __('Appearance & Pages') }}
                    </span>
                </li>
                @canany(['site-setting', 'email-setting', 'plugin-setting', 'page-manage', 'language-setting',
                    'notification-tune-setting'])
                    <li
                        class="side-nav-item side-nav-dropdown {{ isActive(['admin.settings*', 'admin.language*', 'admin.page.setting', 'admin.currency*', 'admin.settings.transactions']) }}">
                        <a href="javascript:void(0);" class="dropdown-link">
                            <i data-lucide="settings"></i>
                            <span>
                                {{ __('Settings') }}
                            </span>
                            <span class="right-arrow">
                                <i data-lucide="chevron-down"></i>
                            </span>
                        </a>
                        <ul class="dropdown-items">
                            @can('site-setting')
                                <li class="{{ isActive('admin.settings.site') }}">
                                    <a href="{{ route('admin.settings.site') }}">
                                        <i data-lucide="settings-2"></i>
                                        {{ __('General Settings') }}
                                    </a>
                                </li>
                                <li class="{{ isActive('admin.settings.transactions') }}">
                                    <a href="{{ route('admin.settings.transactions') }}">
                                        <i data-lucide="arrow-right-left"></i>
                                        {{ __('Transaction Fees & Limits') }}
                                    </a>
                                </li>

                                @can('plugin-setting')
                                    <li class="{{ isActive('admin.settings.plugin', 'system') }}">
                                        <a href="{{ route('admin.settings.plugin', 'system') }}"><i
                                                data-lucide="toy-brick"></i>{{ __('Plugin Settings') }}</a>
                                    </li>
                                @endcan
                                @if (setting('multiple_currency', 'permission'))
                                    @can('currency-manage')
                                        <li class="{{ isActive('admin.currency*') }}">
                                            <a href="{{ route('admin.currency.index') }}">
                                                <i data-lucide="banknote"></i>
                                                {{ __('Currencies') }}
                                            </a>
                                        </li>
                                    @endcan
                                @endif
                            @endcan
                            @can('email-setting')
                                <li class="{{ isActive('admin.settings.mail') }}">
                                    <a href="{{ route('admin.settings.mail') }}">
                                        <i data-lucide="inbox"></i>
                                        {{ __('Email Settings') }}
                                    </a>
                                </li>
                            @endcan
                            @can('language-setting')
                                <li class="{{ isActive('admin.language*') }}">
                                    <a href="{{ route('admin.language.index') }}">
                                        <i data-lucide="languages"></i>
                                        <span>
                                            {{ __('Language Settings') }}
                                        </span>
                                    </a>
                                </li>
                            @endcan
                            @can('page-manage')
                                <li class="side-nav-item {{ isActive('admin.page.setting') }}">
                                    <a href="{{ route('admin.page.setting') }}">
                                        <i data-lucide="layout"></i>
                                        <span>
                                            {{ __('Register Field Settings') }}
                                        </span>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcanany
            @endcanany

            @canany(['site-theme-manage', 'landing-page-manage', 'page-manage', 'footer-manage', 'navigation-manage',
                'custom-css'])
                @canany(['site-theme-manage', 'landing-page-manage'])
                    <li class="side-nav-item side-nav-dropdown  {{ isActive(['admin.theme*', 'admin.custom-css']) }}">
                        <a href="javascript:void(0);" class="dropdown-link">
                            <i data-lucide="palette"></i>
                            <span>
                                {{ __('Appearance') }}
                            </span>
                            <span class="right-arrow">
                                <i data-lucide="chevron-down"></i>
                            </span>
                        </a>
                        <ul class="dropdown-items">
                            @can('site-theme-manage')
                                <li class="{{ isActive('admin.theme.site') }}">
                                    <a href="{{ route('admin.theme.site') }}">
                                        <i data-lucide="roller-coaster"></i>
                                        {{ __('Site Theme') }}
                                    </a>
                                </li>
                            @endcan

                            @can('custom-css')
                                <li class="side-nav-item {{ isActive('admin.custom-css') }}">
                                    <a href="{{ route('admin.custom-css') }}">
                                        <i data-lucide="braces"></i>
                                        <span>
                                            {{ __('Custom CSS') }}
                                        </span>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcanany
                @can('landing-page-manage')
                    <li
                        class="side-nav-item side-nav-dropdown {{ isActive(['admin.page.section.section*', 'admin.page.section.management', 'admin.footer-content']) }}">
                        <a href="javascript:void(0);" class="dropdown-link">
                            <i data-lucide="home"></i>
                            <span>
                                {{ __('Landing Page') }}
                            </span>
                            <span class="right-arrow">
                                <i data-lucide="chevron-down"></i>
                            </span>
                        </a>
                        <ul class="dropdown-items">
                            @can('page-manage')
                                <li class="side-nav-item {{ isActive('admin.page.section.management') }}">
                                    <a href="{{ route('admin.page.section.management') }}"><i
                                            data-lucide="list-end"></i><span>{{ __('Section Management') }}</span></a>
                                </li>
                            @endcan
                            @foreach ($landingSections as $section)
                                <li class="@if (request()->is('admin/page/section/' . $section->code)) active @endif">
                                    <a href="{{ route('admin.page.section.section', $section->code) }}">
                                        <i data-lucide="egg"></i>
                                        {{ $section->name }}
                                    </a>
                                </li>
                            @endforeach
                            @can('footer-manage')
                                <li class="side-nav-item {{ isActive('admin.footer-content') }}">
                                    <a href="{{ route('admin.footer-content') }}">
                                        <i data-lucide="list-end"></i>
                                        <span>
                                            {{ __('Footer Contents') }}
                                        </span>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan

                @can('page-manage')
                    <li class="side-nav-item side-nav-dropdown {{ isActive(['admin.page.edit*', 'admin.page.create']) }}">
                        <a href="javascript:void(0);" class="dropdown-link">
                            <i data-lucide="layout-grid"></i>
                            <span>
                                {{ __('Pages') }}
                            </span>
                            <span class="right-arrow">
                                <i data-lucide="chevron-down"></i>
                            </span>
                        </a>
                        <ul class="dropdown-items">
                            @foreach ($pages as $page)
                                <li class="@if (request()->is('admin/page/edit/' . $page->code)) active @endif">
                                    <a href="{{ route('admin.page.edit', $page->code) }}">
                                        <i data-lucide="egg"></i>
                                        {{ $page->title }}
                                    </a>
                                </li>
                            @endforeach
                            <li class="{{ isActive('admin.page.create') }}">
                                <a href="{{ route('admin.page.create') }}">
                                    <i data-lucide="egg"></i>
                                    {{ __('Add New Page') }}
                                </a>
                            </li>
                        </ul>
                    </li>
                @endcan
                @can('navigation-manage')
                    <li @class([
                        'side-nav-item',
                        'active' =>
                            Route::is('admin.navigation.*') || Route::is('admin.user.navigation.*'),
                    ])>
                        <a href="{{ route('admin.navigation.menu') }}">
                            <i data-lucide="menu"></i>
                            <span>
                                {{ __('Site Navigations') }}
                            </span>
                        </a>
                    </li>
                @endcan
            @endcanany

            @canany(['subscriber-list', 'subscriber-mail-send', 'support-ticket-list', 'support-ticket-action',
                'mail-send-all', 'template-list', 'template-edit', 'template-update', 'template-delete'])
                <li class="side-nav-item category-title">
                    <span>
                        {{ __('Support & Newsletter') }}
                    </span>
                </li>

                @can('mail-send-all')
                    <li class="side-nav-item {{ isActive('admin.mail.send.all') }}">
                        <a href="{{ route('admin.mail.send.all') }}">
                            <i data-lucide="send"></i>
                            <span>
                                {{ __('Mail Send to All') }}
                            </span>
                        </a>
                    </li>
                @endcan

                @canany(['template-list', 'template-edit', 'template-update', 'template-delete'])
                    <li class="side-nav-item {{ isActive('admin.template*') }}">
                        <a href="{{ route('admin.template.index') }}">
                            <i data-lucide="mail"></i>
                            <span>
                                {{ __('Templates') }}
                            </span>
                        </a>
                    </li>
                @endcanany
                @canany(['support-ticket-list', 'support-ticket-action'])
                    <li class="side-nav-item {{ isActive('admin.ticket*') }}">
                        <a href="{{ route('admin.ticket.index') }}">
                            <i data-lucide="wrench"></i>
                            <span>
                                {{ __('Support Tickets') }}
                            </span>
                        </a>
                    </li>
                @endcanany
                @canany(['subscriber-list', 'subscriber-mail-send'])
                    <li class="side-nav-item {{ isActive('admin.subscriber') }}">
                        <a href="{{ route('admin.subscriber') }}">
                            <i data-lucide="mail-open"></i>
                            <span>
                                {{ __('All Subscriber') }}
                            </span>
                        </a>
                    </li>
                @endcanany
            @endcanany

            @canany(['manage-cron-job', 'clear-cache', 'application-details', 'addon-manage'])
                <li class="side-nav-item category-title">
                    <span>
                        {{ __('System') }}
                    </span>
                </li>
                @can('addon-manage')
                    <li class=" side-nav-item {{ isActive('admin.addons.*') }}">
                        <a href="{{ route('admin.addons.index') }}">
                            <i data-lucide="package"></i>
                            <span>
                                {{ __('Addons') }}
                            </span>
                        </a>
                    </li>
                @endcan
                <li
                    class="side-nav-item side-nav-dropdown {{ isActive(['admin.clear-cache', 'admin.application-info', 'admin.cron.jobs.*']) }}">
                    <a href="javascript:void(0);" class="dropdown-link">
                        <i data-lucide="power"></i>
                        <span>
                            {{ __('System') }}
                        </span>
                        <span class="right-arrow">
                            <i data-lucide="chevron-down"></i>
                        </span>
                    </a>
                    <ul class="dropdown-items">
                        @can('manage-cron-job')
                            <li class="{{ isActive('admin.cron.jobs.*') }}">
                                <a href="{{ route('admin.cron.jobs.index') }}">
                                    <i data-lucide="alarm-clock"></i>
                                    <span>
                                        {{ __('Cron Jobs') }}
                                    </span>
                                </a>
                            </li>
                        @endcan
                        @can('clear-cache')
                            <li class="{{ isActive('admin.clear-cache') }}">
                                <a href="{{ route('admin.clear-cache') }}">
                                    <i data-lucide="trash-2"></i>
                                    <span>
                                        {{ __('Clear Cache') }}
                                    </span>
                                </a>
                            </li>
                        @endcan
                        @can('application-details')
                            <li class="{{ isActive('admin.application-info') }}">
                                <a href="{{ route('admin.application-info') }}">
                                    <i data-lucide="app-window"></i>
                                    <span>
                                        {{ __('Application Details') }}
                                    </span>
                                    <span class="badge yellow-color">
                                        {{ config('app.version') }}
                                    </span>
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcanany
        </ul>
    </div>
</div>
