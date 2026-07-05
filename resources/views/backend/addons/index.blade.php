@extends('backend.layouts.app')

@section('title')
    {{ __('Addons') }}
@endsection

@section('style')
    <style>
        .red-btn {
            background: #ef476f !important;
        }
    </style>
@endsection

@section('content')
    <div class="main-content">
        <div class="page-title">
            <div class="container-fluid">
                <div class="row">
                    <div class="col">
                        <div class="title-content">
                            <h2 class="title">@yield('title')</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="container-fluid">
            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                <div class="site-card">
                    <div class="site-card-header">
                        <h3 class="title">{{ __('Addons') }}</h3>
                        <div class="card-header-links">
                            <button type="button" class="card-header-link" id="openUploadAddon">
                                <i data-lucide="plus"></i>
                                {{ __('Add New Addon') }}
                            </button>
                        </div>
                    </div>
                    <div class="site-card-body">
                        @if ($addons->isEmpty())
                            <p class="paragraph">
                                <i data-lucide="info"></i>
                                {{ __('No addons found. Upload a new addon ZIP to get started.') }}
                            </p>
                        @endif

                        @foreach ($addons as $addon)
                            <div class="single-gateway">
                                <div class="gateway-name">
                                    <div class="gateway-icon">
                                        <img src="/modules/Addons/{{ $addon['directory'] }}/icon.png"
                                            alt="{{ $addon['name'] }}" />
                                    </div>
                                    <div class="gateway-title">
                                        <h4>{{ $addon['name'] }}</h4>
                                        <p>
                                            <small>{{ __('Version:') }} {{ $addon['version'] }}</small>
                                        </p>
                                    </div>
                                </div>
                                <div class="gateway-right">
                                    <div class="gateway-status">
                                        @if ($addon['active'])
                                            <div class="site-badge success">{{ __('Activated') }}</div>
                                        @else
                                            <div class="site-badge pending">{{ __('Deactivated') }}</div>
                                        @endif
                                    </div>
                                    <div class="gateway-edit">

                                        @if ($addon['active'])
                                            <form action="{{ route('admin.addons.deactivate', $addon['slug']) }}"
                                                method="post" class="d-inline">
                                                @csrf
                                                <a href="#" onclick="$(this).closest('form').submit(); return false;"
                                                    data-bs-toggle="tooltip"
                                                    data-bs-original-title="{{ __('Deactivate') }}">
                                                    <i data-lucide="pause-circle"></i>
                                                </a>
                                            </form>
                                        @else
                                            <a href="#" class="addon-activate-btn" data-slug="{{ $addon['slug'] }}"
                                                data-license="{{ config('app.demo') ? 'Demo Protected' : $addon['license_key'] }}"
                                                data-bs-toggle="tooltip" data-bs-original-title="{{ __('Activate') }}">
                                                <i data-lucide="play-circle"></i>
                                            </a>
                                        @endif

                                        <form action="{{ route('admin.addons.delete', $addon['slug']) }}" method="post"
                                            class="d-inline addon-delete-form">
                                            @csrf
                                            <a href="#" onclick="$(this).closest('form').submit(); return false;"
                                                class="addon-delete-btn red-btn" data-bs-toggle="tooltip"
                                                data-bs-original-title="{{ __('Delete') }}">
                                                <i data-lucide="trash-2"></i>
                                            </a>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Upload Addon -->
    <div class="modal fade" id="uploadAddonModal" tabindex="-1" aria-labelledby="uploadAddonModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content site-table-modal">
                <div class="modal-body popup-body">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <div class="popup-body-text">
                        <form action="{{ route('admin.addons.upload') }}" method="post" enctype="multipart/form-data">
                            @csrf
                            <h3 class="title mb-4">{{ __('Upload Addon') }}</h3>

                            <div class="site-input-groups">
                                <label class="box-input-label" for="addon">{{ __('Addon ZIP File') }}</label>
                                <div class="wrap-custom-file">
                                    <input type="file" name="addon" id="addon" accept=".zip" required />
                                    <label for="addon" class="file-ok">
                                        <img class="upload-icon" src="{{ asset('global/materials/upload.svg') }}"
                                            alt="" />
                                        <span>{{ __('Upload') }}</span>
                                    </label>
                                </div>
                            </div>

                            <div class="action-btns mt-3">
                                <button type="submit" class="site-btn-sm primary-btn me-2">
                                    <i data-lucide="upload"></i>
                                    {{ __('Upload Addon') }}
                                </button>
                                <button type="button" class="site-btn-sm red-btn" data-bs-dismiss="modal"
                                    aria-label="Close">
                                    <i data-lucide="x"></i>
                                    {{ __('Close') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for License Key -->
    <div class="modal fade" id="addonLicenseModal" tabindex="-1" aria-labelledby="addonLicenseModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content site-table-modal">
                <div class="modal-body popup-body">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <div class="popup-body-text">
                        <form id="addonLicenseForm" method="post">
                            @csrf
                            <h3 class="title mb-4">{{ __('Activate Addon') }}</h3>

                            <div class="site-input-groups">
                                <label for="license_key" class="box-input-label">
                                    {{ __('License Key') }}
                                </label>
                                <input type="text" name="license_key" id="license_key" class="box-input mb-0"
                                    required />
                            </div>

                            <div class="action-btns mt-3">
                                <button type="submit" class="site-btn-sm primary-btn me-2">
                                    <i data-lucide="check"></i>
                                    {{ __('Activate') }}
                                </button>
                                <button type="button" class="site-btn-sm red-btn" data-bs-dismiss="modal"
                                    aria-label="Close">
                                    <i data-lucide="x"></i>
                                    {{ __('Close') }}
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

@section('script')
    <script>
        "use strict";

        // Open upload addon modal
        $('#openUploadAddon').on('click', function() {
            $('#uploadAddonModal').modal('show');
        });

        // Handle Activate button -> open license modal
        $('.addon-activate-btn').on('click', function() {
            const slug = $(this).data('slug');
            const license = $(this).data('license') || '';

            const form = $('#addonLicenseForm');
            let action = '{{ route('admin.addons.activate', ':slug') }}';
            action = action.replace(':slug', slug);

            form.attr('action', action);
            form.find('#license_key').val(license);

            $('#addonLicenseModal').modal('show');
        });

        // Handle Delete button
        $('.addon-delete-btn').on('click', function() {
            const form = $(this).closest('.addon-delete-form');

            if (confirm("{{ __('Are you sure you want to delete this addon? This action cannot be undone.') }}")) {
                form.submit();
            }
        });

        // Enable bootstrap tooltips
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(
            tooltipTriggerEl));
    </script>
@endsection
