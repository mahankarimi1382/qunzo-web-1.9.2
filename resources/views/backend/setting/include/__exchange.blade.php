<div class="site-card">
    <div class="site-card-header">
        <h3 class="title">{{ __('Exchange Settings') }}</h3>
    </div>
    <div class="site-card-body">
        <form action="{{ route('admin.settings.update') }}" method="post">
            @csrf
            <input type="hidden" name="section" value="exchange">

            <div class="row">
                <div class="col-xxl-6">
                    <div class="site-input-groups">
                        <div class="col-label pt-0">{{ __('Min Exchange Amount') }}</div>
                        <div class="input-group joint-input">
                            <input type="text" class="form-control" name="exchange_minimum"
                                value="{{ setting('exchange_minimum', 'exchange') }}">
                            <span class="input-group-text">{{ setting('site_currency', 'global') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-6">
                    <div class="site-input-groups">
                        <div class="col-label pt-0">{{ __('Max Exchange Amount') }}</div>
                        <div class="input-group joint-input">
                            <input type="text" class="form-control" name="exchange_maximum"
                                value="{{ setting('exchange_maximum', 'exchange') }}">
                            <span class="input-group-text">{{ setting('site_currency', 'global') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-12">
                    <div class="site-input-groups">
                        <div class="col-label pt-0">{{ __('Daily Exchange Limit') }}</div>
                        <div class="input-group joint-input">
                            <input type="text" class="form-control" name="exchange_daily_limit"
                                value="{{ setting('exchange_daily_limit', 'exchange') }}">
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
                                    value="{{ setting('exchange_charge', 'exchange') }}" name="exchange_charge">
                                <div class="prcntcurr">
                                    <select name="exchange_charge_type" class="form-select">
                                        @foreach (['fixed' => setting('currency_symbol', 'exchange'), 'percentage' => '%'] as $key => $value)
                                            <option @if (setting('exchange_charge_type', 'exchange') == $key) selected @endif
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
