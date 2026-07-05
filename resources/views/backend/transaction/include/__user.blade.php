<a href="{{ match (strtolower($user->role->value)) {
    'merchant' => route('admin.merchant.edit', $user->merchant->id),
    'agent' => route('admin.agent.edit', $user->agent->id),
    default => route('admin.user.edit', $user->id),
} }}"
    class="link">{{ safe($user->full_name) }} ({{ $user->role }})</a>
