@extends('backend.layouts.app')
@section('title')
    {{ __('About Us') }}
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
                            <h2 class="title">{{ __('About Us') }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @include('backend.page.default.include.__language_bar')

        <div class="tab-content" id="pills-tabContent">

            @foreach ($groupData as $key => $value)
                @php
                    $data = new Illuminate\Support\Fluent($value);
                @endphp

                <div class="tab-pane fade {{ $loop->index == 0 ? 'show active' : '' }}" id="{{ $key }}"
                    role="tabpanel" aria-labelledby="pills-informations-tab">
                    <div class="site-card">
                        <div class="site-card-header">
                            <h3 class="title">{{ __('Contents') }}</h3>
                        </div>
                        <div class="site-card-body">
                            <form action="{{ route('admin.page.update') }}" method="post" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="page_code" value="about">
                                <input type="hidden" name="page_locale" value="{{ $key }}">
                                @if ($key == 'en')
                                    <div class="site-input-groups row">
                                        <label for=""
                                            class="col-sm-3 col-label pt-0">{{ __('Section Visibility') }}<i
                                            data-lucide="info" data-bs-toggle="tooltip" title=""
                                            data-bs-original-title="Manage Section Visibility"></i></label>
                                        <div class="col-sm-3">
                                            <div class="site-input-groups">
                                                <div class="switch-field">
                                                    <input type="radio" id="active" name="status"
                                                        @if ($status) checked @endif value="1" />
                                                    <label for="active">{{ __('Show') }}</label>
                                                    <input type="radio" id="deactivate" name="status"
                                                        @if (!$status) checked @endif value="0" />
                                                    <label for="deactivate">{{ __('Hide') }}</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                <div class="site-input-groups row">
                                    <label for="" class="col-sm-3 col-label">{{ __('Title') }}<i
                                            data-lucide="info" data-bs-toggle="tooltip" title=""
                                            data-bs-original-title="Page Title will show on Breadcrumb"></i></label>
                                    <div class="col-sm-9">
                                        <input type="text" name="title" class="box-input" value="{{ $data->title }}">
                                    </div>
                                </div>

                                <div class="site-input-groups row">
                                    <label for="" class="col-sm-3 col-label">{{ __('About Title') }}</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="about_title" class="box-input"
                                            value="{{ $data->about_title }}">
                                    </div>
                                </div>

                                <div class="site-input-groups row">
                                    <label for="" class="col-sm-3 col-label">{{ __('Content') }}</label>
                                    <div class="col-sm-9">
                                        <div class="site-editor fw-normal">
                                            <textarea class="summernote" name="content">{{ $data->content }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="site-input-groups row">
                                    <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 col-label">
                                        {{ __('Right Image') }}
                                    </div>
                                    <div class="col-xl-9 col-lg-9 col-md-9 col-sm-12">
                                        <div class="wrap-custom-file">
                                            <input type="file" name="right_image" id="rightImage"
                                                accept=".gif, .jpg, .png, .svg, .webp" />
                                            <label for="rightImage" id="right_image"
                                                @if ($data->right_image) class="file-ok"
                                                   style="background-image: url({{ asset($data->right_image) }})" @endif>
                                                <img class="upload-icon" src="{{ asset('global/materials/upload.svg') }}"
                                                    alt="" />
                                                <span>{{ __('Update Image') }}</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="site-input-groups row">
                                    <label for="" class="col-sm-3 col-label">{{ __('Button') }}</label>
                                    <div class="col-sm-9">
                                        <div class="form-row">
                                            <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12 col-12">
                                                <div class="site-input-groups">
                                                    <label for=""
                                                        class="box-input-label">{{ __('Button Label') }}</label>
                                                    <input type="text" name="button_label" class="box-input"
                                                        value="{{ $data->button_label }}">
                                                </div>
                                            </div>
                                            <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12 col-12">
                                                <div class="site-input-groups">
                                                    <label for=""
                                                        class="box-input-label">{{ __('Button URL') }}</label>
                                                    <div class="site-input-groups">
                                                        <div class="site-input-groups">
                                                            <input type="text" name="button_url" class="box-input"
                                                                value="{{ $data->button_url }}">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12 col-12">
                                                <div class="site-input-groups">
                                                    <label for=""
                                                        class="box-input-label">{{ __('Target') }}</label>
                                                    <div class="site-input-groups">
                                                        <select name="button_target" class="form-select">
                                                            <option @if ($data->button_target == '_self') selected @endif
                                                                value="_self">{{ __('Same Tab') }}</option>
                                                            <option @if ($data->hero_button_target == '_blank') selected @endif
                                                                value="_blank">{{ __('Open In New Tab') }}</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="site-input-groups row">
                                    <label for="" class="col-sm-3 col-label">{{ __('Mission Title') }}</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="mission_title" class="box-input"
                                            value="{{ $data->mission_title }}">
                                    </div>
                                </div>

                                <div class="site-input-groups row">
                                    <label for="" class="col-sm-3 col-label">{{ __('Mission Content') }}</label>
                                    <div class="col-sm-9">
                                        <div class="site-editor fw-normal">
                                            <textarea class="form-textarea" name="mission_content">{{ $data->mission_content }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="site-input-groups row">
                                    <label for="" class="col-sm-3 col-label">{{ __('Vision Title') }}</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="vision_title" class="box-input"
                                            value="{{ $data->vision_title }}">
                                    </div>
                                </div>

                                <div class="site-input-groups row">
                                    <label for="" class="col-sm-3 col-label">{{ __('Vision Content') }}</label>
                                    <div class="col-sm-9">
                                        <div class="site-editor fw-normal">
                                            <textarea class="form-textarea" name="vision_content">{{ $data->vision_content }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="site-input-groups row mb-0">
                                    <label for="" class="col-sm-3 col-label">{{ __('Content Come From') }}<i
                                            data-lucide="info" data-bs-toggle="tooltip" title=""
                                            data-bs-original-title="The Contents will come from a section. Don't need any? Leave it blank"></i></label>
                                    <div class="col-sm-9">
                                        <div class="site-input-groups">
                                            <div class="site-input-groups">
                                                <select name="section_id[]" id="section_id" class="form-select" multiple>
                                                    @foreach ($landingSections as $section)
                                                        <option @selected(is_array(json_decode($data->section_id)) && in_array($section->id, json_decode($data->section_id))) value="{{ $section->id }}">
                                                            {{ $section->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="site-input-groups row">
                                    <label for="" class="col-sm-3 col-label">{{ __('Seo Keywords') }}<i
                                            data-lucide="info" data-bs-toggle="tooltip" title=""
                                            data-bs-original-title="Page Seo Keywords"></i></label>
                                    <div class="col-sm-9">
                                        <input type="text" name="meta_keywords" class="box-input"
                                            value="{{ $data->meta_keywords }}">
                                    </div>
                                </div>

                                <div class="site-input-groups row">
                                    <label for="" class="col-sm-3 col-label">{{ __('Seo Description') }}<i
                                            data-lucide="info" data-bs-toggle="tooltip" title=""
                                            data-bs-original-title="Page Seo Description"></i></label>
                                    <div class="col-sm-9">
                                        <textarea name="meta_description" cols="30" rows="5" class="form-textarea">{{ $data->meta_description }}</textarea>
                                    </div>
                                </div>


                                <div class="row">
                                    <div class="offset-sm-3 col-sm-9">
                                        <button type="submit"
                                            class="site-btn-sm primary-btn">{{ __('Save Changes') }}</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach


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
        })
    </script>
@endsection
