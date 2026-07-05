@extends('backend.deposit.index')
@section('title')
    {{ __(ucwords($type) . ' Method') }}
@endsection
@section('deposit_content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-xl-8">
                <div class="site-card">
                    <div class="site-card-body">
                        <form action="{{ route('admin.deposit.method.update', $method->id) }}" class="row" method="post"
                            enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="type" value="{{ $type }}">
                            <div class="col-xl-12">
                                <div class="site-input-groups">
                                    <label class="box-input-label" for="">{{ __('Upload Logo:') }}</label>
                                    <div class="wrap-custom-file">
                                        <input type="file" name="logo" id="schema-icon" accept=".gif, .jpg, .png" />
                                        <label for="schema-icon" class="file-ok"
                                            style="background-image: url({{ asset($method->gateway_logo) }})">
                                            <img class="upload-icon" src="{{ asset('global/materials/upload.svg') }}"
                                                alt="" />
                                            <span>{{ __('Update Logo') }}</span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            @if ($type == 'auto')
                                <div class="col-xl-6">
                                    <div class="site-input-groups">
                                        <label class="box-input-label" for="">{{ __('Automatic Gateway:') }}</label>
                                        <select name="gateway_id" class="form-select" id="gateway-select">
                                            @foreach ($gateways as $gateway)
                                                <option data-currencies="{{ $gateway->supported_currencies }}"
                                                    data-gatewayCode="{{ $gateway->gateway_code }}"
                                                    value="{{ $gateway->id }}" @selected($method->gateway_id == $gateway->id)>
                                                    {{ $gateway->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-xl-6">
                                    <div class="site-input-groups">
                                        <label class="box-input-label"
                                            for="">{{ __('Gateway Supported Currency:') }}</label>
                                        <select name="currency" class="form-select" id="currency">
                                            @foreach (json_decode($supported_currencies) as $currency)
                                                <option value="{{ $currency }}" @selected($currency == $method->currency)>
                                                    {{ $currency }} </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            @endif

                            <div class="col-xl-6">
                                <div class="site-input-groups">
                                    <label class="box-input-label" for="">{{ __('Name:') }}</label>
                                    <input type="text" class="box-input" name="name" value="{{ $method->name }}" />
                                </div>
                            </div>
                            <div class="col-xl-6">
                                <div class="site-input-groups">
                                    <label class="box-input-label" for="">{{ __('Code Name') }}</label>
                                    <input type="text" class="box-input" disabled value="{{ $method->gateway_code }}" />
                                </div>
                            </div>
                            @if ($type == 'manual')
                                <div class="col-xl-6">
                                    <div class="site-input-groups">

                                        <label class="box-input-label" for="">{{ __('Currency:') }}</label>
                                        <select name="currency" id="currency" class="form-select">
                                            <option disabled>{{ __('Select Currency') }}</option>
                                            <option value="{{ $currency }}">{{ $currency }}</option>
                                            @foreach ($currencies as $currency)
                                                <option value="{{ $currency->code }}" @selected($method->currency == $currency->code)>
                                                    {{ $currency->code }}</option>
                                            @endforeach
                                        </select>
                                        @error('currency')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            @endif
                            <div class="col-xl-6">
                                <div class="site-input-groups">
                                    <label class="box-input-label" for="">{{ __('Currency Symbol:') }}</label>
                                    <input type="text" class="box-input" value="{{ $method->currency_symbol }}"
                                        name="currency_symbol" />
                                </div>
                            </div>

                            <div class="col-xl-6">
                                <div class="site-input-groups position-relative">
                                    <label class="box-input-label" for="">{{ __('Charges:') }}</label>
                                    <div class="position-relative">
                                        <input type="text" class="box-input" data-validate="decimal" name="charge"
                                            value="{{ formatAmount($method->charge, $method->currency, thousandSeparatorRemove: true) }}" />
                                        <div class="prcntcurr">
                                            <select name="charge_type" class="form-select">
                                                <option value="percentage"
                                                    @if ($method->charge_type == 'percentage') selected @endif>{{ __('%') }}
                                                </option>
                                                <option value="fixed" @if ($method->charge_type == 'fixed') selected @endif>
                                                    {{ $currencySymbol }}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-6">
                                <div class="site-input-groups">
                                    <label class="box-input-label" for="">{{ __('Minimum Deposit:') }}</label>
                                    <div class="input-group joint-input">
                                        <input type="text" name="minimum_deposit" data-validate="decimal"
                                            class="form-control"
                                            value="{{ formatAmount($method->minimum_deposit, $method->currency, thousandSeparatorRemove: true) }}" />
                                        <span class="input-group-text"></span>
                                    </div>

                                </div>
                            </div>
                            <div class="col-xl-6">
                                <div class="site-input-groups">
                                    <label class="box-input-label" for="">{{ __('Maximum Deposit:') }}</label>
                                    <div class="input-group joint-input">
                                        <input type="text" name="maximum_deposit" data-validate="decimal"
                                            class="form-control"
                                            value="{{ formatAmount($method->maximum_deposit, $method->currency, thousandSeparatorRemove: true) }}" />
                                        <span class="input-group-text"></span>
                                    </div>
                                </div>
                            </div>

                            @if ($type == 'manual')
                                <div class="col-xl-12">
                                    <a href="javascript:void(0)" id="generate"
                                        class="site-btn-xs primary-btn mb-3">{{ __('Add Field option') }}</a>
                                </div>

                                <div class="addOptions">
                                    @foreach ($method->field_options ?? [] as $key => $value)
                                        <div class="mb-4">
                                            <div class="option-remove-row row">
                                                <div class="col-xl-4 col-lg-6 col-md-6 col-sm-6 col-12">
                                                    <div class="site-input-groups">
                                                        <input name="field_options[{{ $key }}][name]"
                                                            class="box-input" type="text"
                                                            value="{{ $value['name'] }}" required
                                                            placeholder="{{ __('Field Name') }}">
                                                    </div>
                                                </div>

                                                <div class="col-xl-4 col-lg-6 col-md-6 col-sm-6 col-12">
                                                    <div class="site-input-groups">
                                                        <select name="field_options[{{ $key }}][type]"
                                                            class="form-select form-select-lg mb-3">
                                                            <option value="text"
                                                                @if ($value['type'] == 'text') selected @endif>
                                                                {{ __('Input Text') }}
                                                            </option>
                                                            <option value="textarea"
                                                                @if ($value['type'] == 'textarea') selected @endif>
                                                                {{ __('Textarea') }}
                                                            </option>
                                                            <option value="file"
                                                                @if ($value['type'] == 'file') selected @endif>
                                                                {{ __('File upload') }}
                                                            </option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-12">
                                                    <div class="site-input-groups mb-0">
                                                        <select name="field_options[{{ $key }}][validation]"
                                                            class="form-select form-select-lg mb-1">
                                                            <option value="required"
                                                                @if ($value['validation'] == 'required') selected @endif>
                                                                {{ __('Required') }}
                                                            </option>
                                                            <option value="nullable"
                                                                @if ($value['validation'] == 'nullable') selected @endif>
                                                                {{ __('Optional') }}
                                                            </option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-xl-1 col-lg-6 col-md-6 col-sm-6 col-12">
                                                    <button class="delete-option-row delete_desc" type="button">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="col-xl-12">
                                    <div class="site-input-groups fw-normal">
                                        <label for=""
                                            class="box-input-label">{{ __('Payment Details:') }}</label>
                                        <div class="site-editor">
                                            <textarea class="summernote" name="payment_details">{!! $method->payment_details !!}</textarea>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <div class="col-xl-6">
                                <div class="site-input-groups">
                                    <label class="box-input-label" for="">{{ __('Status:') }}</label>
                                    <div class="switch-field same-type">
                                        <input type="radio" id="radio-five" name="status" value="1"
                                            @if ($method->status) checked @endif />
                                        <label for="radio-five">{{ __('Active') }}</label>
                                        <input type="radio" id="radio-six" name="status" value="0"
                                            @if (!$method->status) checked @endif />
                                        <label for="radio-six">{{ __('Deactivated') }}</label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-12">
                                <button type="submit" class="site-btn primary-btn w-100">
                                    {{ __('Save Changes') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        'use strict';

        $("#currency").on('change', function() {
            currencySet();
        });

        currencySet();

        function currencySet() {
            const currency = $('#currency').val();
            $('.input-group-text').text(currency);
        }

        var i = Object.keys(@json($method->field_options ?? [])).length;
        $("#generate").on('click', function() {
            ++i;
            var form = `<div class="mb-4">
              <div class="option-remove-row row">
                <div class="col-xl-4 col-lg-6 col-md-6 col-sm-6 col-12">
                  <div class="site-input-groups">
                    <input name="field_options[` + i + `][name]" class="box-input" type="text" value="" required placeholder="{{ __('Field Name') }}">
                  </div>
                </div>

                <div class="col-xl-4 col-lg-6 col-md-6 col-sm-6 col-12">
                  <div class="site-input-groups">
                    <select name="field_options[` + i + `][type]" class="form-select form-select-lg mb-3">
                        <option value="text">{{ __('Input Text') }}</option>
                        <option value="textarea">{{ __('Textarea') }}</option>
                        <option value="file">{{ __('File upload') }}</option>
                    </select>
                  </div>
                </div>
                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-12">
                  <div class="site-input-groups mb-0">
                    <select name="field_options[` + i + `][validation]" class="form-select form-select-lg mb-1">
                        <option value="required">{{ __('Required') }}</option>
                        <option value="nullable">{{ __('Optional') }}</option>
                    </select>
                  </div>
                </div>

                <div class="col-xl-1 col-lg-6 col-md-6 col-sm-6 col-12">
                  <button class="delete-option-row delete_desc" type="button">
                    <i class="fas fa-times"></i>
                  </button>
                </div>
                </div>
              </div>`;
            $('.addOptions').append(form)
        });

        $(document).on('click', '.delete_desc', function() {
            $(this).closest('.option-remove-row').parent().remove();
        });

        $('#gateway-select').on('change', function() {
            var id = $(this).val();
            var url = '{{ route('admin.gateway.supported.currency', ':id') }}';
            url = url.replace(':id', id);
            $.get(url, function(data) {
                $('#currency').html(data.view);
                $('#currency-selected').text(data.pay_currency);
                currency = data.pay_currency
            })
        })
    </script>
@endsection
