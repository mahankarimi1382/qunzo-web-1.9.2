<a href="{{ match (strtolower($kyc->role->value)) {
    'merchant' => route('admin.merchant.edit', $kyc->merchant->id),
    'agent' => route('admin.agent.edit', $kyc->agent->id),
    default => route('admin.user.edit', $kyc->id),
} }}"
    class="link">{{ safe($username) }} ({{ $kyc->role }})</a>
