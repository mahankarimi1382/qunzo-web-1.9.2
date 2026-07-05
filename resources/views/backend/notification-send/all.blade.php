@extends('backend.layouts.app')
@section('title')
    {{ __('Send Email to All') }}
@endsection
@push('style')
    <link rel="stylesheet" href="{{ asset('backend/css/choices.min.css') }}">
@endpush
@section('script')
    <script src="{{ asset('backend/js/choices.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            "use strict";

            new Choices('#user_types', {
                removeItemButton: true
            });
        })
    </script>
@endsection
@section('content')
    <div class="main-content">
        <div class="page-title">
            <div class="container-fluid">
                <div class="row">
                    <div class="col">
                        <div class="title-content">
                            <h2 class="title">{{ __('Send Email to All') }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="container-fluid">
            <div class="row">

                <div class="col-xl-12">
                    <div class="site-card">
                        <div class="site-card-body">
                            <form action="{{ route('admin.mail.send.now') }}" method="post">
                                @csrf
                                <div class="site-input-groups">
                                    <label for="" class="box-input-label">{{ __('For Users :') }}</label>
                                    <select name="user_types[]" id="user_types" multiple>
                                        <option value="User">{{ __('User') }}</option>
                                        <option value="Agent">{{ __('Agent') }}</option>
                                        <option value="Merchant">{{ __('Merchant') }}</option>
                                    </select>
                                </div>

                                <div class="site-input-groups">
                                    <label for="" class="box-input-label">{{ __('Subject:') }}</label>
                                    <input type="text" name="subject" class="box-input mb-0" required="" />
                                </div>

                                <div class="site-input-groups">
                                    <label for="" class="box-input-label">{{ __('Email Details') }}</label>
                                    <textarea name="message" class="form-textarea mb-0"></textarea>
                                </div>

                                <div class="action-btns">
                                    <button type="submit" class="site-btn-sm primary-btn me-2">
                                        <i data-lucide="send"></i>
                                        {{ __('Send Email') }}
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
