<x-admin-layout>
    <x-slot name="pageTitle">My Wallet</x-slot>
    <x-slot name="breadcrumb">Let's check your wallet today</x-slot>

    {{-- Success/Error Message --}}
    @if(session('success'))
    <div class="mb-6 p-4 rounded-xl bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 font-semibold flex items-center">
        <i class="fas fa-check-circle mr-3 text-green-500"></i>
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="mb-6 p-4 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 font-semibold flex items-center">
        <i class="fas fa-exclamation-circle mr-3 text-red-500"></i>
        {{ session('error') }}
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        {{-- Balance Card --}}
        <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-100 dark:border-gray-700">
            <div class="gradient-primary rounded-2xl p-6 text-white mb-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-white/80 text-sm font-medium">Total Balance</p>
                        <h2 class="text-4xl font-bold mt-1">
                            RM {{ number_format(($user->wallet->balance ?? 0) / 100, 2) }}
                        </h2>
                        <p class="text-white/60 text-sm mt-1">MYR</p>
                    </div>
                    <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center">
                        <i class="fas fa-wallet text-3xl"></i>
                    </div>
                </div>
            </div>

            <button class="w-full py-4 bg-green-500 hover:bg-green-600 text-white rounded-xl font-bold transition-colors shadow-lg">
                <i class="fas fa-plus-circle mr-2"></i> Topup Wallet
            </button>
        </div>

        {{-- Quick Stats --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-100 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                <i class="fas fa-chart-line text-indigo-600 dark:text-indigo-400 mr-2"></i>
                Overall Balance
            </h3>

            <div class="flex items-center space-x-2 mb-4">
                <span class="text-2xl font-bold text-gray-900 dark:text-white">
                    RM {{ number_format(($user->wallet->balance ?? 0) / 100, 2) }}
                </span>
                <span class="px-2 py-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 text-xs font-semibold rounded-lg">
                    <i class="fas fa-arrow-up mr-1"></i>+20%
                </span>
            </div>

            <div class="w-full h-48">
                <canvas id="overAllBalance"></canvas>
            </div>
        </div>
    </div>

    {{-- Transaction History --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-100 dark:border-gray-700">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                <i class="fas fa-history text-indigo-600 dark:text-indigo-400 mr-2"></i>
                Transaction History
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table id="transactionsTable" class="w-full">
                <thead>
                    <tr class="text-left text-sm font-semibold text-gray-500 dark:text-gray-400 border-b border-gray-200 dark:border-gray-700">
                        <th class="pb-3 px-4">Date</th>
                        <th class="pb-3 px-4">Description</th>
                        <th class="pb-3 px-4 text-right">Amount</th>
                        <th class="pb-3 px-4 text-center">Type</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($user->wallet->transactions()->latest()->take(20)->get() as $tx)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <td class="py-4 px-4 text-sm text-gray-600 dark:text-gray-400">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl {{ $tx->type === 'credit' ? 'bg-green-100 dark:bg-green-900/30' : 'bg-red-100 dark:bg-red-900/30' }} flex items-center justify-center">
                                    <i class="fas {{ $tx->type === 'credit' ? 'fa-arrow-down text-green-600 dark:text-green-400' : 'fa-arrow-up text-red-600 dark:text-red-400' }}"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900 dark:text-white">{{ $tx->created_at->format('d M Y') }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $tx->created_at->format('h:i A') }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-4 text-sm font-medium text-gray-800 dark:text-gray-200">
                            {{ $tx->description ?? '-' }}
                        </td>
                        <td class="py-4 px-4 text-right text-sm font-bold {{ $tx->type === 'credit' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                            {{ $tx->type === 'credit' ? '+' : '-' }}
                            RM {{ number_format($tx->amount / 100, 2) }}
                        </td>
                        <td class="py-4 px-4 text-center">
                            <span class="px-3 py-1.5 text-xs font-semibold rounded-lg {{ $tx->type === 'credit' ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400' : 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400' }}">
                                {{ ucfirst($tx->type) }}
                            </span>
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
            $('#transactionsTable').DataTable({
                order: [
                    [0, 'desc']
                ], // Sort by date desc
                pageLength: 10
            });
        });
    </script>
    <script>
        let months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];

        // Data from controller
        let transactionData = @json($chartData);

        // Check if dark mode is enabled
        const isDarkMode = document.documentElement.classList.contains('dark');
        const gridColor = isDarkMode ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.1)';
        const textColor = isDarkMode ? '#9CA3AF' : '#6B7280';

        let ctx = document.getElementById("overAllBalance").getContext("2d");
        let chart = new Chart(ctx, {
            type: "line",
            data: {
                labels: months,
                datasets: [{
                    label: "Net Monthly Change",
                    data: transactionData.map(v => v / 100),
                    borderColor: "#6366F1",
                    backgroundColor: (ctx) => {
                        const gradient = ctx.chart.ctx.createLinearGradient(0, 0, 0, 200);
                        gradient.addColorStop(0, "rgba(99, 102, 241, 0.3)");
                        gradient.addColorStop(1, "rgba(99, 102, 241, 0)");
                        return gradient;
                    },
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: "#6366F1",
                    pointBorderColor: isDarkMode ? "#1F2937" : "#fff",
                    pointBorderWidth: 3,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: gridColor,
                        },
                        ticks: {
                            color: textColor,
                            callback: (value) => `RM ${value.toFixed(0)}`,
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: textColor
                        }
                    }
                }
            }
        });
    </script>
    @endpush
</x-admin-layout>