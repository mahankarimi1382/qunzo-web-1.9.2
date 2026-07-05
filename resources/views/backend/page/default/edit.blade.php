@extends('backend.layouts.app')
@section('title')
    {{ __('Edit Page') }}
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
                            <h2 class="title">{{ __('Update Page') }}</h2>
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
                            <h3 class="title">{{ __('Activity and Contents') }}</h3>
                            @if ($key == 'en')
                                <button type="button" class="site-btn-sm red-btn" data-bs-toggle="modal"
                                    data-bs-target="#deletePageModal"><i data-lucide="trash"></i>
                                    {{ __('Delete') }}</button>
                            @endif
                        </div>
                        <div class="site-card-body">
                            <form action="{{ route('admin.page.update') }}" method="post">
                                @csrf
                                <input type="hidden" name="page_code" value="{{ $code }}">
                                <input type="hidden" name="page_locale" value="{{ $key }}">


                                <div class="site-input-groups row">
                                    <label for="" class="col-sm-3 col-label">{{ __('Page Title') }}<i
                                            data-lucide="info" data-bs-toggle="tooltip" title=""
                                            data-bs-original-title="{{ __('Page Title will show on Breadcrumb') }}"></i></label>
                                    <div class="col-sm-9">
                                        <input type="text" name="title" class="box-input" placeholder="{{ __('New Page Name') }}"
                                            value="{{ $data->title ?? $title }}">
                                    </div>

                                </div>

                                @if ($key != 'en')
                                    <input type="hidden" name="section_id" value="{{ $data->section_id }}">
                                @endif
                                
                                <div class="site-input-groups row">
                                    <label for="" class="col-sm-3 col-label">{{ __('Page Contents') }}<i
                                            data-lucide="info" data-bs-toggle="tooltip" title=""
                                            data-bs-original-title="{{ __('Leave it blank if you don\'t need any custom content') }}"></i></label>
                                    <div class="col-sm-9">
                                        <div class="site-editor fw-normal">
                                            <textarea class="summernote" name="content">{!! $data->content !!}</textarea>
                                        </div>
                                    </div>
                                </div>


                                <div class="site-input-groups row mb-4">
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
                                        <input type="text" name="meta_keywords" class="box-input"
                                            value="{{ $data->meta_keywords }}">
                                    </div>
                                </div>

                                <div class="site-input-groups row">
                                    <label for="" class="col-sm-3 col-label">{{ __('Seo Description') }}<i
                                            data-lucide="info" data-bs-toggle="tooltip" title=""
                                            data-bs-original-title="{{ __('Page Seo Description') }}"></i></label>
                                    <div class="col-sm-9">
                                        <textarea name="meta_description" id="" cols="30" rows="5" class="form-textarea">{{ $data->meta_description }}</textarea>
                                    </div>
                                </div>

                                <div class="site-input-groups row @if ($key != 'en') hidden @endif">
                                    <label for="" class="col-sm-3 col-label pt-0">{{ __('Page Status') }}<i
                                            data-lucide="info" data-bs-toggle="tooltip" title=""
                                            data-bs-original-title="{{ __('Manage Page Visibility') }}"></i></label>
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
