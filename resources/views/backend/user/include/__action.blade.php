@can('customer-mail-send')
    <button type="button" data-id="{{ $user->id }}" data-name="{{ $user->first_name . ' ' . $user->last_name }}"
        class="send-mail round-icon-btn blue-btn" data-bs-toggle="tooltip" data-bs-original-title="{{ __('Send Email') }}">
        <i data-lucide="mail"></i>
    </button>
@endcan
@canany(['customer-basic-manage', 'customer-balance-add-or-subtract', 'customer-change-password', 'all-type-status'])
    <a href="{{ route('admin.user.edit', $user->id) }}" class="round-icon-btn primary-btn" data-bs-toggle="tooltip"
        data-bs-original-title="{{ __('Edit User') }}"><i data-lucide="edit-3"></i>
    </a>
    <button type="button" class="round-icon-btn red-btn" id="deleteModal" data-id="{{ $user->id }}"
        data-name="{{ $user->name }}" data-bs-toggle="tooltip" data-bs-original-title="{{ __('Delete User') }}">
        <i data-lucide="trash-2"></i>
    </button>
@endcanany
