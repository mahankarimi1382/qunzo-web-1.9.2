@extends('backend.layouts.app')

@section('title')
    {{ __('Create Payment Method') }}
@endsection

@section('content')
    <div class="main-content">
        <div class="container-fluid">
            <div class="page-title">
                <div class="container-fluid">
                    <div class="row justify-content-center">
                        <div class="col-xl-8">
                            <div class="title-content">
                                <h2 class="title">{{ __('Create Payment Method') }}</h2>
                                <div>
                                    <a href="{{ route('admin.p2p.payment-method.index') }}" class="title-btn">
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
                            <form action="{{ route('admin.p2p.payment-method.store') }}" class="row" method="POST">
                                @csrf
                                <div class="col-xl-6">
                                    <div class="site-input-groups">
                                        <label class="box-input-label">
                                            {{ __('Fiat Currency:') }}
                                            <span class="text-danger">*</span>
                                        </label>
                                        <select name="currency_id" class="form-select" required>
                                            <option value="">{{ __('Select Currency') }}</option>
                                            @foreach ($currencies as $currency)
                                                <option value="{{ $currency->id }}" @selected(old('currency_id') == $currency->id)>
                                                    {{ $currency->name }} ({{ $currency->code }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('currency_id')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-xl-6">
                                    <div class="site-input-groups">
                                        <label class="box-input-label">
                                            {{ __('Name:') }}
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="box-input" name="name" value="{{ old('name') }}"
                                            placeholder="{{ __('e.g. Bank Transfer') }}">
                                        @error('name')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-xl-12">
                                    <a href="javascript:void(0)" id="generate"
                                        class="site-btn-xs primary-btn mb-3">{{ __('Add Field option') }}</a>
                                </div>
                                <div class="col-xl-12 addOptions"></div>
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
        $(document).ready(function() {
            var i = 0;
            $("#generate").on('click', function() {
                ++i;
                var form = `<div class="mb-4">
                    <div class="option-remove-row row">
                        <div class="col-xl-4 col-lg-6 col-md-6 col-sm-6 col-12">
                            <div class="site-input-groups">
                                <input name="fields[` + i + `][name]" class="box-input" type="text" value="" placeholder="{{ __('Field Name') }}">
                            </div>
                        </div>
                        <div class="col-xl-4 col-lg-6 col-md-6 col-sm-6 col-12">
                            <div class="site-input-groups">
                                <select name="fields[` + i + `][type]" class="form-select form-select-lg mb-3">
                                    <option value="text">{{ __('Input Text') }}</option>
                                    <option value="textarea">{{ __('Textarea') }}</option>
                                    <option value="file">{{ __('File upload') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-12">
                            <div class="site-input-groups mb-0">
                                <select name="fields[` + i + `][validation]" class="form-select form-select-lg mb-1">
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
                $('.addOptions').append(form);
            });

            $(document).on('click', '.delete_desc', function() {
                $(this).closest('.option-remove-row').parent().remove();
            });
        });
    </script>
@endsection
