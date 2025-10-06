<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Tabung Collection') }}
        </h2>
    </x-slot>

    <div class="py-8">
        {{-- Cards --}}
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
          @foreach($collections as $type => $collection)
    @php
        // Check if Geran Asas is fully redeemed
        $geranAsasRedeemed = $collections['geran_asas']->is_redeemed == 1;
        
        // Can withdraw for Usahawan / Pembiayaan only if Geran Asas is redeemed
        $canWithdraw = false;
        if (in_array($type, ['tabung_usahawan','had_pembiayaan'])) {
            $canWithdraw = $collection->balance > 0 && $geranAsasRedeemed;
        }

        // For progress display (Geran Asas)
        $progress = 0;
        $progressMax = 60000; // RM600 in cents
        $progressPercent = 0;
        
        if ($type === 'geran_asas') {
            $progress = $collection->pending_balance;
            $progressPercent = min(100, ($progress / $progressMax) * 100);
        }

        $tabungNames = [
            'geran_asas' => 'Geran Asas',
            'tabung_usahawan' => 'Tabung Usahawan',
            'had_pembiayaan' => 'Had Pembiayaan',
        ];
    @endphp

    <div class="bg-white p-6 rounded-2xl shadow-sm flex flex-col justify-between">
        {{-- Header --}}
        <div class="flex justify-between items-center mb-4">
            <div>
                <p class="text-sm text-gray-500">Available Balance</p>
                <p class="text-3xl font-bold text-green-600">
                    RM {{ number_format($collection->pending_balance / 100, 2) }}
                </p>
            </div>
            @if($collection->serial_number)
                <span class="px-3 py-1 bg-indigo-100 text-indigo-700 text-xs font-semibold rounded-full">
                    {{ $collection->serial_number }}
                </span>
            @endif
        </div>

        {{-- Tabung Name --}}
        <h3 class="text-lg font-semibold text-gray-700 capitalize mb-2">
            {{ $tabungNames[$type] ?? $type }}
        </h3>

        {{-- Geran Asas Progress & Status --}}
        @if($type === 'geran_asas')
            @if($collection->is_redeemed == 1)
                {{-- Already redeemed --}}
                <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg">
                    <p class="text-sm text-green-700 font-semibold">
                        ✓ Redeemed Successfully
                    </p>
                    <p class="text-xs text-green-600 mt-1">
                        Your Geran Asas has been claimed and activated.
                    </p>
                </div>
            @elseif($progress >= $progressMax)
                {{-- Completed but not redeemed yet --}}
                <div class="mb-4">
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div class="bg-green-500 h-3 rounded-full" style="width: 100%"></div>
                    </div>
                    <p class="text-xs text-gray-600 mt-1">
                        RM {{ number_format($progress / 100, 2) }} / RM {{ number_format($progressMax / 100, 2) }}
                    </p>
                    <div class="mt-3 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <p class="text-sm text-yellow-800 font-semibold">
                            ✓ Completed! 
                        </p>
                        <p class="text-xs text-yellow-700 mt-1">
                            Please contact administrator to claim your Geran Asas rewards.
                        </p>
                    </div>
                </div>
            @else
                {{-- Still in progress --}}
                <div class="mb-4">
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div class="bg-green-500 h-3 rounded-full" style="width: {{ $progressPercent }}%"></div>
                    </div>
                    <p class="text-xs text-gray-600 mt-1">
                        RM {{ number_format($progress / 100, 2) }} / RM {{ number_format($progressMax / 100, 2) }}
                    </p>
                </div>
            @endif
        @endif

        {{-- Withdraw button for Tabung Usahawan & Had Pembiayaan --}}
        @if($canWithdraw)
            <button class="w-full px-4 py-2 rounded-lg text-white font-semibold bg-indigo-600 hover:bg-indigo-700">
                Withdraw
            </button>
        @elseif(in_array($type, ['tabung_usahawan','had_pembiayaan']) && !$geranAsasRedeemed)
            <div class="p-3 bg-gray-50 border border-gray-200 rounded-lg">
                <p class="text-xs text-gray-600 text-center">
                    Complete Geran Asas to unlock withdrawals
                </p>
            </div>
        @endif
    </div>
@endforeach
        </div>

        {{-- Consolidated Transaction History --}}
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <h3 class="text-xl font-semibold text-gray-700 mb-4">Transaction History</h3>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm text-left border">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-2 border-b">Date</th>
                            <th class="px-3 py-2 border-b">Tabung</th>
                            <th class="px-3 py-2 border-b">Description</th>
                            <th class="px-3 py-2 border-b text-right">Amount (RM)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $allTransactions = collect();
                            foreach ($collections as $type => $col) {
                                foreach ($col->transactions as $tx) {
                                    $tx->tabung_type = $tabungNames[$type] ?? $type;
                                    $allTransactions->push($tx);
                                }
                            }
                            $allTransactions = $allTransactions->sortByDesc('created_at');
                        @endphp

                        @forelse($allTransactions as $tx)
                            <tr>
                                <td class="px-3 py-2 border-b">{{ $tx->created_at->format('d M Y H:i') }}</td>
                                <td class="px-3 py-2 border-b">{{ $tx->tabung_type }}</td>
                                <td class="px-3 py-2 border-b">{{ $tx->description }}</td>
                                <td class="px-3 py-2 border-b text-right
                                    {{ $tx->type === 'credit' ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $tx->type === 'credit' ? '+' : '-' }}
                                    {{ number_format($tx->amount / 100, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-3 py-4 text-center text-gray-500">No transactions yet</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
