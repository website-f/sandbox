<x-admin-layout>
    <x-slot name="pageTitle">My Tabung Collection</x-slot>
    <x-slot name="breadcrumb">Track your savings and manage your collections</x-slot>

    @php
    $tabungNames = [
    'geran_asas' => 'Geran Asas',
    'tabung_usahawan' => 'Tabung Usahawan',
    'had_pembiayaan' => 'Had Pembiayaan',
    ];

    $tabungIcons = [
    'geran_asas' => 'fa-gift',
    'tabung_usahawan' => 'fa-briefcase',
    'had_pembiayaan' => 'fa-hand-holding-usd',
    ];

    $tabungColors = [
    'geran_asas' => ['bg' => 'bg-purple-100 dark:bg-purple-900/30', 'text' => 'text-purple-600 dark:text-purple-400', 'gradient' => 'from-purple-500 to-indigo-500'],
    'tabung_usahawan' => ['bg' => 'bg-blue-100 dark:bg-blue-900/30', 'text' => 'text-blue-600 dark:text-blue-400', 'gradient' => 'from-blue-500 to-cyan-500'],
    'had_pembiayaan' => ['bg' => 'bg-green-100 dark:bg-green-900/30', 'text' => 'text-green-600 dark:text-green-400', 'gradient' => 'from-green-500 to-emerald-500'],
    ];
    @endphp

    {{-- Tabung Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 mb-6 sm:mb-8">
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

        $colors = $tabungColors[$type] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-600', 'gradient' => 'from-gray-500 to-gray-600'];
        @endphp

        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 overflow-hidden">
            {{-- Card Header --}}
            <div class="bg-gradient-to-r {{ $colors['gradient'] }} p-4 sm:p-6 text-white">
                <div class="flex items-center justify-between mb-3 sm:mb-4">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-white/20 rounded-xl flex items-center justify-center">
                        <i class="fas {{ $tabungIcons[$type] ?? 'fa-piggy-bank' }} text-lg sm:text-xl"></i>
                    </div>
                    @if($collection->serial_number)
                    <span class="px-2 sm:px-3 py-1 bg-white/20 text-white text-[10px] sm:text-xs font-semibold rounded-lg">
                        {{ $collection->serial_number }}
                    </span>
                    @endif
                </div>
                <h3 class="text-base sm:text-lg font-semibold">{{ $tabungNames[$type] ?? $type }}</h3>
                <p class="text-white/70 text-xs sm:text-sm">Available Balance</p>
                <p class="text-2xl sm:text-3xl font-bold mt-1">
                    RM {{ number_format($collection->balance / 100, 2) }}
                </p>
            </div>

            {{-- Card Body --}}
            <div class="p-4 sm:p-6">
                {{-- Geran Asas Progress & Status --}}
                @if($type === 'geran_asas')
                @if($collection->is_redeemed == 1)
                {{-- Already redeemed --}}
                <div class="p-3 sm:p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl mb-3 sm:mb-4">
                    <div class="flex items-center gap-2 sm:gap-3">
                        <div class="w-8 h-8 sm:w-10 sm:h-10 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-check text-green-600 dark:text-green-400 text-sm sm:text-base"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="font-semibold text-green-700 dark:text-green-400 text-sm sm:text-base">Redeemed Successfully</p>
                            <p class="text-[10px] sm:text-xs text-green-600 dark:text-green-500">Your Geran Asas has been claimed and activated</p>
                        </div>
                    </div>
                </div>
                @elseif($progress >= $progressMax)
                {{-- Completed but not redeemed yet --}}
                <div class="mb-3 sm:mb-4">
                    <div class="flex justify-between text-xs sm:text-sm mb-2">
                        <span class="text-gray-600 dark:text-gray-400">Progress</span>
                        <span class="font-semibold text-gray-900 dark:text-white">100%</span>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2 sm:h-3 overflow-hidden">
                        <div class="bg-gradient-to-r from-green-500 to-emerald-500 h-2 sm:h-3 rounded-full" style="width: 100%"></div>
                    </div>
                    <p class="text-[10px] sm:text-xs text-gray-500 dark:text-gray-400 mt-1 sm:mt-2">
                        RM {{ number_format($progress / 100, 2) }} / RM {{ number_format($progressMax / 100, 2) }}
                    </p>
                </div>
                <div class="p-3 sm:p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-xl">
                    <div class="flex items-center gap-2 sm:gap-3">
                        <div class="w-8 h-8 sm:w-10 sm:h-10 bg-yellow-100 dark:bg-yellow-900/30 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-star text-yellow-600 dark:text-yellow-400 text-sm sm:text-base"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="font-semibold text-yellow-800 dark:text-yellow-400 text-sm sm:text-base">Completed!</p>
                            <p class="text-[10px] sm:text-xs text-yellow-700 dark:text-yellow-500">Contact administrator to claim your Geran Asas rewards</p>
                        </div>
                    </div>
                </div>
                @else
                {{-- Still in progress --}}
                <div class="mb-3 sm:mb-4">
                    <div class="flex justify-between text-xs sm:text-sm mb-2">
                        <span class="text-gray-600 dark:text-gray-400">Progress</span>
                        <span class="font-semibold text-gray-900 dark:text-white">{{ number_format($progressPercent, 1) }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2 sm:h-3 overflow-hidden">
                        <div class="bg-gradient-to-r {{ $colors['gradient'] }} h-2 sm:h-3 rounded-full transition-all duration-500" style="width: {{ $progressPercent }}%"></div>
                    </div>
                    <p class="text-[10px] sm:text-xs text-gray-500 dark:text-gray-400 mt-1 sm:mt-2">
                        RM {{ number_format($progress / 100, 2) }} / RM {{ number_format($progressMax / 100, 2) }}
                    </p>
                </div>
                @endif
                @endif

                {{-- Withdraw button for Tabung Usahawan & Had Pembiayaan --}}
                @if($canWithdraw)
                <button class="w-full px-3 sm:px-4 py-2.5 sm:py-3 rounded-xl text-white font-semibold bg-gradient-to-r {{ $colors['gradient'] }} hover:opacity-90 transition-opacity shadow-lg text-sm sm:text-base">
                    <i class="fas fa-money-bill-wave mr-2"></i> Withdraw
                </button>
                @elseif(in_array($type, ['tabung_usahawan','had_pembiayaan']) && !$geranAsasRedeemed)
                <div class="p-3 sm:p-4 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl">
                    <div class="flex items-center gap-2 sm:gap-3">
                        <div class="w-8 h-8 sm:w-10 sm:h-10 bg-gray-200 dark:bg-gray-600 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-lock text-gray-500 dark:text-gray-400 text-sm sm:text-base"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-400">Complete Geran Asas to unlock withdrawals</p>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>

    {{-- Consolidated Transaction History --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl p-4 sm:p-6 border border-gray-100 dark:border-gray-700">
        <div class="flex items-center justify-between mb-4 sm:mb-6">
            <h3 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white">
                <i class="fas fa-history text-indigo-600 dark:text-indigo-400 mr-2"></i>
                Transaction History
            </h3>
        </div>

        <div class="overflow-x-auto" style="-webkit-overflow-scrolling: touch;">
            <table id="collectionTable" class="w-full min-w-[500px]">
                <thead>
                    <tr class="text-left text-xs sm:text-sm font-semibold text-gray-500 dark:text-gray-400 border-b border-gray-200 dark:border-gray-700">
                        <th class="pb-3 px-3 sm:px-4">Date</th>
                        <th class="pb-3 px-3 sm:px-4">Tabung</th>
                        <th class="pb-3 px-3 sm:px-4 hidden sm:table-cell">Description</th>
                        <th class="pb-3 px-3 sm:px-4 text-right">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @php
                    $allTransactions = collect();
                    foreach ($collections as $type => $col) {
                    foreach ($col->transactions as $tx) {
                    $tx->tabung_type = $tabungNames[$type] ?? $type;
                    $tx->tabung_key = $type;
                    $allTransactions->push($tx);
                    }
                    }
                    $allTransactions = $allTransactions->sortByDesc('created_at');
                    @endphp

                    @forelse($allTransactions as $tx)
                    @php
                    $colors = $tabungColors[$tx->tabung_key] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-600'];
                    @endphp
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <td class="py-3 sm:py-4 px-3 sm:px-4">
                            <div class="flex items-center gap-2 sm:gap-3">
                                <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl {{ $tx->type === 'credit' ? 'bg-green-100 dark:bg-green-900/30' : 'bg-red-100 dark:bg-red-900/30' }} flex items-center justify-center flex-shrink-0">
                                    <i class="fas {{ $tx->type === 'credit' ? 'fa-arrow-down text-green-600 dark:text-green-400' : 'fa-arrow-up text-red-600 dark:text-red-400' }} text-xs sm:text-sm"></i>
                                </div>
                                <div class="min-w-0">
                                    <p class="font-medium text-gray-900 dark:text-white text-xs sm:text-sm">{{ $tx->created_at->format('d M Y') }}</p>
                                    <p class="text-[10px] sm:text-xs text-gray-500 dark:text-gray-400">{{ $tx->created_at->format('H:i') }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="py-3 sm:py-4 px-3 sm:px-4">
                            <span class="px-2 sm:px-3 py-1 sm:py-1.5 text-[10px] sm:text-xs font-semibold rounded-lg {{ $colors['bg'] }} {{ $colors['text'] }}">
                                {{ $tx->tabung_type }}
                            </span>
                        </td>
                        <td class="py-3 sm:py-4 px-3 sm:px-4 text-xs sm:text-sm text-gray-700 dark:text-gray-300 hidden sm:table-cell">
                            {{ $tx->description }}
                        </td>
                        <td class="py-3 sm:py-4 px-3 sm:px-4 text-right text-xs sm:text-sm font-bold {{ $tx->type === 'credit' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                            {{ $tx->type === 'credit' ? '+' : '-' }}
                            {{ number_format($tx->amount / 100, 2) }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @push('scripts')
    <script>
        $(document).ready(function() {
            $('#collectionTable').DataTable({
                order: [
                    [0, 'desc']
                ], // Sort by date desc
                pageLength: 10
            });
        });
    </script>
    @endpush
</x-admin-layout>