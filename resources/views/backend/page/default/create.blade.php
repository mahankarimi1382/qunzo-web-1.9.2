@extends('backend.layouts.app')
@section('title')
    {{ __('Add New Page') }}
@endsection
@section('style')
    <link rel="stylesheet" href="{{ asset('backend/css/choices.min.css') }}">
@endsection
@section('content')
    <div class="main-content">
        <div class="page-title">
            <div class="container-fluid">
                <div class="row justify-content-center">
                    <div class="col-xl-12">
                        <div class="title-content">
                            <h2 class="title">{{ __('Add New Page') }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                    <div class="site-card">
                        <div class="site-card-header">
                            <h3 class="title">{{ __('Activity and Contents') }}</h3>
                        </div>
                        <div class="site-card-body">
                            <form action="{{ route('admin.page.store') }}" method="post" enctype="multipart/form-data">
                                @csrf
                                <div class="site-input-groups row">
                                    <label for="" class="col-sm-3 col-label">{{ __('Page Type') }}<i
                                            data-lucide="info" data-bs-toggle="tooltip" title=""
                                            data-bs-original-title="{{ __('Select the type of page you want to create') }}"></i></label>
                                    <div class="col-sm-9">
                                        <select name="page_type" class="box-input" id="page_type">
                                            <option value="dynamic">{{ __('Normal Page') }}</option>
                                            <option value="service">{{ __('Service Page') }}</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="site-input-groups row">
                                    <label for="" class="col-sm-3 col-label">{{ __('Page Title') }}<i
                                            data-lucide="info" data-bs-toggle="tooltip" title=""
                                            data-bs-original-title="{{ __('Page Title will show on Breadcrumb') }}"></i></label>
                                    <div class="col-sm-9">
                                        <input type="text" name="title" class="box-input" placeholder="{{ __('New Page Name') }}" required>
                                    </div>
                                </div>

                                <div class="site-input-groups row" id="page_content_field">
                                    <label for="" class="col-sm-3 col-label">{{ __('Page Contents') }}</label>
                                    <div class="col-sm-9">
                                        <div class="site-editor fw-normal">
                                            <textarea class="summernote" name="content"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-4">
                                    <label for="" class="col-sm-3 col-label"></label>
                                    <div class="col-sm-9">
                                        <hr>
                                    </div>
                                </div>
                                <div class="site-input-groups row">
                                    <label for="" class="col-sm-3 col-label">{{ __('Seo Keywords') }}<i
                                            data-lucide="info" data-bs-toggle="tooltip" title=""
                                            data-bs-original-title="{{ __('Page Seo Keywords') }}"></i></label>
                                    <div class="col-sm-9">
                                        <input type="text" name="meta_keywords" class="box-input">
                                    </div>
                                </div>

                                <div class="site-input-groups row">
                                    <label for="" class="col-sm-3 col-label">{{ __('Seo Description') }}<i
                                            data-lucide="info" data-bs-toggle="tooltip" title=""
                                            data-bs-original-title="{{ __('Page Seo Description') }}"></i></label>
                                    <div class="col-sm-9">
                                        <textarea name="meta_description" cols="30" rows="5" class="form-textarea"></textarea>
                                    </div>
                                </div>
                                <div class="site-input-groups row">
                                    <label for="" class="col-sm-3 col-label pt-0">{{ __('Page Status') }}<i
                                            data-lucide="info" data-bs-toggle="tooltip" title=""
                                            data-bs-original-title="{{ __('Manage Page Visibility') }}"></i></label>
                                    <div class="col-sm-3">
                                        <div class="site-input-groups">
                                            <div class="switch-field">
                                                <input type="radio" id="active" name="status" checked=""
                                                    value="1" />
                                                <label for="active">{{ __('Enable') }}</label>
                                                <input type="radio" id="deactivate" name="status" value="0" />
                                                <label for="deactivate">{{ __('Disabled') }}</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="offset-sm-3 col-sm-9">
                                        <button type="submit"
                                            class="site-btn-sm primary-btn w-100">{{ __('Save Changes') }}</button>
                                    </div>
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
    <script src="{{ asset('backend/js/choices.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            "use strict";

            new Choices('#section_id', {
                removeItemButton: true
            });

            // Show/hide page content field based on page type
            $('#page_type').on('change', function() {
                if ($(this).val() === 'service') {
                    $('#page_content_field').hide();
                } else {
                    $('#page_content_field').show();
                }
            });

            // Trigger on page load
            $('#page_type').trigger('change');
        })
    </script>
@endsection
