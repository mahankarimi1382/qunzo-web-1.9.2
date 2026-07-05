<div class="site-card">
    <div class="site-card-header">
        <h3 class="title">{{ __('Make Payment Settings') }}</h3>
    </div>
    <div class="site-card-body">
        <form action="{{ route('admin.settings.update') }}" method="post">
            @csrf
            <input type="hidden" name="section" value="make_payment">

            <div class="row">
                <div class="col-xxl-6">
                    <div class="site-input-groups">
                        <div class="col-label pt-0">{{ __('Min Payment Amount') }}</div>
                        <div class="input-group joint-input">
                            <input type="text" class="form-control" name="payment_minimum"
                                value="{{ setting('payment_minimum', 'make_payment') }}">
                            <span class="input-group-text">{{ setting('site_currency', 'global') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-6">
                    <div class="site-input-groups">
                        <div class="col-label pt-0">{{ __('Max Payment Amount') }}</div>
                        <div class="input-group joint-input">
                            <input type="text" class="form-control" name="payment_maximum"
                                value="{{ setting('payment_maximum', 'make_payment') }}">
                            <span class="input-group-text">{{ setting('site_currency', 'global') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-12">
                    <div class="site-input-groups">
                        <div class="col-label pt-0">{{ __('User Charge') }}</div>
                        <div class="site-input-groups position-relative">
                            <div class="position-relative">
                                <input type="text" class="box-input"
                                    value="{{ oldSetting('user_make_payment_charge', 'make_payment') }}"
                                    name="user_make_payment_charge">
                                <div class="prcntcurr">
                                    <select name="user_make_payment_charge_type" class="form-select" id="">
                                        @foreach (['fixed' => setting('currency_symbol', 'make_payment'), 'percentage' => '%'] as $key => $value)
                                            <option @if (oldSetting('user_make_payment_charge_type', 'make_payment') == $key) selected @endif
                                                value="{{ $key }}"> {{ $value }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-12">
                    <div class="site-input-groups">
                        <div class="col-label pt-0">{{ __('Merchant Charge') }}</div>
                        <div class="site-input-groups position-relative">
                            <div class="position-relative">
                                <input type="text" class="box-input"
                                    value="{{ oldSetting('merchant_make_payment_charge', 'make_payment') }}"
                                    name="merchant_make_payment_charge">
                                <div class="prcntcurr">
                                    <select name="merchant_make_payment_charge_type" class="form-select" id="">
                                        @foreach (['fixed' => setting('currency_symbol', 'make_payment'), 'percentage' => '%'] as $key => $value)
                                            <option @if (oldSetting('merchant_make_payment_charge_type', 'make_payment') == $key) selected @endif
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
