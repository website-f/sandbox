<div class="space-y-3">
    <p><strong>Name:</strong> {{ $user->name }}</p>
    <p><strong>Email:</strong> {{ $user->email }}</p>
    <p><strong>Phone:</strong> {{ $user->profile->phone ?? '-' }}</p>
    <p><strong>Referrer:</strong> {{ $user->referral?->parent?->name ?? '-' }}</p>

    <h4 class="mt-4 font-semibold">Accounts</h4>
    <ul class="list-disc list-inside text-sm">
        @forelse($user->accounts as $acc)
            <li>
                {{ ucfirst($acc->type) }} - {{ $acc->serial_number ?? 'inactive' }}
                @if($acc->expires_at)
                    (expires {{ \Carbon\Carbon::parse($acc->expires_at)->toFormattedDateString() }})
                @endif
            </li>
        @empty
            <li>No accounts</li>
        @endforelse
    </ul>
</div>
