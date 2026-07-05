<div class="site-card">
    <div class="site-card-header">
        <h3 class="title">{{ __('Request Money Settings') }}</h3>
    </div>
    <div class="site-card-body">
        <form action="{{ route('admin.settings.update') }}" method="post">
            @csrf
            <input type="hidden" name="section" value="request_money">

            <div class="row">
                <div class="col-xxl-6">
                    <div class="site-input-groups">
                        <div class="col-label pt-0">{{ __('Min Request Money Amount') }}</div>
                        <div class="input-group joint-input">
                            <input type="text" class="form-control" name="request_money_minimum"
                                value="{{ setting('request_money_minimum', 'request_money') }}">
                            <span class="input-group-text">{{ setting('site_currency', 'global') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-6">
                    <div class="site-input-groups">
                        <div class="col-label pt-0">{{ __('Max Request Money Amount') }}</div>
                        <div class="input-group joint-input">
                            <input type="text" class="form-control" name="request_money_maximum"
                                value="{{ setting('request_money_maximum', 'request_money') }}">
                            <span class="input-group-text">{{ setting('site_currency', 'global') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-12">
                    <div class="site-input-groups">
                        <div class="col-label pt-0">{{ __('Daily Request Money Limit') }}</div>
                        <div class="input-group joint-input">
                            <input type="text" class="form-control" name="request_money_daily_limit"
                                value="{{ setting('request_money_daily_limit', 'request_money') }}">
                            <span class="input-group-text">{{ __('Times') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-12">
                    <div class="site-input-groups">
                        <div class="col-label pt-0">{{ __('Charge') }}</div>
                        <div class="site-input-groups position-relative">
                            <div class="position-relative">
                                <input type="text" class="box-input"
                                    value="{{ setting('request_money_charge', 'request_money') }}"
                                    name="request_money_charge">
                                <div class="prcntcurr">
                                    <select name="request_money_charge_type" class="form-select">
                                        @foreach (['fixed' => setting('currency_symbol', 'request_money'), 'percentage' => '%'] as $key => $value)
                                            <option @if (setting('request_money_charge_type', 'request_money') == $key) selected @endif
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
