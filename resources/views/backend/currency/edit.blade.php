@extends('backend.layouts.app')

@section('title')
    {{ __('Edit Currency') }}
@endsection

@section('content')
    <div class="main-content">
        <div class="container-fluid">
            <div class="page-title">
                <div class="container-fluid">
                    <div class="row justify-content-center">
                        <div class="col-xl-8">
                            <div class="title-content">
                                <h2 class="title">{{ __('Edit Currency') }}</h2>
                                <div>
                                    <a href="{{ route('admin.currency.index') }}" class="title-btn">
                                        <i icon-name="list"></i>
                                        {{ __('List') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-xl-8">
                    <div class="site-card">
                        <div class="site-card-body">
                            <form action="{{ route('admin.currency.update', $currencyInfo->id) }}" class="row"
                                method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-xl-3">
                                        <div class="site-input-groups">
                                            <label class="box-input-label" for="logo">
                                                {{ __('Currency Logo:') }} <small>({{ __('Optional') }})</small>
                                            </label>
                                            <div class="wrap-custom-file">
                                                <input type="file" name="icon" id="logo"
                                                    accept=".gif, .jpg, .png, .webp">
                                                <label for="logo" class="file-ok"
                                                    style="background-image: url({{ asset($currencyInfo->icon) }})">
                                                    <img class="upload-icon"
                                                        src="{{ asset('global/materials/upload.svg') }}" alt="">
                                                    <span>
                                                        {{ __('Upload Icon') }}
                                                    </span>
                                                </label>
                                            </div>
                                            @error('icon')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-6">
                                    <div class="site-input-groups">
                                        <label class="box-input-label">{{ __('Name') }}:
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="box-input" name="name"
                                            value="{{ $currencyInfo->name }}">
                                        @error('name')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-xl-6">
                                    <div class="site-input-groups">
                                        <label class="box-input-label">{{ __('Symbol :') }}
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="box-input" name="symbol"
                                            value="{{ $currencyInfo->symbol }}">
                                        @error('symbol')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-xl-6">
                                    <div class="site-input-groups">
                                        <label class="box-input-label">
                                            {{ __('Code:') }} <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="box-input" name="code" id="code"
                                            value="{{ $currencyInfo->code }}">
                                        @error('code')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-xl-6">
                                    <div class="site-input-groups row">
                                        <div class="col-xl-12">
                                            <label class="box-input-label">
                                                {{ __('Conversion Rate:') }} <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group joint-input">
                                                <span class="input-group-text">{{ "1 $currency =" }} </span>
                                                <input type="text" name="conversion_rate" data-validate="decimal"
                                                    class="form-control" value="{{ $currencyInfo->conversion_rate }}">
                                                <span class="input-group-text"
                                                    id="currency-selected">{{ $currencyInfo->code }}</span>
                                            </div>
                                            @error('conversion_rate')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="col-xl-6">
                                    <div class="site-input-groups">
                                        <label class="box-input-label">{{ __('Status:') }}
                                            <span class="text-danger">*</span>
                                        </label>
                                        <div class="switch-field same-type">
                                            <input type="radio" id="radio-five" name="status" value="active"
                                                @checked ($currencyInfo->status == App\Enums\CurrencyStatus::Active)>
                                            <label for="radio-five">{{ __('Active') }}</label>
                                            <input type="radio" id="radio-six" name="status" value="inactive"
                                                @checked ($currencyInfo->status == App\Enums\CurrencyStatus::Inactive)>
                                            <label for="radio-six">{{ __('Inactive') }}</label>
                                        </div>
                                        @error('status')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-xl-6">
                                    <div class="site-input-groups">
                                        <label class="box-input-label">{{ __('Currency Type:') }}
                                            <span class="text-danger">*</span>
                                        </label>
                                        <div class="switch-field same-type">
                                            <input type="radio" id="fiat-currency" name="type" value="fiat"
                                                @checked ($currencyInfo->type == App\Enums\CurrencyType::Fiat)>
                                            <label for="fiat-currency">{{ __('Fiat') }}</label>
                                            <input type="radio" id="crypto-currency" name="type" value="crypto"
                                                @checked ($currencyInfo->type == App\Enums\CurrencyType::Crypto)>
                                            <label for="crypto-currency">{{ __('Crypto') }}</label>
                                        </div>
                                        @error('type')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-xl-12">
                                    <button type="submit" class="site-btn primary-btn w-100">
                                        {{ __('Submit') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        "use strict";
        $("#code").on('change', function() {
            $('#currency-selected').text(this.value);
        });
    </script>

    <script>
        "use strict";

        function toggleChargeSections() {
            const exchangeChargeStatus = document.querySelector('input[name="exchange_charge_status"]:checked').value;
            const exchangeSection = document.getElementById('exchange-charges-section');
            exchangeSection.style.display = exchangeChargeStatus === '1' ? 'block' : 'none';

            const transferChargeStatus = document.querySelector('input[name="transfer_charge_status"]:checked').value;
            const transferSection = document.getElementById('transfer-charges-section');
            transferSection.style.display = transferChargeStatus === '1' ? 'block' : 'none';
        }

        document.querySelectorAll('input[name="exchange_charge_status"]').forEach(radio => {
            radio.addEventListener('change', toggleChargeSections);
        });

        document.querySelectorAll('input[name="transfer_charge_status"]').forEach(radio => {
            radio.addEventListener('change', toggleChargeSections);
        });

        document.addEventListener('DOMContentLoaded', toggleChargeSections);
    </script>
@endsection
