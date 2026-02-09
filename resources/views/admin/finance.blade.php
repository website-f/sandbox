<x-admin-layout>
    <x-slot name="pageTitle">Finance</x-slot>
    <x-slot name="breadcrumb">Unified ledger for subscriptions, wallet, and collections</x-slot>

    <div class="bg-white dark:bg-gray-800 rounded-2xl p-4 sm:p-6 border border-gray-100 dark:border-gray-700 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-6 gap-4">
            <div>
                <label class="text-xs font-semibold text-gray-600 dark:text-gray-300">From</label>
                <input type="date" name="from" value="{{ $from }}" class="mt-2 w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm px-3 py-2">
            </div>
            <div>
                <label class="text-xs font-semibold text-gray-600 dark:text-gray-300">To</label>
                <input type="date" name="to" value="{{ $to }}" class="mt-2 w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm px-3 py-2">
            </div>
            <div>
                <label class="text-xs font-semibold text-gray-600 dark:text-gray-300">Type</label>
                <select name="type" class="mt-2 w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm px-3 py-2">
                    <option value="all" {{ $type === 'all' ? 'selected' : '' }}>All</option>
                    <option value="subscription" {{ $type === 'subscription' ? 'selected' : '' }}>Subscription</option>
                    <option value="wallet" {{ $type === 'wallet' ? 'selected' : '' }}>Wallet</option>
                    <option value="collection" {{ $type === 'collection' ? 'selected' : '' }}>Collection</option>
                </select>
            </div>
            <div>
                <label class="text-xs font-semibold text-gray-600 dark:text-gray-300">Direction</label>
                <select name="direction" class="mt-2 w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm px-3 py-2">
                    <option value="all" {{ $direction === 'all' ? 'selected' : '' }}>All</option>
                    <option value="credit" {{ $direction === 'credit' ? 'selected' : '' }}>Credit</option>
                    <option value="debit" {{ $direction === 'debit' ? 'selected' : '' }}>Debit</option>
                </select>
            </div>
            <div>
                <label class="text-xs font-semibold text-gray-600 dark:text-gray-300">Status</label>
                <select name="status" class="mt-2 w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm px-3 py-2">
                    <option value="all" {{ $status === 'all' ? 'selected' : '' }}>All</option>
                    <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="success" {{ $status === 'success' ? 'selected' : '' }}>Success</option>
                    <option value="failed" {{ $status === 'failed' ? 'selected' : '' }}>Failed</option>
                    <option value="completed" {{ $status === 'completed' ? 'selected' : '' }}>Completed</option>
                </select>
            </div>
            <div>
                <label class="text-xs font-semibold text-gray-600 dark:text-gray-300">Account Type</label>
                <select name="account_type" class="mt-2 w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm px-3 py-2">
                    <option value="all" {{ $accountType === 'all' ? 'selected' : '' }}>All</option>
                    <option value="usahawan" {{ $accountType === 'usahawan' ? 'selected' : '' }}>Sandbox Usahawan</option>
                    <option value="remaja" {{ $accountType === 'remaja' ? 'selected' : '' }}>Sandbox Remaja</option>
                    <option value="awam" {{ $accountType === 'awam' ? 'selected' : '' }}>Sandbox Awam</option>
                </select>
            </div>
            <div>
                <label class="text-xs font-semibold text-gray-600 dark:text-gray-300">Min Amount (RM)</label>
                <input type="number" name="min_amount" value="{{ $minAmount }}" step="0.01" min="0" class="mt-2 w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm px-3 py-2" placeholder="0.00">
            </div>
            <div>
                <label class="text-xs font-semibold text-gray-600 dark:text-gray-300">Max Amount (RM)</label>
                <input type="number" name="max_amount" value="{{ $maxAmount }}" step="0.01" min="0" class="mt-2 w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm px-3 py-2" placeholder="0.00">
            </div>
            <div class="md:col-span-2">
                <label class="text-xs font-semibold text-gray-600 dark:text-gray-300">Search</label>
                <input type="text" name="q" value="{{ $q }}" class="mt-2 w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm px-3 py-2" placeholder="Name, email, reference">
            </div>
            <div class="md:col-span-6 flex flex-wrap gap-3">
                <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-semibold transition-colors shadow-md">
                    <i class="fas fa-filter mr-2"></i>Apply Filters
                </button>
                <a href="{{ route('admin.finance') }}" class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-xl text-sm font-semibold">
                    Reset
                </a>
                <a href="{{ route('admin.finance.export', request()->query()) }}" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-xl text-sm font-semibold transition-colors shadow-md">
                    <i class="fas fa-file-csv mr-2"></i>Export CSV
                </a>
            </div>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-4 border border-gray-100 dark:border-gray-700">
            <p class="text-xs text-gray-500">Total In</p>
            <p class="text-2xl font-bold text-green-600">RM {{ number_format(($summary['credits'] ?? 0) / 100, 2) }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-4 border border-gray-100 dark:border-gray-700">
            <p class="text-xs text-gray-500">Total Out</p>
            <p class="text-2xl font-bold text-red-600">RM {{ number_format(($summary['debits'] ?? 0) / 100, 2) }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-4 border border-gray-100 dark:border-gray-700">
            <p class="text-xs text-gray-500">Net</p>
            <p class="text-2xl font-bold text-indigo-600">RM {{ number_format(($summary['net'] ?? 0) / 100, 2) }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-4 border border-gray-100 dark:border-gray-700">
            <p class="text-xs text-gray-500">Transactions</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $summary['count'] ?? 0 }}</p>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-2xl p-4 sm:p-6 border border-gray-100 dark:border-gray-700 mb-6">
        <h3 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white mb-4">
            <i class="fas fa-chart-pie text-indigo-600 dark:text-indigo-400 mr-2"></i>
            Breakdown by Source
        </h3>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[600px]">
                <thead>
                    <tr class="text-left text-xs sm:text-sm font-semibold text-gray-500 dark:text-gray-400 border-b border-gray-200 dark:border-gray-700">
                        <th class="pb-3 px-3 sm:px-4">Source</th>
                        <th class="pb-3 px-3 sm:px-4">Count</th>
                        <th class="pb-3 px-3 sm:px-4">Credits</th>
                        <th class="pb-3 px-3 sm:px-4">Debits</th>
                        <th class="pb-3 px-3 sm:px-4">Net</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700 text-sm">
                    <tr>
                        <td class="py-3 px-3 sm:px-4 font-semibold">Subscription</td>
                        <td class="py-3 px-3 sm:px-4">{{ $bySource['subscription']['count'] ?? 0 }}</td>
                        <td class="py-3 px-3 sm:px-4 text-green-600">RM {{ number_format(($bySource['subscription']['credit'] ?? 0) / 100, 2) }}</td>
                        <td class="py-3 px-3 sm:px-4 text-red-600">RM {{ number_format(($bySource['subscription']['debit'] ?? 0) / 100, 2) }}</td>
                        <td class="py-3 px-3 sm:px-4 font-semibold">RM {{ number_format(($bySource['subscription']['net'] ?? 0) / 100, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="py-3 px-3 sm:px-4 font-semibold">Wallet</td>
                        <td class="py-3 px-3 sm:px-4">{{ $bySource['wallet']['count'] ?? 0 }}</td>
                        <td class="py-3 px-3 sm:px-4 text-green-600">RM {{ number_format(($bySource['wallet']['credit'] ?? 0) / 100, 2) }}</td>
                        <td class="py-3 px-3 sm:px-4 text-red-600">RM {{ number_format(($bySource['wallet']['debit'] ?? 0) / 100, 2) }}</td>
                        <td class="py-3 px-3 sm:px-4 font-semibold">RM {{ number_format(($bySource['wallet']['net'] ?? 0) / 100, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="py-3 px-3 sm:px-4 font-semibold">Collection</td>
                        <td class="py-3 px-3 sm:px-4">{{ $bySource['collection']['count'] ?? 0 }}</td>
                        <td class="py-3 px-3 sm:px-4 text-green-600">RM {{ number_format(($bySource['collection']['credit'] ?? 0) / 100, 2) }}</td>
                        <td class="py-3 px-3 sm:px-4 text-red-600">RM {{ number_format(($bySource['collection']['debit'] ?? 0) / 100, 2) }}</td>
                        <td class="py-3 px-3 sm:px-4 font-semibold">RM {{ number_format(($bySource['collection']['net'] ?? 0) / 100, 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-4 sm:p-6 border border-gray-100 dark:border-gray-700 lg:col-span-2">
            <h3 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white mb-4">
                <i class="fas fa-chart-line text-indigo-600 dark:text-indigo-400 mr-2"></i>
                Cash Flow (Monthly)
            </h3>
            <div class="h-64">
                <canvas id="financeTrendChart"></canvas>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-4 sm:p-6 border border-gray-100 dark:border-gray-700">
            <h3 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white mb-4">
                <i class="fas fa-chart-pie text-indigo-600 dark:text-indigo-400 mr-2"></i>
                Sources Mix
            </h3>
            <div class="h-64">
                <canvas id="financeSourceChart"></canvas>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-2xl p-4 sm:p-6 border border-gray-100 dark:border-gray-700">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 sm:gap-0 mb-4 sm:mb-6">
            <h3 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white">
                <i class="fas fa-receipt text-indigo-600 dark:text-indigo-400 mr-2"></i>
                Transactions
            </h3>
        </div>

        <div class="overflow-x-auto" style="-webkit-overflow-scrolling: touch;">
            <table id="financeTable" class="w-full min-w-[1000px]">
                <thead>
                    <tr class="text-left text-xs sm:text-sm font-semibold text-gray-500 dark:text-gray-400 border-b border-gray-200 dark:border-gray-700">
                        <th class="pb-3 px-3 sm:px-4">Date</th>
                        <th class="pb-3 px-3 sm:px-4">Source</th>
                        <th class="pb-3 px-3 sm:px-4">User</th>
                        <th class="pb-3 px-3 sm:px-4 hidden md:table-cell">Email</th>
                        <th class="pb-3 px-3 sm:px-4 hidden md:table-cell">Account Type</th>
                        <th class="pb-3 px-3 sm:px-4">Description</th>
                        <th class="pb-3 px-3 sm:px-4">Reference</th>
                        <th class="pb-3 px-3 sm:px-4">Direction</th>
                        <th class="pb-3 px-3 sm:px-4 text-right">Amount</th>
                        <th class="pb-3 px-3 sm:px-4">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($transactions as $tx)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <td class="py-3 sm:py-4 px-3 sm:px-4 text-xs sm:text-sm text-gray-600 dark:text-gray-400">
                                {{ optional($tx['date'])->format('d M Y, h:i A') }}
                            </td>
                            <td class="py-3 sm:py-4 px-3 sm:px-4 text-xs sm:text-sm">
                                <span class="px-2 py-1 rounded-lg text-[10px] sm:text-xs font-semibold bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300">
                                    {{ $tx['source'] }}
                                </span>
                            </td>
                            <td class="py-3 sm:py-4 px-3 sm:px-4 text-xs sm:text-sm text-gray-800 dark:text-gray-200">
                                {{ $tx['user'] ?? '—' }}
                            </td>
                            <td class="py-3 sm:py-4 px-3 sm:px-4 text-xs sm:text-sm text-gray-500 hidden md:table-cell">
                                {{ $tx['email'] ?? '-' }}
                            </td>
                            <td class="py-3 sm:py-4 px-3 sm:px-4 text-xs sm:text-sm text-gray-500 hidden md:table-cell">
                                {{ $tx['account_type'] ?? '-' }}
                            </td>
                            <td class="py-3 sm:py-4 px-3 sm:px-4 text-xs sm:text-sm">
                                {{ $tx['description'] ?? '-' }}
                            </td>
                            <td class="py-3 sm:py-4 px-3 sm:px-4 text-xs sm:text-sm text-gray-500">
                                {{ $tx['reference'] ?? '-' }}
                            </td>
                            <td class="py-3 sm:py-4 px-3 sm:px-4 text-xs sm:text-sm">
                                <span class="px-2 py-1 rounded-lg text-[10px] sm:text-xs font-semibold {{ $tx['direction'] === 'debit' ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300' : 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300' }}">
                                    {{ ucfirst($tx['direction']) }}
                                </span>
                            </td>
                            <td class="py-3 sm:py-4 px-3 sm:px-4 text-right text-xs sm:text-sm font-bold {{ $tx['direction'] === 'debit' ? 'text-red-600' : 'text-green-600' }}">
                                {{ $tx['direction'] === 'debit' ? '-' : '+' }}
                                RM {{ number_format(($tx['amount'] ?? 0) / 100, 2) }}
                            </td>
                            <td class="py-3 sm:py-4 px-3 sm:px-4">
                                <span class="px-2 py-1 rounded-lg text-[10px] sm:text-xs font-semibold
                                    {{ $tx['status'] === 'success' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300' : '' }}
                                    {{ $tx['status'] === 'pending' ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300' : '' }}
                                    {{ $tx['status'] === 'failed' ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300' : '' }}
                                    {{ $tx['status'] === 'completed' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300' : '' }}
                                ">
                                    {{ ucfirst($tx['status']) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="py-6 text-center text-sm text-gray-500">
                                No transactions found for the selected filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                $('#financeTable').DataTable({
                    order: [
                        [0, 'desc']
                    ],
                    pageLength: 25
                });
            });
        </script>
        <script>
            const trendLabels = @json($chartLabels);
            const trendCredits = @json($chartCredits);
            const trendDebits = @json($chartDebits);
            const trendNet = @json($chartNet);
            const sourceLabels = @json($sourceLabels);
            const sourceTotals = @json($sourceTotals);

            const trendCtx = document.getElementById('financeTrendChart');
            if (trendCtx) {
                new Chart(trendCtx, {
                    type: 'line',
                    data: {
                        labels: trendLabels,
                        datasets: [{
                                label: 'Credits',
                                data: trendCredits,
                                borderColor: '#22c55e',
                                backgroundColor: 'rgba(34, 197, 94, 0.15)',
                                tension: 0.35,
                                fill: true
                            },
                            {
                                label: 'Debits',
                                data: trendDebits,
                                borderColor: '#ef4444',
                                backgroundColor: 'rgba(239, 68, 68, 0.15)',
                                tension: 0.35,
                                fill: true
                            },
                            {
                                label: 'Net',
                                data: trendNet,
                                borderColor: '#6366f1',
                                backgroundColor: 'rgba(99, 102, 241, 0.15)',
                                tension: 0.35,
                                fill: false
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top'
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: (value) => `RM ${value}`
                                }
                            }
                        }
                    }
                });
            }

            const sourceCtx = document.getElementById('financeSourceChart');
            if (sourceCtx) {
                new Chart(sourceCtx, {
                    type: 'doughnut',
                    data: {
                        labels: sourceLabels,
                        datasets: [{
                            data: sourceTotals,
                            backgroundColor: ['#6366f1', '#22c55e', '#f59e0b']
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });
            }
        </script>
    @endpush
</x-admin-layout>
