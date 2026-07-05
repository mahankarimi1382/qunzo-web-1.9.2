@extends('backend.setting.index')
@section('setting-title')
    {{ __('Transactions Settings') }}
@endsection
@section('setting-content')
    <div class="container-fluid">
        <div class="section-design-nb mb-3">
            <strong>{{ __('Note:') }}</strong>
            {{ __('Transactions are converted to the desired currency, but fees are based on the main currency.') }}
        </div>
        <div class="row">
            <div class="col-xl-6 col-md-6">
                @include('backend.setting.include.__cashout')
            </div>
            <div class="col-xl-6 col-md-6">
                @include('backend.setting.include.__cashin')
            </div>
            <div class="col-xl-6 col-md-6">
                @include('backend.setting.include.__transfer')
            </div>
            <div class="col-xl-6 col-md-6">
                @include('backend.setting.include.__payment')
            </div>
            <div class="col-xl-6 col-md-6">
                @include('backend.setting.include.__gift')
            </div>
            <div class="col-xl-6 col-md-6">
                @include('backend.setting.include.__invoice')
            </div>
            <div class="col-xl-6 col-md-6">
                @include('backend.setting.include.__request_money')
            </div>
            <div class="col-xl-6 col-md-6">
                @include('backend.setting.include.__exchange')
            </div>
            <div class="col-xl-6 col-md-6">
                @include('backend.setting.include.__api_payment')
            </div>

        </div>
    </div>
@endsection
