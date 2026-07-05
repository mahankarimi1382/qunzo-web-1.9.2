@extends('backend.auth.index')
@section('title')
    {{ __('Two Factor Authentication') }}
@endsection
@section('auth-content')
    <div class="login">
        <div class="side-img primary-overlay"
            style="background: url( {{ asset(setting('login_bg', 'global')) }} ) no-repeat center center;">
            <div class="title">
                <h3>{{ __('Two Factor Authentication') }}</h3>
            </div>
        </div>
        <div class="login-content">
            @php
                $height =
                    setting('site_logo_height', 'global') == 'auto'
                        ? 'auto'
                        : setting('site_logo_height', 'global') . 'px';
                $width =
                    setting('site_logo_width', 'global') == 'auto'
                        ? 'auto'
                        : setting('site_logo_width', 'global') . 'px';
            @endphp
            <div class="logo">
                <a href="#">
                    <img src="{{ asset(setting('site_logo', 'global')) }}"
                        style="height:{{ $height }};width:{{ $width }}"
                        alt="{{ asset(setting('site_title', 'global')) }}" />
                </a>
            </div>
            <div class="auth-body">
                <form action="{{ route('admin.two.fa.verify.post') }}" method="post">
                    @csrf

                    @if ($errors->has('email'))
                        <span class="text-danger">{{ $errors->first('email') }}</span>
                    @endif

                    <div class="single-box">
                        <label for="" class="box-label">{{ __('OTP') }}</label>
                        <input type="text" name="otp" class="box-input" required />
                    </div>
                    <div class="single-box">
                        <button class="site-btn primary-btn" type="submit">{{ __('Verify OTP') }}</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
@endsection
