<div class="site-card">
    <div class="site-card-header">
        <h3 class="title">{{ __('Cash Out Settings') }}</h3>
    </div>
    <div class="site-card-body">
        <form action="{{ route('admin.settings.update') }}" method="post">
            @csrf
            <input type="hidden" name="section" value="cashout">

            <div class="row">
                <div class="col-xxl-6">
                    <div class="site-input-groups">
                        <div class="col-label pt-0">{{ __('Min Cashout Amount') }}</div>
                        <div class="input-group joint-input">
                            <input type="text" class="form-control" name="cashout_minimum"
                                value="{{ setting('cashout_minimum', 'cashout') }}">
                            <span class="input-group-text">{{ setting('site_currency', 'global') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-6">
                    <div class="site-input-groups">
                        <div class="col-label pt-0">{{ __('Max Cashout Amount') }}</div>
                        <div class="input-group joint-input">
                            <input type="text" class="form-control" name="cashout_maximum"
                                value="{{ setting('cashout_maximum', 'cashout') }}">
                            <span class="input-group-text">{{ setting('site_currency', 'global') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-6">
                    <div class="site-input-groups">
                        <div class="col-label pt-0">{{ __('Daily Cashout Amount') }}</div>
                        <div class="input-group joint-input">
                            <input type="text" class="form-control" name="cashout_daily_limit"
                                value="{{ setting('cashout_daily_limit', 'cashout') }}">
                            <span class="input-group-text">{{ setting('site_currency', 'global') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-6">
                    <div class="site-input-groups">
                        <div class="col-label pt-0">{{ __('Monthly Cashout Amount') }}</div>
                        <div class="input-group joint-input">
                            <input type="text" class="form-control" name="cashout_monthly_limit"
                                value="{{ setting('cashout_monthly_limit', 'cashout') }}">
                            <span class="input-group-text">{{ setting('site_currency', 'global') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-6">
                    <div class="site-input-groups">
                        <div class="col-label pt-0">{{ __('Charge') }}</div>
                        <div class="site-input-groups position-relative">
                            <div class="position-relative">
                                <input type="text" class="box-input"
                                    value="{{ setting('cashout_charge', 'cashout') }}" name="cashout_charge">
                                <div class="prcntcurr">
                                    <select name="cashout_charge_type" class="form-select">
                                        @foreach (['fixed' => setting('currency_symbol', 'cashout'), 'percentage' => '%'] as $key => $value)
                                            <option @if (setting('cashout_charge_type', 'cashout') == $key) selected @endif
                                                value="{{ $key }}"> {{ $value }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-6">
                    <div class="site-input-groups">
                        <div class="col-label pt-0">{{ __('Agent Commission') }}</div>
                        <div class="site-input-groups position-relative">
                            <div class="position-relative">
                                <input type="text" class="box-input"
                                    value="{{ setting('cashout_agent_commission', 'cashout') }}" name="cashout_agent_commission">
                                <div class="prcntcurr">
                                    <select name="cashout_agent_commission_type" class="form-select">
                                        @foreach (['fixed' => setting('currency_symbol', 'cashout'), 'percentage' => '%'] as $key => $value)
                                            <option @if (setting('cashout_agent_commission_type', 'cashout') == $key) selected @endif
                                                value="{{ $key }}"> {{ $value }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row justify-content-end">
                <div class="col-sm-4">
                    <button type="submit" class="site-btn-sm primary-btn w-100">
                        {{ __('Save Changes') }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
