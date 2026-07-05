@extends('backend.layouts.app')
@section('title')
    {{ __('Security Settings') }}
@endsection
@section('content')
    <div class="main-content">
        <div class="page-title">
            <div class="container-fluid">
                <div class="row justify-content-center">
                    <div class="col-xl-6">
                        <div class="title-content">
                            <h2 class="title">{{ __('Security Settings') }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="container-fluid">
            <form action="{{ route('admin.security.settings.update') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="row justify-content-center">
                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                        <div class="site-card">
                            <div class="site-card-body">
                                @if ($admin->two_fa == 0 && $admin->two_fa_secret)
                                    @php
                                        $google2fa = new \PragmaRX\Google2FAQRCode\Google2FA();

                                        $inlineUrl = $google2fa->getQRCodeInline(
                                            setting('site_title', 'global'),
                                            $admin->email,
                                            $admin->two_fa_secret,
                                        );
                                    @endphp
                                    @if (Str::of($inlineUrl)->startsWith('data:image/'))
                                        <img src="{{ $inlineUrl }}">
                                    @else
                                        {!! $inlineUrl !!}
                                    @endif

                                    <div class="site-input-groups mt-3">
                                        <div class="site-input-group">
                                            <label for="otp" class="box-input-label">{{ __('Enter OTP') }}</label>
                                            <input type="text" name="otp" class="box-input mb-0" required>
                                        </div>

                                        <button class="site-btn primary-btn mt-3" type="submit" name="type"
                                            value="enable">
                                            {{ __('Enable 2FA') }}
                                        </button>
                                    </div>
                                @elseif ($admin->two_fa == 1 && $admin->two_fa_secret)
                                    <div class="site-input-groups mt-3">
                                        <div class="site-input-group">
                                            <label for="otp" class="box-input-label">{{ __('Enter Password') }}</label>
                                            <input type="password" name="password" class="box-input mb-0" required>
                                        </div>

                                        <button class="site-btn primary-btn mt-3" type="submit" name="type"
                                            value="disable">
                                            {{ __('Disable 2FA') }}
                                        </button>
                                    </div>
                                @else
                                    <button class="site-btn primary-btn" type="submit" name="type" value="generate">
                                        {{ __('Generate 2FA') }}</button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </form>

        </div>
    </div>
@endsection
