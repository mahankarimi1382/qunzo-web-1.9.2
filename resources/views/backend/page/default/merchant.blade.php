@extends('backend.layouts.app')
@section('title')
    {{ __('Merchant') }}
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
                            <h2 class="title">@yield('title')</h2>
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
                                <input type="hidden" name="page_code" value="merchant">
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
                                        <input type="text" name="title" class="box-input" value="{{ $data->title }}">
                                    </div>
                                </div>
                                <div class="site-input-groups row">
                                    <label for="" class="col-sm-3 col-label">{{ __('Section Title') }}</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="section_title" class="box-input"
                                            value="{{ $data->section_title }}">
                                    </div>
                                </div>
                                <div class="site-input-groups row">
                                    <label for="" class="col-sm-3 col-label">{{ __('Section Subtitle') }}</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="section_subtitle" class="box-input"
                                            value="{{ $data->section_subtitle }}">
                                    </div>
                                </div>
                                <div class="site-input-groups row">
                                    <label for="" class="col-sm-3 col-label">{{ __('App Title') }}</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="app_title" class="box-input"
                                            value="{{ $data->app_title }}">
                                    </div>
                                </div>
                                <div class="site-input-groups row">
                                    <label for="" class="col-sm-3 col-label">{{ __('App Subtitle') }}</label>
                                    <div class="col-sm-9">
                                        <textarea name="app_subtitle" class="form-textarea">{{ $data->app_subtitle }}</textarea>
                                    </div>
                                </div>
                                <div class="site-input-groups row">
                                    <label for="" class="col-sm-3 col-label">{{ __('App Store Link') }}</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="app_store_link" class="box-input"
                                            value="{{ $data->app_store_link }}">
                                    </div>
                                </div>
                                <div class="site-input-groups row">
                                    <label for="" class="col-sm-3 col-label">{{ __('Play Store Link') }}</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="play_store_link" class="box-input"
                                            value="{{ $data->play_store_link }}">
                                    </div>
                                </div>

                                @if ($key == 'en')
                                    <div class="site-input-groups row">
                                        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 col-label">
                                            {{ __('App Screen Image') }}
                                        </div>
                                        <div class="col-xl-9 col-lg-9 col-md-9 col-sm-12">
                                            <div class="wrap-custom-file">
                                                <input type="file" name="app_image" id="app_image"
                                                    accept=".gif, .jpg, .png, .svg, .webp" />
                                                <label for="app_image"
                                                    @if ($data->app_image) class="file-ok"
                                                   style="background-image: url({{ asset($data->app_image) }})" @endif>
                                                    <img class="upload-icon"
                                                        src="{{ asset('global/materials/upload.svg') }}" alt="" />
                                                    <span>{{ __('Update Image') }}</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="site-input-groups row">
                                        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 col-label">
                                            {{ __('App Store Image') }}
                                        </div>
                                        <div class="col-xl-9 col-lg-9 col-md-9 col-sm-12">
                                            <div class="wrap-custom-file">
                                                <input type="file" name="app_store_image" id="app_store_image"
                                                    accept=".gif, .jpg, .png, .svg, .webp" />
                                                <label for="app_store_image"
                                                    @if ($data->app_store_image) class="file-ok"
                                                   style="background-image: url({{ asset($data->app_store_image) }})" @endif>
                                                    <img class="upload-icon"
                                                        src="{{ asset('global/materials/upload.svg') }}"
                                                        alt="" />
                                                    <span>{{ __('Update Image') }}</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="site-input-groups row">
                                        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 col-label">
                                            {{ __('Play Store Image') }}
                                        </div>
                                        <div class="col-xl-9 col-lg-9 col-md-9 col-sm-12">
                                            <div class="wrap-custom-file">
                                                <input type="file" name="play_store_image" id="play_store_image"
                                                    accept=".gif, .jpg, .png, .svg, .webp" />
                                                <label for="play_store_image"
                                                    @if ($data->play_store_image) class="file-ok"
                                                   style="background-image: url({{ asset($data->play_store_image) }})" @endif>
                                                    <img class="upload-icon"
                                                        src="{{ asset('global/materials/upload.svg') }}"
                                                        alt="" />
                                                    <span>{{ __('Update Image') }}</span>
                                                </label>
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
                                                    <select name="section_id[]" id="section_id" class="form-select"
                                                        multiple>
                                                        @foreach ($landingSections as $section)
                                                            <option @selected(is_array(json_decode($data->section_id)) && in_array($section->id, json_decode($data->section_id)))
                                                                value="{{ $section->id }}">{{ $section->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <div class="site-input-groups row">
                                    <label for="" class="col-sm-3 col-label">{{ __('Content') }}</label>
                                    <div class="col-sm-9">
                                        <div class="site-editor fw-normal">
                                            <textarea class="summernote" name="content">{{ $data->content }}</textarea>
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
        @php
            $landingContent = getLandingContents('merchant');
        @endphp
        <div class="site-card">
            <div class="site-card-header">
                <h3 class="title">{{ __('Contents') }}</h3>
                <div class="card-header-links">
                    <a href="" class="card-header-link" type="button" data-bs-toggle="modal"
                        data-bs-target="#addNew">{{ __('Add New') }}</a>
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
                            @foreach ($landingContent as $content)
                                <tr>
                                    <td>
                                        <img src="{{ asset($content->icon) }}" alt="{{ $content->title }}">
                                    </td>
                                    <td>{{ $content->title }}</td>
                                    <td>{{ $content->description }}</td>
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
    </div>

    <!-- Modal for Add New  -->
    @include('backend.page.default.section.include.__add_new_merchant_content')
    <!-- Modal for Add New End -->

    <!-- Modal for Edit -->
    @include('backend.page.default.section.include.__edit')
    <!-- Modal for Edit  End-->

    <!-- Modal for Delete  -->
    @include('backend.page.default.section.include.__delete')
    <!-- Modal for Delete  End-->
@endsection

@section('script')
    <script src="{{ asset('backend/js/choices.min.js') }}"></script>
    <script>
        $('.editContent').on('click', function(e) {
            "use strict";
            e.preventDefault();
            var id = $(this).data('id');

            var url = '{{ route('admin.page.content-edit', ':id') }}';
            url = url.replace(':id', id);

            $.ajax({
                url: url,
                type: 'GET',
                success: function(response) {
                    // Handle the response HTML
                    $('#target-element').html(response.html);
                    $('#editContent').modal('show');
                },
                error: function(xhr) {
                    // Handle any errors that occurred during the request
                    console.log(xhr.responseText);
                }
            });
        });

        $('.deleteContent').on('click', function(e) {
            "use strict";
            e.preventDefault();
            var id = $(this).data('id');
            $('#deleteId').val(id);
            $('#deleteContent').modal('show');
        });

        $(document).ready(function() {
            "use strict";

            new Choices('#section_id', {
                removeItemButton: true
            });
        })
    </script>
@endsection
