@php
    $fields = config('setting.p2p');
@endphp
<div class="col-xl-6 col-lg-12 col-md-12 col-12">
    <div class="site-card">
        <div class="site-card-header">
            <h3 class="title">{{ $fields['title'] }}</h3>
        </div>
        <div class="site-card-body">
            @include('backend.setting.site_setting.include.form.__open_action')

            <div class="site-input-groups row mb-0">
                <label for="" class="col-sm-4 col-label">{{ __('Ads Poster Fee') }}
                    <i data-lucide="info" data-bs-toggle="tooltip" title=""
                        data-bs-original-title="{{ __('Fee will be converted to the ad\'s asset currency when users create ads in a different currency.') }}"></i>
                </label>
                <div class="col-sm-8">
                    <div class="site-input-groups position-relative">
                        <div class="position-relative">
                            <input type="text" class="box-input" value="{{ oldSetting('ads_poster_fee', 'p2p') }}"
                                name="ads_poster_fee">
                            <div class="prcntcurr">
                                <select name="ads_poster_fee_type" class="form-select">
                                    <option value="fixed" selected>{{ setting('currency_symbol', 'global') }}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="site-input-groups row mb-0">
                <label for="" class="col-sm-4 col-label">{{ __('Ads Approval Required') }}</label>
                <div class="col-sm-8">
                    <div class="switch-field same-type">
                        <input type="radio" id="ads_approval_required_yes" name="ads_approval_required" value="1"
                            @if (oldSetting('ads_approval_required', 'p2p')) checked @endif>
                        <label for="ads_approval_required_yes">{{ __('Yes') }}</label>
                        <input type="radio" id="ads_approval_required_no" name="ads_approval_required" value="0"
                            @if (!oldSetting('ads_approval_required', 'p2p')) checked @endif>
                        <label for="ads_approval_required_no">{{ __('No') }}</label>
                    </div>
                </div>
            </div>

            <div class="site-input-groups row mb-0">
                <label for="" class="col-sm-4 col-label">{{ __('Order Fee Percent') }}</label>
                <div class="col-sm-8">
                    <div class="site-input-groups position-relative">
                        <input type="text" class="box-input" value="{{ oldSetting('order_fee_percent', 'p2p') }}"
                            name="order_fee_percent">
                    </div>
                </div>
            </div>

            <div class="site-input-groups row mb-4">
                <label for="" class="col-sm-4 col-label">{{ __('Seller Release Grace Minutes') }}
                    <i data-lucide="info" data-bs-toggle="tooltip" title=""
                        data-bs-original-title="{{ __('After buyer marks paid, if seller does not release within this many minutes, order is auto-disputed.') }}"></i>
                </label>
                <div class="col-sm-8">
                    <div class="input-group joint-input">
                        <input type="number" min="1" class="form-control"
                            value="{{ oldSetting('seller_release_grace_minutes', 'p2p') }}"
                            name="seller_release_grace_minutes">
                        <span class="input-group-text">{{ __('Minutes') }}</span>
                    </div>
                </div>
            </div>

            <div class="site-input-groups row mb-4">
                <label for=""
                    class="col-sm-12 col-label"><strong>{{ __('Ads Creation Eligibility Thresholds') }}</strong></label>
            </div>

            <div class="site-input-groups row mb-0">
                <label for="" class="col-sm-4 col-label">{{ __('Min Transactions Success Rate') }}
                    <i data-lucide="info" data-bs-toggle="tooltip" title=""
                        data-bs-original-title="{{ __('Uses all user transactions. Success rate = successful transactions divided by total transactions.') }}"></i>
                </label>
                <div class="col-sm-8">
                    <div class="input-group joint-input">
                        <input type="number" min="0" max="100" step="0.01" class="form-control"
                            value="{{ oldSetting('min_transactions_success_rate', 'p2p') }}"
                            name="min_transactions_success_rate">
                        <span class="input-group-text">%</span>
                    </div>
                </div>
            </div>

            <div class="site-input-groups row mb-4">
                <label for="" class="col-sm-4 col-label">{{ __('Min P2P Order Completion Rate') }}
                    <i data-lucide="info" data-bs-toggle="tooltip" title=""
                        data-bs-original-title="{{ __('Uses terminal P2P orders (completed, cancelled, expired, disputed).') }}"></i>
                </label>
                <div class="col-sm-8">
                    <div class="input-group joint-input">
                        <input type="number" min="0" max="100" step="0.01" class="form-control"
                            value="{{ oldSetting('min_completion_rate', 'p2p') }}" name="min_completion_rate">
                        <span class="input-group-text">%</span>
                    </div>
                </div>
            </div>

            <div class="site-input-groups row mb-4">
                <label for="" class="col-sm-4 col-label">{{ __('Min Orders Required') }}
                    <i data-lucide="info" data-bs-toggle="tooltip" title=""
                        data-bs-original-title="{{ __('Minimum terminal P2P orders required before a user can create ads.') }}"></i>
                </label>
                <div class="col-sm-8">
                    <div class="site-input-groups position-relative">
                        <input type="number" min="0" step="1" class="box-input"
                            value="{{ oldSetting('min_orders_required', 'p2p') }}" name="min_orders_required">
                    </div>
                </div>
            </div>

            <div class="site-input-groups row mb-4">
                <label for=""
                    class="col-sm-12 col-label"><strong>{{ __('Verified Trader Eligibility Thresholds') }}</strong></label>
            </div>

            <div class="site-input-groups row mb-4">
                <label for="" class="col-sm-4 col-label">{{ __('Min Completed P2P Orders') }}
                    <i data-lucide="info" data-bs-toggle="tooltip" title=""
                        data-bs-original-title="{{ __('Minimum completed P2P orders required before a user can apply for Verified Trader status.') }}"></i>
                </label>
                <div class="col-sm-8">
                    <div class="site-input-groups position-relative">
                        <input type="number" min="0" step="1" class="box-input"
                            value="{{ oldSetting('verified_trader_min_completed_orders', 'p2p') }}"
                            name="verified_trader_min_completed_orders">
                    </div>
                </div>
            </div>

            <div class="site-input-groups row mb-4">
                <label for="" class="col-sm-4 col-label">{{ __('Min P2P Order Completion Rate') }}
                    <i data-lucide="info" data-bs-toggle="tooltip" title=""
                        data-bs-original-title="{{ __('Minimum P2P order completion rate required to apply for Verified Trader status.') }}"></i>
                </label>
                <div class="col-sm-8">
                    <div class="input-group joint-input">
                        <input type="number" min="0" max="100" step="0.01" class="form-control"
                            value="{{ oldSetting('verified_trader_min_completion_rate', 'p2p') }}"
                            name="verified_trader_min_completion_rate">
                        <span class="input-group-text">%</span>
                    </div>
                </div>
            </div>

            @include('backend.setting.site_setting.include.form.__close_action')
        </div>
    </div>
</div>
