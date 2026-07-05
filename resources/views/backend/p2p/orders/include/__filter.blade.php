<form action="{{ request()->url() }}" method="get" id="orderFilterForm">
    <div class="table-filter">
        <div class="filter">
            <div class="search">
                <input type="text" id="orderSearch" name="search" value="{{ request('search') }}"
                    placeholder="{{ __('Search by order id, username or email') }}" />
            </div>
            <select name="status" id="orderStatus" class="form-select form-select-sm">
                <option value="">{{ __('All Status') }}</option>
                <option value="pending_payment" {{ request('status') == 'pending_payment' ? 'selected' : '' }}>{{ __('Pending Payment') }}</option>
                <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>{{ __('Paid') }}</option>
                <option value="disputed" {{ request('status') == 'disputed' ? 'selected' : '' }}>{{ __('Disputed') }}</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>{{ __('Completed') }}</option>
                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>{{ __('Cancelled') }}</option>
                <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>{{ __('Expired') }}</option>
            </select>
            <select name="per_page" id="orderPerPage" class="form-select form-select-sm show">
                <option value="15" {{ request('per_page', 15) == 15 ? 'selected' : '' }}>15</option>
                <option value="30" {{ request('per_page') == 30 ? 'selected' : '' }}>30</option>
                <option value="45" {{ request('per_page') == 45 ? 'selected' : '' }}>45</option>
                <option value="60" {{ request('per_page') == 60 ? 'selected' : '' }}>60</option>
            </select>
            <button type="submit" class="apply-btn"><i data-lucide="search"></i>{{ __('Search') }}</button>
        </div>
    </div>
</form>

@push('single-script')
    <script>
        (function($) {
            "use strict";
            $('#orderPerPage').on('change', function() {
                $('#orderFilterForm').submit();
            });
            $('#orderStatus').on('change', function() {
                $('#orderFilterForm').submit();
            });
        })(jQuery);
    </script>
@endpush

