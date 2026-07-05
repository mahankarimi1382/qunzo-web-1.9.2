@extends('backend.setting.index')
@section('setting-title')
    {{ __('Site Settings') }}
@endsection
@section('title')
    {{ __('Site Settings') }}
@endsection
@section('setting-content')
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        @include('backend.setting.site_setting.include.__global', ['section' => 'global'])
        @include('backend.setting.site_setting.include.__permission', ['section' => 'permission'])
        @include('backend.setting.site_setting.include.__system', ['section' => 'system'])
        @include('backend.setting.site_setting.include.__fee', ['section' => 'fee'])
        @include('backend.setting.site_setting.include.__inactive_user', ['section' => 'inactive_user'])
        @includeWhen(addonActive('gift-cards'), 'backend.setting.site_setting.include.__gift_card', [
            'section' => 'gift_card',
        ])
        @includeWhen(addonActive('virtual-cards'), 'backend.setting.site_setting.include.__virtual_card', [
            'section' => 'virtual_card',
        ])
        @includeWhen(addonActive('p2p-trading'), 'backend.setting.site_setting.include.__p2p', [
            'section' => 'p2p',
        ])
        @include('backend.setting.site_setting.include.__passcode', ['section' => 'passcode'])
        @include('backend.setting.site_setting.include.__site_maintenance', [
            'section' => 'site_maintenance',
        ])
    </div>
@endsection
@push('single-script')
    <script>
        (function($) {
            'use strict';

            var timezoneData = JSON.parse(@json(getJsonData('timeZone')));
            const convertedData = timezoneData.map(item => ({
                id: item.name,
                text: `${item.description} (${item.name})`
            }));

            $('.site-timezone').select2({
                data: convertedData
            });

            // Account Deactivation Functionality
            function toggleElementsVisibility() {
                var inactiveAccountDisabledValue = $('input[name="inactive_account_disabled"]:checked').val();

                // Check the value and show/hide elements accordingly
                if (inactiveAccountDisabledValue == 1) {
                    $('#inactive_days_sec').show();
                    $('#inactive_account_fees_sec').show();
                    toggleFeesAmountVisibility();
                } else {
                    $('#inactive_days_sec').hide();
                    $('#inactive_account_fees_sec').hide();
                    $("#fees_amount_sec").hide();
                }
            }

            function toggleFeesAmountVisibility() {
                var inactive_account_fees = $('input[name="inactive_account_fees"]:checked').val();

                if (inactive_account_fees == 1) {
                    $('#fees_amount_sec').show();
                } else {
                    $('#fees_amount_sec').hide();
                }
            }

            // Initial toggle on page load
            toggleElementsVisibility();
            toggleFeesAmountVisibility();

            $('input[name="inactive_account_disabled"]').on('change', function() {
                toggleElementsVisibility();
            });

            $('input[name="inactive_account_fees"]').on('change', function() {
                toggleFeesAmountVisibility();
            });
        })(jQuery);
    </script>
@endpush
