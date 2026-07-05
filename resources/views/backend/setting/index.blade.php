@extends('backend.layouts.app')
@section('title')
    {{ __('Settings') }}
@endsection
@section('content')
    <div class="main-content">
        <div class="page-title">
            <div class="container-fluid">
                <div class="row">
                    <div class="col">
                        <div class="title-content">
                            <h2 class="title">@yield('setting-title')</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="container-fluid">
            <div class="row">

                <div class="col-xl-12">
                    <div class="site-tab-bars">
                        <ul>
                            @can('site-setting')
                                <li class="{{ isActive('admin.settings.site') }}">
                                    <a href="{{ route('admin.settings.site') }}"><i
                                            data-lucide="settings"></i>{{ __('General') }}</a>
                                </li>
                            @endcan

                            @can('transactions-settings')
                                <li class="{{ isActive('admin.settings.transactions') }}">
                                    <a href="{{ route('admin.settings.transactions') }}"><i
                                            data-lucide="arrow-right-left"></i>{{ __('Transaction Fees & Limits') }}</a>
                                </li>
                            @endcan

                            @can('plugin-setting')
                                <li class="{{ isActive('admin.settings.plugin', 'system') }}">
                                    <a href="{{ route('admin.settings.plugin', 'system') }}"><i
                                            data-lucide="toy-brick"></i>{{ __('Plugins') }}
                                    </a>
                                </li>
                            @endcan

                            @can('site-setting')
                                <li class="{{ isActive('admin.currency.*') }}">
                                    <a href="{{ route('admin.currency.index') }}"><i
                                            data-lucide="banknote"></i>{{ __('Currencies') }}</a>
                                </li>
                            @endcan

                            @can('site-setting')
                                <li class="{{ isActive('admin.settings.seo.meta') }} ">
                                    <a href="{{ route('admin.settings.seo.meta') }}"><i
                                            data-lucide="search-code"></i>{{ __('SEO Meta') }}</a>
                                </li>
                            @endcan

                            @can('email-setting')
                                <li class="{{ isActive('admin.settings.mail') }}">
                                    <a href="{{ route('admin.settings.mail') }}"><i
                                            data-lucide="mail"></i>{{ __('Email') }}</a>
                                </li>
                            @endcan

                            @can('language-setting')
                                <li class="{{ isActive('admin.language.*') }} ">
                                    <a href="{{ route('admin.language.index') }}"><i
                                            data-lucide="languages"></i>{{ __('Languages') }}</a>
                                </li>
                            @endcan

                            @can('page-manage')
                                <li class="{{ isActive('admin.page.setting') }} ">
                                    <a href="{{ route('admin.page.setting') }}"><i
                                            data-lucide="layout"></i>{{ __('Register Field Settings') }}</a>
                                </li>
                            @endcan
                        </ul>
                    </div>
                    <div class="row">
                        @yield('setting-content')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
