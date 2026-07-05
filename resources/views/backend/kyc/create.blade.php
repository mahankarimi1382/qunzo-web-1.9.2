@extends('backend.layouts.app')
@section('title')
    {{ __('Add New Verification Form') }}
@endsection
@section('content')
    <div class="main-content">
        <div class="page-title">
            <div class="container-fluid">
                <div class="row justify-content-center">
                    <div class="col-xl-8">
                        <div class="title-content">
                            <h2 class="title">{{ __('Add New Form') }}</h2>
                            <a href="{{ route('admin.verification-form.index') }}" class="title-btn"><i
                                    data-lucide="corner-down-left"></i>{{ __('Back') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-xl-8">
                    <div class="site-card">
                        <div class="site-card-body">
                            <form action="{{ route('admin.verification-form.store') }}" method="post" class="row">
                                @csrf

                                <div class="col-xl-8">
                                    <div class="site-input-groups">
                                        <label class="box-input-label" for="">{{ __('Name:') }}</label>
                                        <input type="text" name="name" value="{{ old('name') }}" class="box-input"
                                            placeholder="{{ __('Verification Name') }}" required />
                                    </div>
                                </div>

                                <div class="col-xl-4">
                                    <div class="site-input-groups">
                                        <label class="box-input-label" for="">{{ __('For:') }}</label>
                                        <select name="for" class="form-select">
                                            @foreach (App\Enums\KycFor::allOptions() as $kycFor)
                                                <option value="{{ $kycFor->value }}">
                                                    {{ ucfirst(str_replace('_', ' ', $kycFor->value)) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-xl-3">
                                    <a href="javascript:void(0)" id="generate"
                                        class="site-btn-xs primary-btn mb-3">{{ __('Add Field option') }}</a>
                                </div>
                                <div class="addOptions">

                                </div>

                                <div class="col-xl-12">
                                    <div class="row">
                                        <div class="col-xl-4 col-lg-4 col-md-6 col-sm-6">
                                            <div class="site-input-groups">
                                                <label class="box-input-label" for="">{{ __('Status:') }}</label>
                                                <div class="switch-field">
                                                    <input type="radio" id="active-status" name="status" checked=""
                                                        value="1" />
                                                    <label for="active-status">{{ __('Active') }}</label>
                                                    <input type="radio" id="deactivate-status" name="status"
                                                        value="0" />
                                                    <label for="deactivate-status">{{ __('Deactivated') }}</label>
                                                </div>
                                            </div>
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
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function(e) {
            "use strict";
            var i = 0;
            const verifiedTraderFor = @json(App\Enums\KycFor::VerifiedTrader->value);
            const forSelect = $('select[name="for"]');
            const fieldTypeLabels = {
                text: @json(__('Input Text')),
                textarea: @json(__('Textarea')),
                file: @json(__('File upload')),
                camera: @json(__('Camera')),
                front_camera: @json(__('Front Camera')),
            };

            const getAllowedFieldTypes = () => {
                const isVerifiedTrader = forSelect.val() === verifiedTraderFor;
                const baseTypes = ['file', 'camera', 'front_camera'];
                return isVerifiedTrader ? ['text', 'textarea', ...baseTypes] : baseTypes;
            };

            const renderFieldTypeOptions = (selectedValue = null) => {
                const allowedTypes = getAllowedFieldTypes();
                const finalSelected = allowedTypes.includes(selectedValue) ? selectedValue : allowedTypes[0];

                return allowedTypes.map((type) => {
                    const selected = type === finalSelected ? 'selected' : '';
                    return `<option value="${type}" ${selected}>${fieldTypeLabels[type]}</option>`;
                }).join('');
            };

            const refreshFieldTypeSelects = () => {
                $('select[name^="fields["][name$="[type]"]').each(function() {
                    const currentValue = $(this).val();
                    $(this).html(renderFieldTypeOptions(currentValue));
                });
            };

            $("#generate").on('click', function() {
                ++i;
                var form = `
                <div class="mb-4">
                  <div class="option-remove-row row">
                        <div class="col-xl-4 col-lg-6 col-md-6 col-sm-6 col-12">
                            <div class="site-input-groups">
                                <input name="fields[` + i + `][name]" class="box-input" type="text" value="" required placeholder="{{ __('Field Name') }}">
                            </div>
                        </div>

                        <div class="col-xl-4 col-lg-6 col-md-6 col-sm-6 col-12">
                            <div class="site-input-groups">
                                <select name="fields[` + i + `][type]" class="form-select form-select-lg mb-3">
                                    ` + renderFieldTypeOptions() + `
                                </select>
                            </div>
                        </div>

                        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-12">
                            <div class="site-input-groups">
                                <select name="fields[` + i + `][validation]" class="form-select form-select-lg mb-3">
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
                        
                        <div class="col-xl-11 col-lg-11 col-md-11 col-sm-11 col-11">
                            <div class="site-input-groups">
                                <input name="fields[` + i + `][instructions]" class="box-input" type="text" value="" required placeholder="{{ __('Instructions') }}">
                            </div>
                        </div>
                    </div>
                </div>`;
                $('.addOptions').append(form)
            });

            $(document).on('click', '.delete_desc', function() {
                $(this).closest('.option-remove-row').parent().remove();
            });

            forSelect.on('change', function() {
                refreshFieldTypeSelects();
            });
        });
    </script>
@endsection
