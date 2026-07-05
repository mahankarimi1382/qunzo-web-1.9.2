<div class="site-card">
    <div class="site-card-header">
        <h3 class="title">{{ __('Gift Settings') }}</h3>
    </div>
    <div class="site-card-body">
        <form action="{{ route('admin.settings.update') }}" method="post">
            @csrf
            <input type="hidden" name="section" value="gift">

            <div class="row">
                <div class="col-xxl-6">
                    <div class="site-input-groups">
                        <div class="col-label pt-0">{{ __('Min Gift Amount') }}</div>
                        <div class="input-group joint-input">
                            <input type="text" class="form-control" name="gift_minimum"
                                value="{{ setting('gift_minimum', 'gift') }}">
                            <span class="input-group-text">{{ setting('site_currency', 'global') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-6">
                    <div class="site-input-groups">
                        <div class="col-label pt-0">{{ __('Max Gift Amount') }}</div>
                        <div class="input-group joint-input">
                            <input type="text" class="form-control" name="gift_maximum"
                                value="{{ setting('gift_maximum', 'gift') }}">
                            <span class="input-group-text">{{ setting('site_currency', 'global') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-6">
                    <div class="site-input-groups">
                        <div class="col-label pt-0">{{ __('Daily Gift Limit') }}</div>
                        <div class="input-group joint-input">
                            <input type="text" class="form-control" name="gift_daily_limit"
                                value="{{ setting('gift_daily_limit', 'gift') }}">
                            <span class="input-group-text">{{ __('Times') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-6">
                    <div class="site-input-groups">
                        <div class="col-label pt-0">{{ __('Charge') }}</div>
                        <div class="site-input-groups position-relative">
                            <div class="position-relative">
                                <input type="text" class="box-input" value="{{ setting('gift_charge', 'gift') }}"
                                    name="gift_charge">
                                <div class="prcntcurr">
                                    <select name="gift_charge_type" class="form-select">
                                        @foreach (['fixed' => setting('currency_symbol', 'gift'), 'percentage' => '%'] as $key => $value)
                                            <option @if (setting('gift_charge_type', 'gift') == $key) selected @endif
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
