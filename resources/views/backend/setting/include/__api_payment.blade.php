<div class="site-card">
    <div class="site-card-header">
        <h3 class="title">{{ __('API Payment Settings') }}</h3>
    </div>
    <div class="site-card-body">
        <form action="{{ route('admin.settings.update') }}" method="post">
            @csrf
            <input type="hidden" name="section" value="api_payment">

            <div class="row">
                <div class="col-xxl-12">
                    <div class="site-input-groups">
                        <div class="col-label pt-0">{{ __('Charge') }}</div>
                        <div class="site-input-groups position-relative">
                            <div class="position-relative">
                                <input type="text" class="box-input"
                                    value="{{ oldSetting('api_payment_charge', 'api_payment') }}" name="api_payment_charge">
                                <div class="prcntcurr">
                                    <select name="api_payment_charge_type" class="form-select" id="">
                                        @foreach (['fixed' => setting('currency_symbol', 'api_payment'), 'percentage' => '%'] as $key => $value)
                                            <option @if (oldSetting('api_payment_charge_type', 'api_payment') == $key) selected @endif
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
