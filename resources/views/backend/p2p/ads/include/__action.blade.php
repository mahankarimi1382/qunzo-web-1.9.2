@can('p2p-ads-approve')
    <button type="button" class="round-icon-btn primary-btn" data-id="{{ $id }}" id="ads-action"
        data-bs-toggle="modal" data-bs-target="#ads-action-modal"
        data-bs-toggle="tooltip" data-bs-original-title="{{ __('View & Approve') }}">
        <i data-lucide="eye"></i>
    </button>
@endcan
