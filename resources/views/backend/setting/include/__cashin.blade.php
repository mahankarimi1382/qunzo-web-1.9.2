<div class="site-card">
    <div class="site-card-header">
        <h3 class="title">{{ __('Cash In Settings') }}</h3>
    </div>
    <div class="site-card-body">
        <form action="{{ route('admin.settings.update') }}" method="post">
            @csrf
            <input type="hidden" name="section" value="cashin">

            <div class="row">
                <div class="col-xxl-6">
                    <div class="site-input-groups">
                        <div class="col-label pt-0">{{ __('Min Cash In Amount') }}</div>
                        <div class="input-group joint-input">
                            <input type="text" class="form-control" name="cashin_minimum"
                                value="{{ setting('cashin_minimum', 'cashin') }}">
                            <span class="input-group-text">{{ setting('site_currency', 'global') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-6">
                    <div class="site-input-groups">
                        <div class="col-label pt-0">{{ __('Max Cash In Amount') }}</div>
                        <div class="input-group joint-input">
                            <input type="text" class="form-control" name="cashin_maximum"
                                value="{{ setting('cashin_maximum', 'cashin') }}">
                            <span class="input-group-text">{{ setting('site_currency', 'global') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-6">
                    <div class="site-input-groups">
                        <div class="col-label pt-0">{{ __('Daily Cash In Amount') }}</div>
                        <div class="input-group joint-input">
                            <input type="text" class="form-control" name="cashin_daily_limit"
                                value="{{ setting('cashin_daily_limit', 'cashin') }}">
                            <span class="input-group-text">{{ setting('site_currency', 'global') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-6">
                    <div class="site-input-groups">
                        <div class="col-label pt-0">{{ __('Monthly Cash In Amount') }}</div>
                        <div class="input-group joint-input">
                            <input type="text" class="form-control" name="cashin_monthly_limit"
                                value="{{ setting('cashin_monthly_limit', 'cashin') }}">
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
                                    value="{{ setting('cashin_charge', 'cashin') }}" name="cashin_charge">
                                <div class="prcntcurr">
                                    <select name="cashin_charge_type" class="form-select">
                                        @foreach (['fixed' => setting('currency_symbol', 'cashin'), 'percentage' => '%'] as $key => $value)
                                            <option @if (setting('cashin_charge_type', 'cashin') == $key) selected @endif
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
                                    value="{{ setting('cashin_agent_commission', 'cashin') }}" name="cashin_agent_commission">
                                <div class="prcntcurr">
                                    <select name="cashin_agent_commission_type" class="form-select">
                                        @foreach (['fixed' => setting('currency_symbol', 'cashin'), 'percentage' => '%'] as $key => $value)
                                            <option @if (setting('cashin_agent_commission_type', 'cashin') == $key) selected @endif
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
