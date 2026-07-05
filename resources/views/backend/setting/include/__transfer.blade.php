<div class="site-card">
    <div class="site-card-header">
        <h3 class="title">{{ __('Transfer Settings') }}</h3>
    </div>
    <div class="site-card-body">
        <form action="{{ route('admin.settings.update') }}" method="post">
            @csrf
            <input type="hidden" name="section" value="transfer">

            <div class="row">
                <div class="col-xxl-6">
                    <div class="site-input-groups">
                        <div class="col-label pt-0">{{ __('Min Transfer Amount') }}</div>
                        <div class="input-group joint-input">
                            <input type="text" class="form-control" name="transfer_minimum"
                                value="{{ oldSetting('transfer_minimum', 'transfer') }}">
                            <span class="input-group-text">{{ setting('site_currency', 'global') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-6">
                    <div class="site-input-groups">
                        <div class="col-label pt-0">{{ __('Max Transfer Amount') }}</div>
                        <div class="input-group joint-input">
                            <input type="text" class="form-control" name="transfer_maximum"
                                value="{{ oldSetting('transfer_maximum', 'transfer') }}">
                            <span class="input-group-text">{{ setting('site_currency', 'global') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-12">
                    <div class="site-input-groups">
                        <div class="col-label pt-0">{{ __('Daily Transfer Amount') }}</div>
                        <div class="input-group joint-input">
                            <input type="text" class="form-control" name="transfer_daily_limit"
                                value="{{ oldSetting('transfer_daily_limit', 'transfer') }}">
                            <span class="input-group-text">{{ setting('site_currency', 'global') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-12">
                    <div class="site-input-groups">
                        <div class="col-label pt-0">{{ __('Charge') }}</div>
                        <div class="site-input-groups position-relative">
                            <div class="position-relative">
                                <input type="text" class="box-input"
                                    value="{{ oldSetting('transfer_charge', 'transfer') }}" name="transfer_charge">
                                <div class="prcntcurr">
                                    <select name="transfer_charge_type" class="form-select" id="">
                                        @foreach (['fixed' => setting('currency_symbol', 'transfer'), 'percentage' => '%'] as $key => $value)
                                            <option @if (oldSetting('transfer_charge_type', 'transfer') == $key) selected @endif
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
