@extends('backend.layouts.app')
@section('title')
    {{ __('Theme Management') }}
@endsection
@section('content')
    <div class="main-content">
        <div class="page-title">
            <div class="container-fluid">
                <div class="row">
                    <div class="col">
                        <h2 class="title">@yield('theme-title')</h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="container-fluid">
            <div class="row">

                <div class="col-xl-12">
                    <div class="row">
                        @yield('theme-content')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
