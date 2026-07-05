@switch(strtolower($status))
    @case('pending')
        <span class="site-badge pending">{{ __('Pending') }}</span>
    @break

    @case('success')
        <span class="site-badge success">{{ __('Success') }}</span>
    @break

    @case('failed')
        <span class="site-badge danger">{{ __('Cancelled') }}</span>
    @break
@endswitch
