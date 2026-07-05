@php
    $fields = config('setting.gift_card');
@endphp
<div class="col-xl-6 col-lg-12 col-md-12 col-12">
    <div class="site-card">
        <div class="site-card-header">
            <h3 class="title">{{ $fields['title'] }}</h3>
        </div>
        <div class="site-card-body">
            @include('backend.setting.site_setting.include.form.__open_action')

            <div class="site-input-groups row mb-0">
                <label for="" class="col-sm-4 col-label">{{ __('Gift Card Order Charge') }}</label>
                <div class="col-sm-8">
                    <div class="site-input-groups">
                        <div class="input-group joint-input">
                            <input type="text" name="giftcard_order_charge"
                                value="{{ oldSetting('giftcard_order_charge', 'gift_card') }}" class="form-control">
                            <span class="input-group-text">{{ setting('site_currency', 'global') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            @include('backend.setting.site_setting.include.form.__close_action')
        </div>
    </div>
</div>
