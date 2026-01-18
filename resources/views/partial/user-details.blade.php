<div class="space-y-2 sm:space-y-3 text-sm sm:text-base">
    <p class="break-words"><strong class="text-gray-700 dark:text-gray-300">Name:</strong> <span class="text-gray-900 dark:text-white">{{ $user->name }}</span></p>
    <p class="break-words"><strong class="text-gray-700 dark:text-gray-300">Email:</strong> <span class="text-gray-900 dark:text-white truncate block sm:inline max-w-[200px] sm:max-w-none">{{ $user->email }}</span></p>
    <p><strong class="text-gray-700 dark:text-gray-300">Phone:</strong> <span class="text-gray-900 dark:text-white">{{ $user->profile->phone ?? '-' }}</span></p>
    <p><strong class="text-gray-700 dark:text-gray-300">Referrer:</strong> <span class="text-gray-900 dark:text-white">{{ $user->referral?->parent?->name ?? '-' }}</span></p>

    <h4 class="mt-3 sm:mt-4 font-semibold text-gray-900 dark:text-white">Accounts</h4>
    <ul class="list-disc list-inside text-xs sm:text-sm text-gray-600 dark:text-gray-400">
        @forelse($user->accounts as $acc)
            <li>
                {{ ucfirst($acc->type) }} - {{ $acc->serial_number ?? 'inactive' }}
                @if($acc->expires_at)
                    <span class="text-gray-500 dark:text-gray-500">(expires {{ \Carbon\Carbon::parse($acc->expires_at)->toFormattedDateString() }})</span>
                @endif
            </li>
        @empty
            <li>No accounts</li>
        @endforelse
    </ul>
</div>
