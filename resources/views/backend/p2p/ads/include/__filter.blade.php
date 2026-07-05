<form action="{{ request()->url() }}" method="get" id="filterForm">
    @if(request('status'))
        <input type="hidden" name="status" value="{{ request('status') }}">
    @endif
    <div class="table-filter">
        <div class="filter">
            <div class="search">
                <input type="text" id="search" name="search" value="{{ request('search') }}"
                    placeholder="{{ __('Search by username or email') }}" />
            </div>
            @if($showType ?? true)
                <select name="type" id="type" class="form-select form-select-sm">
                    <option value="">{{ __('All Types') }}</option>
                    <option value="buy" {{ request('type') == 'buy' ? 'selected' : '' }}>{{ __('Buy') }}</option>
                    <option value="sell" {{ request('type') == 'sell' ? 'selected' : '' }}>{{ __('Sell') }}</option>
                </select>
            @endif
            @if($showPerPage ?? true)
                <select name="per_page" id="perPage" class="form-select form-select-sm show">
                    <option value="15" {{ request('per_page', 15) == 15 ? 'selected' : '' }}>15</option>
                    <option value="30" {{ request('per_page') == 30 ? 'selected' : '' }}>30</option>
                    <option value="45" {{ request('per_page') == 45 ? 'selected' : '' }}>45</option>
                    <option value="60" {{ request('per_page') == 60 ? 'selected' : '' }}>60</option>
                </select>
            @endif
            <button type="submit" class="apply-btn"><i data-lucide="search"></i>{{ __('Search') }}</button>
        </div>
    </div>
</form>
@push('single-script')
    <script>
        (function($) {
            "use strict";
            $('#perPage').on('change', function() {
                $('#filterForm').submit();
            });
            $('#type').on('change', function() {
                $('#filterForm').submit();
            });
        })(jQuery);
    </script>
@endpush
