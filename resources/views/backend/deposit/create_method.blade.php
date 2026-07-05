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
                        <form action="{{ route('admin.deposit.method.store') }}" class="row" method="post"
                            enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="type" value="{{ $type }}">
                            <div class="col-xl-12">
                                <div class="site-input-groups">
                                    <label class="box-input-label" for="">{{ __('Add Method Logo:') }}</label>
                                    <div class="wrap-custom-file">
                                        <input type="file" name="logo" id="logo"
                                            accept=".gif, .jpg, .png, .webp, .jpeg" />
                                        <label for="logo">
                                            <img class="upload-icon" src="{{ asset('global/materials/upload.svg') }}"
                                                alt="" />
                                            <span>{{ __('Upload Logo') }}</span>
                                        </label>
                                    </div>
                                    @error('logo')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            @if ($type == 'auto')
                                <div class="col-xl-6">
                                    <div class="site-input-groups">
                                        <label class="box-input-label" for="">{{ __('Automatic Gateway:') }}</label>
                                        <select name="gateway_id" class="form-select" id="gateway-select">
                                            <option>{{ __('Select Gateway') }}</option>
                                            @foreach ($gateways as $gateway)
                                                <option data-currencies="{{ $gateway->supported_currencies }}"
                                                    value="{{ $gateway->id }}"> {{ $gateway->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('gateway_id')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-xl-6">
                                    <div class="site-input-groups">
                                        <label class="box-input-label">
                                            {{ __('Gateway Supported Currency:') }}
                                        </label>
                                        <select name="currency" class="form-select" id="currency">

                                        </select>
                                        @error('currency')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            @endif

                            <div class="col-xl-6">
                                <div class="site-input-groups">
                                    <label class="box-input-label" for="">{{ __('Name:') }}</label>
                                    <input type="text" class="box-input" name="name" />
                                    @error('name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            @if ($type == 'manual')
                                <div class="col-xl-6">
                                    <div class="site-input-groups">
                                        <label class="box-input-label" for="">{{ __('Code Name:') }}</label>
                                        <input type="text" class="box-input" name="method_code" />
                                        @error('method_code')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            @endif


                            @if ($type == 'manual')
                                <div class="col-xl-6">
                                    <div class="site-input-groups">
                                        <label class="box-input-label" for="">{{ __('Currency:') }}</label>
                                        <select name="currency" id="currency" class="form-select">
                                            <option selected disabled>{{ __('Select Currency') }}</option>
                                            <option value="{{ $currency }}">{{ $currency }}</option>
                                            @foreach ($currencies as $currency)
                                                <option value="{{ $currency->code }}"> {{ $currency->code }}</option>
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
                                    <input type="text" class="box-input" name="currency_symbol" id="currency" />
                                    @error('currency_symbol')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-xl-6">
                                <div class="site-input-groups position-relative">
                                    <label class="box-input-label" for="">{{ __('Charges:') }}</label>
                                    <div class="position-relative">
                                        <input type="text" class="box-input" data-validate="decimal" name="charge" />
                                        <div class="prcntcurr">
                                            <select name="charge_type" class="form-select">
                                                <option value="percentage">{{ __('%') }}</option>
                                                <option value="fixed">{{ $currencySymbol }}</option>
                                            </select>
                                            @error('charge_type')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-6">
                                <div class="site-input-groups">
                                    <label class="box-input-label" for="">{{ __('Minimum Deposit:') }}</label>
                                    <div class="input-group joint-input">
                                        <input type="text" name="minimum_deposit" data-validate="decimal"
                                            class="form-control" />
                                        <span class="input-group-text"></span>
                                    </div>
                                    @error('minimum_deposit')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-xl-6">
                                <div class="site-input-groups">
                                    <label class="box-input-label" for="">{{ __('Maximum Deposit:') }}</label>
                                    <div class="input-group joint-input">
                                        <input type="text" name="maximum_deposit" data-validate="decimal"
                                            class="form-control" />
                                        <span class="input-group-text"></span>
                                    </div>
                                    @error('maximum_deposit')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-xl-12"></div>
                            @if ($type == 'manual')
                                <div class="col-xl-3 ">
                                    <a href="javascript:void(0)" id="generate"
                                        class="site-btn-xs primary-btn mb-3">{{ __('Add Field option') }}</a>
                                </div>
                                <div class="addOptions">
                                </div>
                                <div class="col-xl-12">
                                    <div class="site-input-groups fw-normal">
                                        <label for=""
                                            class="box-input-label">{{ __('Payment Details:') }}</label>
                                        <div class="site-editor">
                                            <textarea class="summernote" name="payment_details"></textarea>
                                        </div>
                                        @error('payment_details')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            @endif
                            <div class="col-xl-6">
                                <div class="site-input-groups">
                                    <label class="box-input-label" for="">{{ __('Status:') }}</label>
                                    <div class="switch-field same-type">
                                        <input type="radio" id="radio-five" name="status" value="1" checked />
                                        <label for="radio-five">{{ __('Active') }}</label>
                                        <input type="radio" id="radio-six" name="status" value="0" />
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
        (function($) {
            "use strict";
            var i = 0;

            $("#currency").on('change', function() {
                $('.input-group-text').text(this.value);
            });

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
                $.get(url, function($data) {
                    $('#currency').html($data.view);
                    $('#currency-selected').text($data.pay_currency);
                    currency = $data.pay_currency
                })
            })
        })(jQuery)
    </script>
@endsection
