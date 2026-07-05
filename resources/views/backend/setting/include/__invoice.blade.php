<div class="site-card">
    <div class="site-card-header">
        <h3 class="title">{{ __('Invoice Settings') }}</h3>
    </div>
    <div class="site-card-body">
        <form action="{{ route('admin.settings.update') }}" method="post">
            @csrf
            <input type="hidden" name="section" value="invoice">

            <div class="row">
                <div class="col-xxl-12">
                    <div class="site-input-groups">
                        <div class="col-label pt-0">{{ __('Daily Invoice Limit') }}</div>
                        <div class="input-group joint-input">
                            <input type="text" class="form-control" name="invoice_daily_limit"
                                value="{{ setting('invoice_daily_limit', 'invoice') }}">
                            <span class="input-group-text">{{ __('Times') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-12">
                    <div class="site-input-groups">
                        <div class="col-label pt-0">{{ __('Charge') }}</div>
                        <div class="site-input-groups position-relative">
                            <div class="position-relative">
                                <input type="text" class="box-input" value="{{ setting('invoice_charge', 'invoice') }}"
                                    name="invoice_charge">
                                <div class="prcntcurr">
                                    <select name="invoice_charge_type" class="form-select">
                                        @foreach (['fixed' => setting('currency_symbol', 'invoice'), 'percentage' => '%'] as $key => $value)
                                            <option @if (setting('invoice_charge_type', 'invoice') == $key) selected @endif
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
