@extends('backend.layouts.app')
@section('title')
    {{ __('Contact Us') }}
@endsection
@section('content')
    <div class="main-content">
        <div class="page-title">
            <div class="container-fluid">
                <div class="row justify-content-center">
                    <div class="col-xl-12">
                        <div class="title-content">
                            <h2 class="title">{{ __('Contact Page') }}</h2>
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
                                <input type="hidden" name="page_code" value="contact">
                                <input type="hidden" name="page_locale" value="{{ $key }}">
                                <div class="site-input-groups row">
                                    <label for="" class="col-sm-3 col-label">{{ __('Page Title') }}<i
                                            data-lucide="info" data-bs-toggle="tooltip" title=""
                                            data-bs-original-title="Page Title will show on Breadcrumb"></i></label>
                                    <div class="col-sm-9">
                                        <input type="text" name="title" class="box-input" value="{{ $data->title }}">
                                    </div>
                                </div>
                                @if ($key == 'en')
                                    <div class="site-input-groups row">
                                        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 col-label">
                                            {{ __('Phone Icon') }}
                                        </div>
                                        <div class="col-xl-9 col-lg-9 col-md-9 col-sm-12">
                                            <div class="wrap-custom-file">
                                                <input type="file" name="phone_icon" id="phoneIcon"
                                                    accept=".gif, .jpg, .png, .svg" />
                                                <label for="phoneIcon" id="phone_icon"
                                                    @if ($data->phone_icon) class="file-ok"
                                                   style="background-image: url({{ asset($data->phone_icon) }})" @endif>
                                                    <img class="upload-icon"
                                                        src="{{ asset('global/materials/upload.svg') }}" alt="" />
                                                    <span>{{ __('Update Icon') }}</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="site-input-groups row">
                                        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 col-label">
                                            {{ __('Email Icon') }}
                                        </div>
                                        <div class="col-xl-9 col-lg-9 col-md-9 col-sm-12">
                                            <div class="wrap-custom-file">
                                                <input type="file" name="email_icon" id="emailIcon"
                                                    accept=".gif, .jpg, .png, .svg" />
                                                <label for="emailIcon" id="email_icon"
                                                    @if ($data->email_icon) class="file-ok"
                                                   style="background-image: url({{ asset($data->email_icon) }})" @endif>
                                                    <img class="upload-icon"
                                                        src="{{ asset('global/materials/upload.svg') }}" alt="" />
                                                    <span>{{ __('Update Icon') }}</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="site-input-groups row">
                                        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 col-label">
                                            {{ __('Address Icon') }}
                                        </div>
                                        <div class="col-xl-9 col-lg-9 col-md-9 col-sm-12">
                                            <div class="wrap-custom-file">
                                                <input type="file" name="address_icon" id="addressIcon"
                                                    accept=".gif, .jpg, .png, .svg" />
                                                <label for="addressIcon" id="address_icon"
                                                    @if ($data->address_icon) class="file-ok"
                                                   style="background-image: url({{ asset($data->address_icon) }})" @endif>
                                                    <img class="upload-icon"
                                                        src="{{ asset('global/materials/upload.svg') }}" alt="" />
                                                    <span>{{ __('Update Icon') }}</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="site-input-groups row">
                                        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 col-label">
                                            {{ __('Form Right Image') }}
                                        </div>
                                        <div class="col-xl-9 col-lg-9 col-md-9 col-sm-12">
                                            <div class="wrap-custom-file">
                                                <input type="file" name="form_right_image" id="formRightImage"
                                                    accept=".gif, .jpg, .png, .svg, .webp" />
                                                <label for="formRightImage" id="form_right_image"
                                                    @if ($data->form_right_image) class="file-ok"
                                                   style="background-image: url({{ asset($data->form_right_image) }})" @endif>
                                                    <img class="upload-icon"
                                                        src="{{ asset('global/materials/upload.svg') }}" alt="" />
                                                    <span>{{ __('Update Image') }}</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <div class="site-input-groups row">
                                    <label for="" class="col-sm-3 col-label">{{ __('Phone Number Label') }}
                                        <i data-lucide="info" data-bs-toggle="tooltip"
                                            data-bs-original-title="Leave it blank if you don't need to show"></i>
                                    </label>
                                    <div class="col-sm-9">
                                        <input type="text" name="phone_no_label" class="box-input"
                                            value="{{ $data->phone_no_label }}">
                                    </div>
                                </div>

                                <div class="site-input-groups row">
                                    <label for="" class="col-sm-3 col-label">{{ __('Phone Number') }}
                                        <i data-lucide="info" data-bs-toggle="tooltip"
                                            data-bs-original-title="Leave it blank if you don't need to show"></i>
                                    </label>
                                    <div class="col-sm-9">
                                        <input type="text" name="phone_no" class="box-input"
                                            value="{{ $data->phone_no }}">
                                    </div>
                                </div>

                                <div class="site-input-groups row">
                                    <label for="" class="col-sm-3 col-label">{{ __('Email Label') }}
                                        <i data-lucide="info" data-bs-toggle="tooltip"
                                            data-bs-original-title="Leave it blank if you don't need to show"></i>
                                    </label>
                                    <div class="col-sm-9">
                                        <input type="text" name="email_label" class="box-input"
                                            value="{{ $data->email_label }}">
                                    </div>
                                </div>

                                <div class="site-input-groups row">
                                    <label for="" class="col-sm-3 col-label">{{ __('Email Address') }}<i
                                            data-lucide="info" data-bs-toggle="tooltip"
                                            data-bs-original-title="Leave it blank if you don't need to show"></i></label>
                                    <div class="col-sm-9">
                                        <input type="text" name="email" class="box-input"
                                            value="{{ $data->email }}">
                                    </div>
                                </div>

                                <div class="site-input-groups row">
                                    <label for="" class="col-sm-3 col-label">{{ __('Address Label') }}
                                        <i data-lucide="info" data-bs-toggle="tooltip"
                                            data-bs-original-title="Leave it blank if you don't need to show"></i>
                                    </label>
                                    <div class="col-sm-9">
                                        <input type="text" name="address_label" class="box-input"
                                            value="{{ $data->address_label }}">
                                    </div>
                                </div>

                                <div class="site-input-groups row">
                                    <label for="" class="col-sm-3 col-label">{{ __('Address') }}<i
                                            data-lucide="info" data-bs-toggle="tooltip"
                                            data-bs-original-title="Leave it blank if you don't need to show"></i></label>
                                    <div class="col-sm-9">
                                        <input type="text" name="address" class="box-input"
                                            value="{{ $data->address }}">
                                    </div>
                                </div>


                                <div class="site-input-groups row">
                                    <label for="" class="col-sm-3 col-label">
                                        {{ __('Form Title') }}
                                    </label>
                                    <div class="col-sm-9">
                                        <input type="text" name="form_title" class="box-input"
                                            value="{{ $data->form_title }}">
                                    </div>
                                </div>

                                <div class="site-input-groups row">
                                    <label for="" class="col-sm-3 col-label">
                                        {{ __('Form Subtitle') }}
                                    </label>
                                    <div class="col-sm-9">
                                        <input type="text" name="form_subtitle" class="box-input"
                                            value="{{ $data->form_subtitle }}">
                                    </div>
                                </div>

                                <div class="site-input-groups row">
                                    <label for="" class="col-sm-3 col-label">
                                        {{ __('Form Button Text') }}
                                    </label>
                                    <div class="col-sm-9">
                                        <input type="text" name="form_button_text" class="box-input"
                                            value="{{ $data->form_button_text }}">
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
                                @if ($key == 'en')
                                    <div class="site-input-groups row">
                                        <label for="" class="col-sm-3 col-label pt-0">{{ __('Page Status') }}<i
                                                data-lucide="info" data-bs-toggle="tooltip" title=""
                                                data-bs-original-title="Manage Page Visibility"></i></label>
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
