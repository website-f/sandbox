<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Collection') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            {{-- Summary --}}
            <div class="bg-white p-6 rounded-2xl shadow-sm mb-6">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-sm text-gray-500">Available Balance</p>
                        <p class="text-3xl font-bold text-green-600">
                            RM {{ number_format($collection->balance / 100, 2) }}
                        </p>
                    </div>

                    @if($collection->serial_number)
                        <span class="px-4 py-2 bg-indigo-100 text-indigo-700 text-sm font-semibold rounded-full">
                            {{ $collection->serial_number }}
                        </span>
                    @endif
                </div>
            </div>

            {{-- Transactions --}}
            <div class="bg-white p-6 rounded-2xl shadow-sm">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">Recent Transactions</h3>

                <ul class="divide-y divide-gray-200">
                    @forelse($collection->transactions as $tx)
                        <li class="py-3 flex justify-between items-center">
                            <div>
                                <p class="text-sm font-medium text-gray-700">{{ $tx->description }}</p>
                                <p class="text-xs text-gray-400">{{ $tx->created_at->format('d M Y H:i') }}</p>
                            </div>
                            <span class="text-sm font-semibold {{ $tx->type === 'credit' ? 'text-green-600' : 'text-red-600' }}">
                                {{ $tx->type === 'credit' ? '+' : '-' }} RM {{ number_format($tx->amount / 100, 2) }}
                            </span>
                        </li>
                    @empty
                        <li class="py-6 text-sm text-gray-500 text-center">No transactions yet</li>
                    @endforelse
                </ul>
            </div>

        </div>
    </div>
</x-app-layout>
