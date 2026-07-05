@extends('backend.layouts.app')
@section('title')
    {{ __('Hero Section') }}
@endsection
@section('content')
    <div class="main-content">
        <div class="page-title">
            <div class="container-fluid">
                <div class="row justify-content-center">
                    <div class="col-xl-12">
                        <div class="title-content">
                            <h2 class="title">{{ __('Hero Section') }}</h2>
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
                            <form action="{{ route('admin.page.section.section.update') }}" method="post"
                                enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="section_code" value="hero">
                                <input type="hidden" name="section_locale" value="{{ $key }}">
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
                                    <label for="" class="col-sm-3 col-label">{{ __('Bonus Text') }}</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="bonus_text" class="box-input"
                                            value="{{ $data->bonus_text }}">
                                    </div>
                                </div>
                                <div class="site-input-groups row">
                                    <label for="" class="col-sm-3 col-label">{{ __('Hero Title') }}</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="hero_title" class="box-input"
                                            value="{{ $data->hero_title }}">
                                        <small class="text-muted">
                                            {{ __('Use this shortcode to highlight words. Example: [[color_text= Your Text Here ]]') }}
                                        </small>
                                    </div>
                                </div>
                                <div class="site-input-groups row">
                                    <label for="" class="col-sm-3 col-label">{{ __('Description') }}</label>
                                    <div class="col-sm-9">
                                        <textarea name="hero_description" class="form-textarea">{{ $data->hero_description }}</textarea>
                                    </div>
                                </div>

                                @if ($key == 'en')
                                    <div class="site-input-groups row">
                                        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 col-label">
                                            {{ __('Image') }}
                                        </div>
                                        <div class="col-xl-9 col-lg-9 col-md-9 col-sm-12">
                                            <div class="wrap-custom-file">
                                                <input type="file" name="image" id="image"
                                                    accept=".gif, .jpg, .png, .svg, .webp" />
                                                <label for="image" id="image"
                                                    @if ($data->image) class="file-ok"
                                                   style="background-image: url({{ asset($data->image) }})" @endif>
                                                    <img class="upload-icon"
                                                        src="{{ asset('global/materials/upload.svg') }}" alt="" />
                                                    <span>{{ __('Update Image') }}</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                @endif

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
                                    <label for="" class="col-sm-3 col-label">{{ __('Rounded Button') }}</label>
                                    <div class="col-sm-9">
                                        <div class="form-row">
                                            <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12 col-12">
                                                <div class="site-input-groups">
                                                    <label for=""
                                                        class="box-input-label">{{ __('Button Label') }}</label>
                                                    <input type="text" name="rounded_button_label" class="box-input"
                                                        value="{{ $data->rounded_button_label }}">
                                                </div>
                                            </div>
                                            <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12 col-12">
                                                <div class="site-input-groups">
                                                    <label for=""
                                                        class="box-input-label">{{ __('Button URL') }}</label>
                                                    <div class="site-input-groups">
                                                        <div class="site-input-groups">
                                                            <input type="text" name="rounded_button_url"
                                                                class="box-input"
                                                                value="{{ $data->rounded_button_url }}">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12 col-12">
                                                <div class="site-input-groups">
                                                    <label for=""
                                                        class="box-input-label">{{ __('Target') }}</label>
                                                    <div class="site-input-groups">
                                                        <select name="rounded_button_target" class="form-select">
                                                            <option @if ($data->rounded_button_target == '_self') selected @endif
                                                                value="_self">{{ __('Same Tab') }}</option>
                                                            <option @if ($data->rounded_button_target == '_blank') selected @endif
                                                                value="_blank">{{ __('Open In New Tab') }}</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                @if ($key == 'en')
                                    <div class="site-input-groups row">
                                        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 col-label">
                                            {{ __('Rounded Button Image') }}
                                        </div>
                                        <div class="col-xl-9 col-lg-9 col-md-9 col-sm-12">
                                            <div class="wrap-custom-file">
                                                <input type="file" name="rounded_button_image"
                                                    id="rounded_button_image" accept=".gif, .jpg, .png, .svg, .webp" />
                                                <label for="rounded_button_image" id="rounded_button_image"
                                                    @if ($data->rounded_button_image) class="file-ok"
                                                   style="background-image: url({{ asset($data->rounded_button_image) }})" @endif>
                                                    <img class="upload-icon"
                                                        src="{{ asset('global/materials/upload.svg') }}"
                                                        alt="" />
                                                    <span>{{ __('Update Image') }}</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <div class="site-input-groups row">
                                    <label for="" class="col-sm-3 col-label">{{ __('Bubble Counter') }}</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="bubble_counter" class="box-input"
                                            value="{{ $data->bubble_counter }}">
                                    </div>
                                </div>

                                <div class="site-input-groups row">
                                    <label for="" class="col-sm-3 col-label">{{ __('Bubble Text') }}</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="bubble_text" class="box-input"
                                            value="{{ $data->bubble_text }}">
                                    </div>
                                </div>

                                @if ($key == 'en')
                                    <div class="site-input-groups row">
                                        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 col-label">
                                            {{ __('Bubble Image') }}
                                        </div>
                                        <div class="col-xl-9 col-lg-9 col-md-9 col-sm-12">
                                            <div class="wrap-custom-file">
                                                <input type="file" name="bubble_image" id="bubble_image"
                                                    accept=".gif, .jpg, .png, .svg, .webp" />
                                                <label for="bubble_image" id="bubble_image"
                                                    @if ($data->bubble_image) class="file-ok"
                                                   style="background-image: url({{ asset($data->bubble_image) }})" @endif>
                                                    <img class="upload-icon"
                                                        src="{{ asset('global/materials/upload.svg') }}"
                                                        alt="" />
                                                    <span>{{ __('Update Image') }}</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                @endif

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
    </div>
@endsection

@section('script')
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
    </script>
@endsection
