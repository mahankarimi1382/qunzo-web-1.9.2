@extends('backend.layouts.app')
@section('title')
    {{ __('Edit Service Page') }}
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
                            <h2 class="title">{{ __('Update Service Page') }}</h2>
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
                            @if ($key == 'en')
                                <button type="button" class="site-btn-sm red-btn" data-bs-toggle="modal"
                                    data-bs-target="#deletePageModal"><i data-lucide="trash"></i>
                                    {{ __('Delete') }}</button>
                            @endif
                        </div>
                        <div class="site-card-body">
                            <form action="{{ route('admin.page.update') }}" method="post" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="page_code" value="{{ $code }}">
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
                                    <label for="" class="col-sm-3 col-label">{{ __('Page Title') }}<i
                                            data-lucide="info" data-bs-toggle="tooltip" title=""
                                            data-bs-original-title="Page Title will show on Breadcrumb"></i></label>
                                    <div class="col-sm-9">
                                        <input type="text" name="title" class="box-input" value="{{ $data->title ?? $title }}" required>
                                    </div>
                                </div>

                                {{-- Hero Section --}}
                                <div class="site-input-groups row mt-4">
                                    <label class="col-sm-12 col-label fw-bold">{{ __('Hero Section') }}</label>
                                </div>
                                <div class="site-input-groups row">
                                    <label for="" class="col-sm-3 col-label">{{ __('Hero Title') }}</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="hero_title" class="box-input"
                                            value="{{ $data->hero_title ?? '' }}">
                                        <small class="text-muted">
                                            {{ __('Use this shortcode to highlight words. Example: [[color_text= Your Text Here ]]') }}
                                        </small>
                                    </div>
                                </div>
                                <div class="site-input-groups row">
                                    <label for="" class="col-sm-3 col-label">{{ __('Hero Description') }}</label>
                                    <div class="col-sm-9">
                                        <textarea name="hero_description" class="form-textarea" rows="3">{{ $data->hero_description ?? '' }}</textarea>
                                    </div>
                                </div>
                                <div class="site-input-groups row">
                                    <label for="" class="col-sm-3 col-label">{{ __('Hero Button') }}</label>
                                    <div class="col-sm-9">
                                        <div class="form-row">
                                            <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12 col-12">
                                                <div class="site-input-groups">
                                                    <label for=""
                                                        class="box-input-label">{{ __('Button Label') }}</label>
                                                    <input type="text" name="hero_button_text" class="box-input"
                                                        value="{{ $data->hero_button_text ?? '' }}">
                                                </div>
                                            </div>
                                            <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12 col-12">
                                                <div class="site-input-groups">
                                                    <label for=""
                                                        class="box-input-label">{{ __('Button URL') }}</label>
                                                    <input type="text" name="hero_button_link" class="box-input"
                                                        value="{{ $data->hero_button_link ?? '' }}">
                                                </div>
                                            </div>
                                            <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12 col-12">
                                                <div class="site-input-groups">
                                                    <label for=""
                                                        class="box-input-label">{{ __('Target') }}</label>
                                                    <select name="hero_button_target" class="form-select">
                                                        <option @if (($data->hero_button_target ?? '_self') == '_self') selected @endif
                                                            value="_self">{{ __('Same Tab') }}</option>
                                                        <option @if (($data->hero_button_target ?? '_self') == '_blank') selected @endif
                                                            value="_blank">{{ __('Open In New Tab') }}</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @if ($key == 'en')
                                    <div class="site-input-groups row">
                                        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 col-label">
                                            {{ __('Hero Image') }}
                                        </div>
                                        <div class="col-xl-9 col-lg-9 col-md-9 col-sm-12">
                                            <div class="wrap-custom-file">
                                                <input type="file" name="hero_image" id="hero_image_{{ $key }}"
                                                    accept=".gif, .jpg, .png, .svg, .webp" />
                                                <label for="hero_image_{{ $key }}" id="hero_image_label_{{ $key }}"
                                                    @if ($data->hero_image) class="file-ok"
                                                   style="background-image: url({{ asset($data->hero_image) }})" @endif>
                                                    <img class="upload-icon"
                                                        src="{{ asset('global/materials/upload.svg') }}" alt="" />
                                                    <span>{{ __('Update Image') }}</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                {{-- Highlight Section --}}
                                <div class="site-input-groups row mt-4">
                                    <label class="col-sm-12 col-label fw-bold">{{ __('Highlight Section') }}</label>
                                </div>
                                <div class="site-input-groups row">
                                    <label for="" class="col-sm-3 col-label">{{ __('Highlight Title') }}</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="highlight_title" class="box-input"
                                            value="{{ $data->highlight_title ?? '' }}">
                                        <small class="text-muted">
                                            {{ __('Use this shortcode to highlight words. Example: [[color_text= Your Text Here ]]') }}
                                        </small>
                                    </div>
                                </div>
                                <div class="site-input-groups row">
                                    <label for="" class="col-sm-3 col-label">{{ __('Highlight Description') }}</label>
                                    <div class="col-sm-9">
                                        <textarea name="highlight_description" class="form-textarea summernote" rows="3">{{ $data->highlight_description ?? '' }}</textarea>
                                    </div>
                                </div>
                                <div class="site-input-groups row">
                                    <label for="" class="col-sm-3 col-label">{{ __('Highlight Button') }}</label>
                                    <div class="col-sm-9">
                                        <div class="form-row">
                                            <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12 col-12">
                                                <div class="site-input-groups">
                                                    <label for=""
                                                        class="box-input-label">{{ __('Button Label') }}</label>
                                                    <input type="text" name="highlight_button_text" class="box-input"
                                                        value="{{ $data->highlight_button_text ?? '' }}">
                                                </div>
                                            </div>
                                            <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12 col-12">
                                                <div class="site-input-groups">
                                                    <label for=""
                                                        class="box-input-label">{{ __('Button URL') }}</label>
                                                    <input type="text" name="highlight_button_link" class="box-input"
                                                        value="{{ $data->highlight_button_link ?? '' }}">
                                                </div>
                                            </div>
                                            <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12 col-12">
                                                <div class="site-input-groups">
                                                    <label for=""
                                                        class="box-input-label">{{ __('Target') }}</label>
                                                    <select name="highlight_button_target" class="form-select">
                                                        <option @if (($data->highlight_button_target ?? '_self') == '_self') selected @endif
                                                            value="_self">{{ __('Same Tab') }}</option>
                                                        <option @if (($data->highlight_button_target ?? '_self') == '_blank') selected @endif
                                                            value="_blank">{{ __('Open In New Tab') }}</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @if ($key == 'en')
                                    <div class="site-input-groups row">
                                        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 col-label">
                                            {{ __('Highlight Image') }}
                                        </div>
                                        <div class="col-xl-9 col-lg-9 col-md-9 col-sm-12">
                                            <div class="wrap-custom-file">
                                                <input type="file" name="highlight_image" id="highlight_image_{{ $key }}"
                                                    accept=".gif, .jpg, .png, .svg, .webp" />
                                                <label for="highlight_image_{{ $key }}" id="highlight_image_label_{{ $key }}"
                                                    @if ($data->highlight_image) class="file-ok"
                                                   style="background-image: url({{ asset($data->highlight_image) }})" @endif>
                                                    <img class="upload-icon"
                                                        src="{{ asset('global/materials/upload.svg') }}" alt="" />
                                                    <span>{{ __('Update Image') }}</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                {{-- Features Section --}}
                                <div class="site-input-groups row mt-4">
                                    <label class="col-sm-12 col-label fw-bold">{{ __('Features Section') }}</label>
                                </div>
                                <div class="site-input-groups row">
                                    <label for="" class="col-sm-3 col-label">{{ __('Section Title') }}</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="features_title" class="box-input"
                                            value="{{ $data->features_title ?? '' }}">
                                        <small class="text-muted">
                                            {{ __('Use this shortcode to highlight words. Example: [[color_text= Your Text Here ]]') }}
                                        </small>
                                    </div>
                                </div>

                                {{-- How It Works Steps Section --}}
                                <div class="site-input-groups row mt-4">
                                    <label class="col-sm-12 col-label fw-bold">{{ __('How It Works Steps Section') }}</label>
                                </div>
                                <div class="site-input-groups row">
                                    <label for="" class="col-sm-3 col-label">{{ __('Section Title') }}</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="steps_title" class="box-input"
                                            value="{{ $data->steps_title ?? '' }}">
                                        <small class="text-muted">
                                            {{ __('Use this shortcode to highlight words. Example: [[color_text= Your Text Here ]]') }}
                                        </small>
                                    </div>
                                </div>

                                {{-- FAQ Section --}}
                                <div class="site-input-groups row mt-4">
                                    <label class="col-sm-12 col-label fw-bold">{{ __('FAQ Section') }}</label>
                                </div>
                                <div class="site-input-groups row">
                                    <label for="" class="col-sm-3 col-label">{{ __('Section Title') }}</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="faq_title" class="box-input"
                                            value="{{ $data->faq_title ?? '' }}">
                                        <small class="text-muted">
                                            {{ __('Use this shortcode to highlight words. Example: [[color_text= Your Text Here ]]') }}
                                        </small>
                                    </div>
                                </div>
                                <div class="site-input-groups row">
                                    <label for="" class="col-sm-3 col-label">{{ __('Section Subtitle') }}</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="faq_subtitle" class="box-input"
                                            value="{{ $data->faq_subtitle ?? '' }}">
                                    </div>
                                </div>
                                @if ($key == 'en')
                                    <div class="site-input-groups row">
                                        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 col-label">
                                            {{ __('FAQ Image') }}
                                        </div>
                                        <div class="col-xl-9 col-lg-9 col-md-9 col-sm-12">
                                            <div class="wrap-custom-file">
                                                <input type="file" name="faq_image" id="faq_image_{{ $key }}"
                                                    accept=".gif, .jpg, .png, .svg, .webp" />
                                                <label for="faq_image_{{ $key }}" id="faq_image_label_{{ $key }}"
                                                    @if ($data->faq_image ?? null) class="file-ok"
                                                   style="background-image: url({{ asset($data->faq_image) }})" @endif>
                                                    <img class="upload-icon"
                                                        src="{{ asset('global/materials/upload.svg') }}" alt="" />
                                                    <span>{{ __('Update Image') }}</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                {{-- SEO Fields --}}
                                <div class="site-input-groups row mt-4">
                                    <label class="col-sm-12 col-label fw-bold">{{ __('SEO Settings') }}</label>
                                </div>
                                <div class="site-input-groups row">
                                    <label for="" class="col-sm-3 col-label">{{ __('Seo Keywords') }}<i
                                            data-lucide="info" data-bs-toggle="tooltip" title=""
                                            data-bs-original-title="{{ __('Page Seo Keywords') }}"></i></label>
                                    <div class="col-sm-9">
                                        <input type="text" name="meta_keywords" class="box-input"
                                            value="{{ $data->meta_keywords ?? '' }}">
                                    </div>
                                </div>
                                <div class="site-input-groups row">
                                    <label for="" class="col-sm-3 col-label">{{ __('Seo Description') }}<i
                                            data-lucide="info" data-bs-toggle="tooltip" title=""
                                            data-bs-original-title="{{ __('Page Seo Description') }}"></i></label>
                                    <div class="col-sm-9">
                                        <textarea name="meta_description" id="" cols="30" rows="5" class="form-textarea">{{ $data->meta_description ?? '' }}</textarea>
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
            @endforeach
        </div>

        @if (isset($groupData['en']))
            {{-- Features Content Table --}}
            <div class="site-card">
                <div class="site-card-header">
                    <h3 class="title">{{ __('Features Contents') }}</h3>
                    <div class="card-header-links">
                        <a href="" class="card-header-link" type="button" data-bs-toggle="modal"
                            data-bs-target="#addNewFeature">{{ __('Add New') }}</a>
                    </div>
                </div>
                <div class="site-card-body">
                    <div class="site-table table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">{{ __('Icon') }}</th>
                                    <th scope="col">{{ __('Title') }}</th>
                                    <th scope="col">{{ __('Description') }}</th>
                                    <th scope="col">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($features as $content)
                                    <tr>
                                        <td>
                                            @if ($content->icon)
                                                <img src="{{ asset($content->icon) }}" alt="{{ $content->title }}"
                                                    style="max-width: 50px; max-height: 50px;">
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>{{ $content->title }}</td>
                                        <td>{{ Str::limit($content->description, 100) }}</td>
                                        <td>
                                            <button class="round-icon-btn primary-btn editContent" type="button"
                                                data-id="{{ $content->id }}">
                                                <i data-lucide="edit-3"></i>
                                            </button>
                                            <button class="round-icon-btn red-btn deleteContent" type="button"
                                                data-id="{{ $content->id }}">
                                                <i data-lucide="trash-2"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Steps Content Table --}}
            <div class="site-card">
                <div class="site-card-header">
                    <h3 class="title">{{ __('How It Works Steps Contents') }}</h3>
                    <div class="card-header-links">
                        <a href="" class="card-header-link" type="button" data-bs-toggle="modal"
                            data-bs-target="#addNewStep">{{ __('Add New') }}</a>
                    </div>
                </div>
                <div class="site-card-body">
                    <div class="site-table table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">{{ __('Title') }}</th>
                                    <th scope="col">{{ __('Description') }}</th>
                                    <th scope="col">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($steps as $content)
                                    <tr>
                                        <td>{{ $content->title }}</td>
                                        <td>{{ Str::limit($content->description, 100) }}</td>
                                        <td>
                                            <button class="round-icon-btn primary-btn editContent" type="button"
                                                data-id="{{ $content->id }}">
                                                <i data-lucide="edit-3"></i>
                                            </button>
                                            <button class="round-icon-btn red-btn deleteContent" type="button"
                                                data-id="{{ $content->id }}">
                                                <i data-lucide="trash-2"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- FAQ Content Table --}}
            <div class="site-card">
                <div class="site-card-header">
                    <h3 class="title">{{ __('FAQ Contents') }}</h3>
                    <div class="card-header-links">
                        <a href="" class="card-header-link" type="button" data-bs-toggle="modal"
                            data-bs-target="#addNewFaq">{{ __('Add New') }}</a>
                    </div>
                </div>
                <div class="site-card-body">
                    <div class="site-table table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">{{ __('Question') }}</th>
                                    <th scope="col">{{ __('Answer') }}</th>
                                    <th scope="col">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($faqs as $content)
                                    <tr>
                                        <td>{{ $content->title }}</td>
                                        <td>{{ Str::limit($content->description, 100) }}</td>
                                        <td>
                                            <button class="round-icon-btn primary-btn editContent" type="button"
                                                data-id="{{ $content->id }}">
                                                <i data-lucide="edit-3"></i>
                                            </button>
                                            <button class="round-icon-btn red-btn deleteContent" type="button"
                                                data-id="{{ $content->id }}">
                                                <i data-lucide="trash-2"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </div>

    @if (isset($groupData['en']))
        <!-- Modal for Delete Page Confirmation -->
        <div class="modal fade" id="deletePageModal" tabindex="-1" aria-labelledby="deletePageModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-md modal-dialog-centered">
                <div class="modal-content site-table-modal">
                    <div class="modal-body popup-body">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        <div class="popup-body-text centered">
                            <div class="info-icon">
                                <i data-lucide="alert-triangle"></i>
                            </div>
                            <div class="title">
                                <h4>{{ __('Are you sure?') }}</h4>
                            </div>
                            <p>{{ __('Do you want to delete this page? All related contents and images will be permanently removed.') }}</p>
                            <div class="action-btns">
                                <form action="{{ route('admin.page.delete.now') }}" method="post" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="page_code" value="{{ $code }}">
                                    <button type="submit" class="site-btn-sm primary-btn me-2">
                                        <i data-lucide="check"></i>
                                        {{ __('Confirm') }}
                                    </button>
                                </form>
                                <a href="#" class="site-btn-sm red-btn" type="button" data-bs-dismiss="modal" aria-label="Close">
                                    <i data-lucide="x"></i>{{ __('Cancel') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal for Add New Feature -->
        @include('backend.page.default.service-page.include.__add_new_feature')
        <!-- Modal for Add New Step -->
        @include('backend.page.default.service-page.include.__add_new_step')
        <!-- Modal for Add New FAQ -->
        @include('backend.page.default.service-page.include.__add_new_faq')
        <!-- Modal for Edit -->
        @include('backend.page.default.section.include.__edit')
        <!-- Modal for Delete -->
        @include('backend.page.default.section.include.__delete')
    @endif
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            "use strict";

            // File preview for hero image
            $('#hero_image_{{ $key ?? 'en' }}').on('change', function() {
                filePreview($(this), '#hero_image_label_{{ $key ?? 'en' }}');
            });

            // File preview for highlight image
            $('#highlight_image_{{ $key ?? 'en' }}').on('change', function() {
                filePreview($(this), '#highlight_image_label_{{ $key ?? 'en' }}');
            });

            // File preview for FAQ image
            $('#faq_image_en').on('change', function() {
                filePreview($(this), '#faq_image_label_en');
            });

            function filePreview(el, target) {
                var file = $(el);
                var label = $(target);
                var labelText = label.find('span');

                var fileName = file.val().split('\\').pop();
                var tmppath = URL.createObjectURL(file.get(0).files[0]);

                label.css('background-image', 'url(' + tmppath + ')');
                label.addClass('file-ok');
                labelText.text(fileName);
            }

            // Edit content handler
            $('.editContent').on('click', function(e) {
                e.preventDefault();
                var id = $(this).data('id');

                var url = '{{ route('admin.page.content-edit', ':id') }}';
                url = url.replace(':id', id);

                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(response) {
                        $('#target-element').html(response.html);
                        $('#editContent').modal('show');
                    },
                    error: function(xhr) {
                        console.log(xhr.responseText);
                    }
                });
            });

            // Delete content handler
            $('.deleteContent').on('click', function(e) {
                e.preventDefault();
                var id = $(this).data('id');
                $('#deleteId').val(id);
                $('#deleteContent').modal('show');
            });
        })
    </script>
@endsection
