<x-admin-layout>
    <x-slot name="pageTitle">Dashboard</x-slot>
    <x-slot name="breadcrumb">Welcome back, {{ auth()->user()->name }}</x-slot>

    {{-- Admin Stats Cards --}}
    @if (auth()->user()->hasRole('Admin'))
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 sm:gap-4 mb-6 sm:mb-8">
        {{-- Total Users --}}
        <button onclick="openLocationModal()"
            class="card-hover bg-white dark:bg-gray-800 rounded-2xl p-4 sm:p-6 border border-gray-100 dark:border-gray-700 text-left group">
            <div class="flex items-center justify-between mb-3 sm:mb-4">
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                    <i class="fas fa-users text-blue-600 dark:text-blue-400 text-lg sm:text-xl"></i>
                </div>
                <span class="text-xs font-medium text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/20 px-2 py-1 rounded-lg hidden sm:inline">
                    <i class="fas fa-filter mr-1"></i> Filter
                </span>
            </div>
            <p class="text-xl sm:text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['total_users']) }}</p>
            <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-1">Total Users</p>
        </button>

        {{-- Active RizqMall --}}
        <div class="card-hover bg-white dark:bg-gray-800 rounded-2xl p-4 sm:p-6 border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between mb-3 sm:mb-4">
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                    <i class="fas fa-store text-green-600 dark:text-green-400 text-lg sm:text-xl"></i>
                </div>
            </div>
            <p class="text-xl sm:text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['total_rizqmall']) }}</p>
            <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-1">Active RizqMall</p>
        </div>

        {{-- Active Sandbox --}}
        <div class="card-hover bg-white dark:bg-gray-800 rounded-2xl p-4 sm:p-6 border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between mb-3 sm:mb-4">
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center">
                    <i class="fas fa-box text-purple-600 dark:text-purple-400 text-lg sm:text-xl"></i>
                </div>
            </div>
            <p class="text-xl sm:text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['total_sandbox']) }}</p>
            <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-1">Active Sandbox</p>
        </div>

        {{-- Total Profit --}}
        <div class="card-hover bg-white dark:bg-gray-800 rounded-2xl p-4 sm:p-6 border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between mb-3 sm:mb-4">
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-yellow-100 dark:bg-yellow-900/30 flex items-center justify-center">
                    <i class="fas fa-coins text-yellow-600 dark:text-yellow-400 text-lg sm:text-xl"></i>
                </div>
            </div>
            <p class="text-lg sm:text-3xl font-bold text-gray-900 dark:text-white">RM {{ number_format($stats['total_profit'], 2) }}</p>
            <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-1">Total Profit</p>
        </div>

        {{-- Total Subscriptions --}}
        <div class="card-hover bg-white dark:bg-gray-800 rounded-2xl p-4 sm:p-6 border border-gray-100 dark:border-gray-700 col-span-2 sm:col-span-1">
            <div class="flex items-center justify-between mb-3 sm:mb-4">
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center">
                    <i class="fas fa-file-invoice text-indigo-600 dark:text-indigo-400 text-lg sm:text-xl"></i>
                </div>
            </div>
            <p class="text-xl sm:text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['total_subscriptions']) }}</p>
            <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-1">Subscriptions</p>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6 mb-6 sm:mb-8">
        {{-- Referral Link & QR --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-4 sm:p-6 border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white">Your Referral</h3>
                <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center">
                    <i class="fas fa-link text-indigo-600 dark:text-indigo-400 text-sm sm:text-base"></i>
                </div>
            </div>
            <div class="mb-4 relative">
                <input id="referralLink"
                    class="w-full px-3 sm:px-4 py-2.5 sm:py-3 pr-10 sm:pr-12 rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 font-mono text-xs sm:text-sm text-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    readonly value="{{ route('register', ['ref' => auth()->user()->referral?->ref_code]) }}">
                <button class="absolute right-2 sm:right-3 top-1/2 -translate-y-1/2 p-1.5 sm:p-2 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors touch-target"
                    onclick="copyToClipboard()">
                    <i class="fas fa-copy text-gray-500 dark:text-gray-400"></i>
                </button>
            </div>
            <div class="flex justify-center p-3 sm:p-4 bg-white dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-700">
                <img src="{{ route('referrals.qr') }}" alt="QR Code" class="w-24 h-24 sm:w-32 sm:h-32 rounded-lg">
            </div>
        </div>

        {{-- RizqMall Shopping Card --}}
        <div class="gradient-success rounded-2xl p-4 sm:p-6 text-white">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base sm:text-lg font-semibold">Shop on RizqMall</h3>
                <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl bg-white/20 flex items-center justify-center">
                    <i class="fas fa-shopping-bag text-lg sm:text-xl"></i>
                </div>
            </div>
            <p class="text-white/80 mb-4 sm:mb-6 text-sm sm:text-base">Browse products, add to cart, and shop from verified vendors</p>
            <form method="POST" action="{{ route('rizqmall.customer-redirect') }}">
                @csrf
                <button type="submit" class="w-full px-4 sm:px-6 py-2.5 sm:py-3 bg-white text-green-600 rounded-xl font-semibold hover:bg-gray-100 transition-colors shadow-lg text-sm sm:text-base">
                    <i class="fas fa-arrow-right mr-2"></i> Start Shopping
                </button>
            </form>
        </div>

        {{-- Account Status Summary --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-4 sm:p-6 border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white">Quick Stats</h3>
                <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center">
                    <i class="fas fa-chart-pie text-purple-600 dark:text-purple-400 text-sm sm:text-base"></i>
                </div>
            </div>
            <div class="space-y-3 sm:space-y-4">
                @php
                    $activeCount = $accounts->filter(fn($a) => $a->active)->count();
                    $pendingCount = $accounts->filter(fn($a) => !$a->active)->count();
                @endphp
                <div class="flex items-center justify-between p-2.5 sm:p-3 bg-green-50 dark:bg-green-900/20 rounded-xl">
                    <div class="flex items-center gap-2 sm:gap-3">
                        <div class="w-2.5 h-2.5 sm:w-3 sm:h-3 bg-green-500 rounded-full"></div>
                        <span class="text-xs sm:text-sm font-medium text-gray-700 dark:text-gray-300">Active Accounts</span>
                    </div>
                    <span class="text-base sm:text-lg font-bold text-green-600 dark:text-green-400">{{ $activeCount }}</span>
                </div>
                <div class="flex items-center justify-between p-2.5 sm:p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-xl">
                    <div class="flex items-center gap-2 sm:gap-3">
                        <div class="w-2.5 h-2.5 sm:w-3 sm:h-3 bg-yellow-500 rounded-full"></div>
                        <span class="text-xs sm:text-sm font-medium text-gray-700 dark:text-gray-300">Pending Setup</span>
                    </div>
                    <span class="text-base sm:text-lg font-bold text-yellow-600 dark:text-yellow-400">{{ $pendingCount }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Account Status Section --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl p-4 sm:p-6 border border-gray-100 dark:border-gray-700 mb-6 sm:mb-8">
        <div class="flex items-center justify-between mb-4 sm:mb-6">
            <h3 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white">Account Status</h3>
        </div>

        @if (session('error'))
        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-600 dark:text-red-400 px-4 py-3 rounded-xl mb-4 flex items-center">
            <i class="fas fa-exclamation-circle mr-2"></i>
            {{ session('error') }}
        </div>
        @endif

        @php
            $today = now();
            $logos = [
                'rizqmall' => asset('rizqmall.jpeg'),
                'sandbox' => asset('sandboxlogo.png'),
            ];
        @endphp

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach ($accounts as $account)
                @php
                    if ($account->type === 'sandbox remaja' && $account->account_type_id == 3) {
                        continue;
                    }

                    $subscription = isset($subscriptions[$account->account_type_id])
                        ? $subscriptions[$account->account_type_id]->sortByDesc('created_at')->first()
                        : null;

                    $indicatorColor = 'bg-red-500';
                    $indicatorText = 'inactive';
                    $indicatorBg = 'bg-red-100 dark:bg-red-900/20 text-red-700 dark:text-red-400';
                    $expiryText = '';
                    $serialText = '';
                    $showButton = true;
                    $isExpired = false;

                    if ($account) {
                        $expires = $account->expires_at ? \Carbon\Carbon::parse($account->expires_at) : null;

                        if ($account->type === 'rizqmall' && $expires && $expires->isPast()) {
                            $indicatorColor = 'bg-orange-500';
                            $indicatorText = 'expired';
                            $indicatorBg = 'bg-orange-100 dark:bg-orange-900/20 text-orange-700 dark:text-orange-400';
                            $expiryText = 'Expired on ' . $expires->toFormattedDateString();
                            $isExpired = true;
                            $showButton = true;
                            if ($account->serial_number) {
                                $serialText = $account->serial_number;
                            }
                        } elseif ($account->active && (!$expires || $expires->isFuture())) {
                            $indicatorColor = 'bg-green-500';
                            $indicatorText = 'active';
                            $indicatorBg = 'bg-green-100 dark:bg-green-900/20 text-green-700 dark:text-green-400';
                            $showButton = false;
                            if ($expires) {
                                $expiryText = 'Valid until ' . $expires->toFormattedDateString();
                            }
                            if ($account->serial_number) {
                                $serialText = $account->serial_number;
                            }
                        } elseif (
                            $account->type === 'sandbox' &&
                            $subscription &&
                            $subscription->installments_paid > 0 &&
                            $subscription->installments_paid < $subscription->installments_total
                        ) {
                            $indicatorColor = 'bg-yellow-500';
                            $indicatorText = 'pending';
                            $indicatorBg = 'bg-yellow-100 dark:bg-yellow-900/20 text-yellow-700 dark:text-yellow-400';
                        }
                    }

                    $basePrice = $account->type === 'sandbox' ? 300 : 20;
                    $tax = round($basePrice * 0.08, 2);
                    $fpx = 1.0;

                    $showProgress = $account->type === 'sandbox' && $subscription && $subscription->installments_paid > 0 && $subscription->installments_paid < $subscription->installments_total;

                    if ($showProgress) {
                        $perInstallmentBase = round($basePrice / $subscription->installments_total, 2);
                        $paidBase = round($subscription->installments_paid * $perInstallmentBase, 2);
                        $progressPercent = ($subscription->installments_paid / $subscription->installments_total) * 100;
                        $nextAmount = round(($basePrice + $basePrice * 0.08) / $subscription->installments_total + $fpx, 2);
                    } else {
                        $perInstallmentBase = 0;
                        $paidBase = 0;
                        $progressPercent = 0;
                        $nextAmount = round($basePrice + $tax + $fpx, 2);
                    }

                    $fullPrice = round($basePrice + $tax + $fpx, 2);
                @endphp

                <div class="flex flex-col sm:flex-row sm:items-start gap-3 sm:gap-4 p-4 sm:p-5 bg-gray-50 dark:bg-gray-700/50 rounded-2xl border border-gray-100 dark:border-gray-600">
                    <div class="flex items-center gap-3 sm:block">
                        <div class="flex-shrink-0">
                            <img src="{{ $logos[$account->type] }}" alt="{{ ucfirst($account->type) }} Logo"
                                class="w-12 h-12 sm:w-14 sm:h-14 rounded-xl border-2 border-white dark:border-gray-600 shadow-md object-cover">
                        </div>
                        {{-- Mobile: Show status badge next to logo --}}
                        <div class="sm:hidden flex-1">
                            <h4 class="font-semibold text-gray-900 dark:text-white text-base">
                                {{ ucfirst($account->type) }}
                                @if ($account->type === 'sandbox') Malaysia @endif
                            </h4>
                            @if ($serialText)
                            <p class="text-xs font-mono text-indigo-600 dark:text-indigo-400">{{ $serialText }}</p>
                            @endif
                        </div>
                        <span class="sm:hidden px-2.5 py-1 text-xs font-semibold rounded-full {{ $indicatorBg }} whitespace-nowrap">
                            {{ ucfirst($indicatorText) }}
                        </span>
                    </div>

                    <div class="flex-1 min-w-0">
                        {{-- Desktop: Show title and status --}}
                        <div class="hidden sm:flex items-start justify-between mb-2">
                            <div>
                                <h4 class="font-semibold text-gray-900 dark:text-white text-lg">
                                    {{ ucfirst($account->type) }}
                                    @if ($account->type === 'sandbox') Malaysia @endif
                                </h4>
                                @if ($serialText)
                                <p class="text-sm font-mono text-indigo-600 dark:text-indigo-400">{{ $serialText }}</p>
                                @endif
                            </div>
                            <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $indicatorBg }}">
                                {{ ucfirst($indicatorText) }}
                            </span>
                        </div>

                        @if ($expiryText)
                        <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mb-2 sm:mb-3">{{ $expiryText }}</p>
                        @endif

                        @if ($showProgress)
                        <div class="mb-3">
                            <div class="flex justify-between text-xs sm:text-sm mb-1">
                                <span class="text-gray-600 dark:text-gray-400">Payment Progress</span>
                                <span class="font-medium text-gray-900 dark:text-white">{{ $subscription->installments_paid }}/{{ $subscription->installments_total }}</span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-2 sm:h-2.5">
                                <div class="bg-gradient-to-r from-indigo-500 to-purple-500 h-2 sm:h-2.5 rounded-full transition-all duration-300"
                                    style="width: {{ $progressPercent }}%"></div>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                RM {{ number_format($paidBase, 2) }} paid / RM {{ number_format($basePrice, 2) }} total
                            </p>
                        </div>
                        @endif

                        <div class="flex flex-wrap gap-2">
                            @if ($showButton)
                                @if ($account->type === 'sandbox')
                                    @if ($showProgress)
                                    <button x-data
                                        @click="$dispatch('open-pay-next-modal', {
                                            subscriptionId: {{ $subscription->id }},
                                            base: {{ $perInstallmentBase }},
                                            fullFinal: {{ $fullPrice }},
                                            paidCount: {{ $subscription->installments_paid }},
                                            totalInstallments: {{ $subscription->installments_total }}
                                        })"
                                        class="flex-1 sm:flex-none px-3 sm:px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs sm:text-sm font-semibold transition-colors shadow-md text-center">
                                        <i class="fas fa-credit-card mr-1 sm:mr-2"></i> <span class="hidden sm:inline">Pay Next </span>Installment
                                    </button>
                                    @else
                                    <button x-data
                                        @click="$dispatch('open-installment-modal', {
                                            plan: '{{ $account->type }}',
                                            label: '{{ ucfirst($account->type) }}',
                                            base: {{ $basePrice }},
                                            tax: {{ $tax }},
                                            fpx: {{ $fpx }},
                                            final: {{ $fullPrice }}
                                        })"
                                        class="flex-1 sm:flex-none px-3 sm:px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs sm:text-sm font-semibold transition-colors shadow-md text-center">
                                        <i class="fas fa-plus-circle mr-1 sm:mr-2"></i> Subscribe
                                    </button>
                                    @endif
                                @elseif ($account->type === 'rizqmall' && $isExpired)
                                <button x-data
                                    @click="$dispatch('open-modal', {
                                        plan: '{{ $account->type }}',
                                        label: 'Renew {{ ucfirst($account->type) }}',
                                        base: {{ $basePrice }},
                                        tax: {{ $tax }},
                                        fpx: {{ $fpx }},
                                        final: {{ $fullPrice }}
                                    })"
                                    class="flex-1 sm:flex-none px-3 sm:px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-xl text-xs sm:text-sm font-semibold transition-colors shadow-md text-center">
                                    <i class="fas fa-sync-alt mr-1 sm:mr-2"></i> Renew
                                </button>
                                @else
                                <button x-data
                                    @click="$dispatch('open-modal', {
                                        plan: '{{ $account->type }}',
                                        label: '{{ ucfirst($account->type) }}',
                                        base: {{ $basePrice }},
                                        tax: {{ $tax }},
                                        fpx: {{ $fpx }},
                                        final: {{ $fullPrice }}
                                    })"
                                    class="flex-1 sm:flex-none px-3 sm:px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs sm:text-sm font-semibold transition-colors shadow-md text-center">
                                    <i class="fas fa-plus-circle mr-1 sm:mr-2"></i> Subscribe
                                </button>
                                @endif
                            @else
                                @if ($account->type === 'rizqmall')
                                <form method="POST" action="{{ route('rizqmall.redirect') }}" class="flex-1 sm:flex-none">
                                    @csrf
                                    <button type="submit" class="w-full px-3 sm:px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-xl text-xs sm:text-sm font-semibold transition-colors shadow-md">
                                        <i class="fas fa-external-link-alt mr-1 sm:mr-2"></i> Go to Store
                                    </button>
                                </form>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- RizqMall Store Members Section --}}
    <div x-data="{
        loading: true,
        error: null,
        members: [],
        stats: null,
        store: null,
        init() {
            fetch('{{ route('rizqmall.members') }}')
                .then(res => res.json())
                .then(data => {
                    this.loading = false;
                    if (data.success && data.store) {
                        this.members = data.members;
                        this.stats = data.stats;
                        this.store = data.store;
                    }
                })
                .catch(err => {
                    this.loading = false;
                    this.error = 'Failed to load members';
                    console.error(err);
                });
        }
    }" x-show="store" x-cloak class="bg-white dark:bg-gray-800 rounded-2xl p-4 sm:p-6 border border-gray-100 dark:border-gray-700 mb-6 sm:mb-8">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 sm:gap-4 mb-4 sm:mb-6">
            <h3 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white">
                <i class="fas fa-users text-indigo-600 dark:text-indigo-400 mr-2"></i>
                <span x-text="store?.name"></span> Members
            </h3>
            <div class="flex gap-2 sm:gap-3 text-xs sm:text-sm" x-show="stats">
                <span class="px-2 sm:px-3 py-1 sm:py-1.5 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-400 rounded-lg font-medium">
                    Total: <span class="font-bold" x-text="stats?.total_members || 0"></span>
                </span>
                <span class="px-2 sm:px-3 py-1 sm:py-1.5 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 rounded-lg font-medium">
                    New: <span class="font-bold" x-text="stats?.new_this_month || 0"></span>
                </span>
            </div>
        </div>

        <div x-show="loading" class="flex justify-center py-8">
            <div class="animate-spin rounded-full h-10 w-10 border-4 border-indigo-500 border-t-transparent"></div>
        </div>

        <div x-show="!loading && members.length === 0" class="text-center py-8">
            <i class="fas fa-users text-4xl text-gray-300 dark:text-gray-600 mb-3"></i>
            <p class="text-gray-500 dark:text-gray-400">No members found.</p>
        </div>

        <div x-show="!loading && members.length > 0" class="overflow-x-auto -mx-4 sm:mx-0">
            <table id="membersTable" class="w-full min-w-[600px]">
                <thead>
                    <tr class="text-left text-xs sm:text-sm font-semibold text-gray-500 dark:text-gray-400 border-b border-gray-200 dark:border-gray-700">
                        <th class="pb-3 px-4">Customer</th>
                        <th class="pb-3 px-4">Join Method</th>
                        <th class="pb-3 px-4 hidden sm:table-cell">Joined Date</th>
                        <th class="pb-3 px-4">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    <template x-for="member in members" :key="member.id">
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <td class="py-3 sm:py-4 px-4">
                                <div class="flex items-center gap-2 sm:gap-3">
                                    <template x-if="member.customer_avatar">
                                        <img :src="member.customer_avatar" class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl object-cover flex-shrink-0">
                                    </template>
                                    <template x-if="!member.customer_avatar">
                                        <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl gradient-primary flex items-center justify-center flex-shrink-0">
                                            <span class="text-white font-semibold text-xs sm:text-sm" x-text="member.customer_name ? member.customer_name.charAt(0) : 'U'"></span>
                                        </div>
                                    </template>
                                    <div class="min-w-0">
                                        <p class="font-medium text-gray-900 dark:text-white text-sm truncate" x-text="member.customer_name"></p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 truncate" x-text="member.customer_email"></p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 sm:py-4 px-4">
                                <span class="px-2 sm:px-3 py-1 text-xs font-medium rounded-lg"
                                    :class="{
                                        'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400': member.join_method === 'qr_scan',
                                        'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400': member.join_method === 'referral' || member.join_method === 'ref_code',
                                        'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300': !['qr_scan', 'referral', 'ref_code'].includes(member.join_method)
                                    }"
                                    x-text="member.join_method === 'qr_scan' ? 'QR Scan' : (member.join_method === 'referral' || member.join_method === 'ref_code' ? 'Referral' : member.join_method)">
                                </span>
                            </td>
                            <td class="py-3 sm:py-4 px-4 text-xs sm:text-sm text-gray-600 dark:text-gray-400 hidden sm:table-cell" x-text="member.joined_at_human"></td>
                            <td class="py-3 sm:py-4 px-4">
                                <span class="px-2 sm:px-3 py-1 text-xs font-medium rounded-lg"
                                    :class="{
                                        'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400': member.status === 'active',
                                        'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400': member.status === 'inactive'
                                    }"
                                    x-text="member.status">
                                </span>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Users / Referrals Table --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl p-4 sm:p-6 border border-gray-100 dark:border-gray-700">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 sm:gap-4 mb-4 sm:mb-6">
            <h3 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white">
                <i class="fas fa-users text-indigo-600 dark:text-indigo-400 mr-2"></i>
                @if (auth()->user()->hasRole('Admin')) All Users @else Your Referrals @endif
            </h3>
        </div>

        {{-- Search form --}}
        <form method="GET" action="{{ route('dashboard') }}" class="mb-4 sm:mb-6">
            <div class="flex flex-col sm:flex-row gap-2">
                <div class="relative flex-1">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Search by name, email, or serial..."
                        class="w-full pl-10 pr-4 py-2.5 sm:py-3 rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:text-white">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                </div>
                <button type="submit" class="px-4 sm:px-6 py-2.5 sm:py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-medium transition-colors text-sm sm:text-base">
                    Search
                </button>
            </div>
        </form>

        <div class="overflow-x-auto -mx-4 sm:mx-0">
            <table id="usersTable" class="w-full min-w-[800px]">
                <thead>
                    <tr class="text-left text-xs sm:text-sm font-semibold text-gray-500 dark:text-gray-400 border-b border-gray-200 dark:border-gray-700">
                        <th class="pb-3 px-3 sm:px-4">RM No</th>
                        <th class="pb-3 px-3 sm:px-4">SB No</th>
                        <th class="pb-3 px-3 sm:px-4">Name</th>
                        <th class="pb-3 px-3 sm:px-4">Email</th>
                        <th class="pb-3 px-3 sm:px-4 hidden lg:table-cell">Phone</th>
                        <th class="pb-3 px-3 sm:px-4 hidden md:table-cell">Referrer</th>
                        @if (auth()->user()->hasRole('Admin'))
                        <th class="pb-3 px-3 sm:px-4 no-export">Action</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach ($users as $u)
                    @php
                        $rizqmall = $u->accounts->firstWhere('type', 'rizqmall');
                        $sandbox = $u->accounts->firstWhere('type', 'sandbox');
                    @endphp
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <td class="py-3 sm:py-4 px-3 sm:px-4">
                            <span class="px-1.5 sm:px-2 py-0.5 sm:py-1 text-[10px] sm:text-xs font-semibold rounded-lg {{ $rizqmall && $rizqmall->serial_number ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400' : 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400' }}">
                                {{ $rizqmall && $rizqmall->serial_number ? $rizqmall->serial_number : 'N/A' }}
                            </span>
                        </td>
                        <td class="py-3 sm:py-4 px-3 sm:px-4">
                            <span class="px-1.5 sm:px-2 py-0.5 sm:py-1 text-[10px] sm:text-xs font-semibold rounded-lg {{ $sandbox && $sandbox->serial_number ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400' : 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400' }}">
                                {{ $sandbox && $sandbox->serial_number ? $sandbox->serial_number : 'N/A' }}
                            </span>
                        </td>
                        <td class="py-3 sm:py-4 px-3 sm:px-4">
                            <div class="flex items-center gap-1 sm:gap-2">
                                <span class="user-name-display font-medium text-gray-900 dark:text-white text-xs sm:text-sm truncate max-w-[100px] sm:max-w-none">{{ $u->profile?->full_name ?? $u->name }}</span>
                                <button class="edit-name-btn p-1 hover:bg-gray-200 dark:hover:bg-gray-600 rounded text-gray-500 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors flex-shrink-0"
                                    data-user-id="{{ $u->id }}">
                                    <i class="fas fa-pencil-alt text-[10px] sm:text-xs"></i>
                                </button>
                            </div>
                            <form action="{{ route('admin.users.updateName', $u->id) }}" method="POST"
                                class="edit-name-form hidden flex items-center gap-2 mt-2">
                                @csrf
                                @method('PUT')
                                <input type="text" name="name" value="{{ $u->profile?->full_name ?? $u->name }}"
                                    class="flex-1 px-2 sm:px-3 py-1 sm:py-1.5 text-xs sm:text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-indigo-500 min-w-0">
                                <button type="submit" class="p-1 sm:p-1.5 bg-green-500 hover:bg-green-600 text-white rounded-lg flex-shrink-0">
                                    <i class="fas fa-check text-[10px] sm:text-xs"></i>
                                </button>
                                <button type="button" class="cancel-edit-name-btn p-1 sm:p-1.5 bg-gray-400 hover:bg-gray-500 text-white rounded-lg flex-shrink-0">
                                    <i class="fas fa-times text-[10px] sm:text-xs"></i>
                                </button>
                            </form>
                        </td>
                        <td class="py-3 sm:py-4 px-3 sm:px-4 text-xs sm:text-sm text-gray-600 dark:text-gray-400">
                            <span class="truncate block max-w-[120px] sm:max-w-none">{{ $u->email }}</span>
                        </td>
                        <td class="py-3 sm:py-4 px-3 sm:px-4 hidden lg:table-cell">
                            <div class="flex items-center gap-1 sm:gap-2">
                                <span class="user-phone-display text-xs sm:text-sm text-gray-600 dark:text-gray-400">{{ $u->profile?->phone ?? '-' }}</span>
                                <button class="edit-phone-btn p-1 hover:bg-gray-200 dark:hover:bg-gray-600 rounded text-gray-500 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors flex-shrink-0"
                                    data-phone-id="{{ $u->id }}">
                                    <i class="fas fa-pencil-alt text-[10px] sm:text-xs"></i>
                                </button>
                            </div>
                            <form action="{{ route('admin.users.updatePhone', $u->id) }}" method="POST"
                                class="edit-phone-form hidden flex items-center gap-2 mt-2">
                                @csrf
                                @method('PUT')
                                <input type="text" name="phone" value="{{ $u->profile?->phone ?? '-' }}"
                                    class="flex-1 px-2 sm:px-3 py-1 sm:py-1.5 text-xs sm:text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-indigo-500 min-w-0">
                                <button type="submit" class="p-1 sm:p-1.5 bg-green-500 hover:bg-green-600 text-white rounded-lg flex-shrink-0">
                                    <i class="fas fa-check text-[10px] sm:text-xs"></i>
                                </button>
                                <button type="button" class="cancel-edit-phone-btn p-1 sm:p-1.5 bg-gray-400 hover:bg-gray-500 text-white rounded-lg flex-shrink-0">
                                    <i class="fas fa-times text-[10px] sm:text-xs"></i>
                                </button>
                            </form>
                        </td>
                        <td class="py-3 sm:py-4 px-3 sm:px-4 text-xs sm:text-sm text-gray-600 dark:text-gray-400 hidden md:table-cell">
                            @if (auth()->user()->hasRole('Admin'))
                                {{ $u->referral?->parent?->name ?? '-' }}
                            @else
                                {{ $u->referral?->parent_id === auth()->id() ? 'YOU' : $u->referral?->parent?->name }}
                            @endif
                        </td>
                        @if (auth()->user()->hasRole('Admin'))
                        <td class="py-3 sm:py-4 px-3 sm:px-4 no-export">
                            <button data-user="{{ $u->id }}"
                                class="view-details px-2 sm:px-4 py-1.5 sm:py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs sm:text-sm font-medium rounded-lg transition-colors whitespace-nowrap">
                                <i class="fas fa-eye sm:mr-1"></i> <span class="hidden sm:inline">View</span>
                            </button>
                        </td>
                        @endif
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-4 sm:mt-6">
            {{ $users->links() }}
        </div>
    </div>

    {{-- Modals --}}
    @include('partials.payment-modals')

    {{-- Location Filter Modal --}}
    @if (auth()->user()->hasRole('Admin'))
    <div id="locationModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-4xl p-6 relative max-h-[90vh] overflow-y-auto">
            <button onclick="closeLocationModal()" class="absolute top-4 right-4 p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                <i class="fas fa-times text-gray-500 dark:text-gray-400"></i>
            </button>

            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6">
                <i class="fas fa-map-marker-alt text-indigo-600 dark:text-indigo-400 mr-2"></i>
                Users by Location
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Country</label>
                    <select id="countryFilter" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                        <option value="">All Countries</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">State</label>
                    <select id="stateFilter" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                        <option value="">All States</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">City</label>
                    <select id="cityFilter" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                        <option value="">All Cities</option>
                    </select>
                </div>
            </div>

            <button onclick="filterUsers()" class="mb-6 px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-medium transition-colors">
                <i class="fas fa-filter mr-2"></i> Apply Filters
            </button>

            <div class="mb-4 p-4 bg-indigo-50 dark:bg-indigo-900/20 rounded-xl">
                <p class="text-lg font-semibold text-gray-700 dark:text-gray-300">
                    Total Users: <span id="userCount" class="text-indigo-600 dark:text-indigo-400">0</span>
                </p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="text-left text-sm font-semibold text-gray-500 dark:text-gray-400 border-b border-gray-200 dark:border-gray-700">
                            <th class="pb-3 px-4">Name</th>
                            <th class="pb-3 px-4">Email</th>
                            <th class="pb-3 px-4">Country</th>
                            <th class="pb-3 px-4">State</th>
                            <th class="pb-3 px-4">City</th>
                        </tr>
                    </thead>
                    <tbody id="userTableBody" class="divide-y divide-gray-100 dark:divide-gray-700">
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                Click "Apply Filters" to load users
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    {{-- User Details Modal --}}
    @if (auth()->user()->hasRole('Admin'))
    <div id="detailsModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-2xl p-6 relative">
            <button onclick="document.getElementById('detailsModal').classList.add('hidden')"
                class="absolute top-4 right-4 p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                <i class="fas fa-times text-gray-500 dark:text-gray-400"></i>
            </button>

            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">
                <i class="fas fa-user text-indigo-600 dark:text-indigo-400 mr-2"></i>
                User Details
            </h3>
            <div id="modalContent" class="text-sm text-gray-700 dark:text-gray-300">
                <div class="flex justify-center py-8">
                    <div class="animate-spin rounded-full h-8 w-8 border-4 border-indigo-500 border-t-transparent"></div>
                </div>
            </div>
            <div class="mt-6 flex justify-end">
                <button onclick="document.getElementById('detailsModal').classList.add('hidden')"
                    class="px-6 py-2.5 bg-gray-600 hover:bg-gray-700 text-white rounded-xl font-medium transition-colors">
                    Close
                </button>
            </div>
        </div>
    </div>
    @endif

    @push('scripts')
    <script>
        // Copy referral link
        function copyToClipboard() {
            const input = document.getElementById('referralLink');
            input.select();
            input.setSelectionRange(0, 99999);
            navigator.clipboard.writeText(input.value)
                .then(() => {
                    // Show success feedback
                    const btn = input.nextElementSibling;
                    btn.innerHTML = '<i class="fas fa-check text-green-500"></i>';
                    setTimeout(() => {
                        btn.innerHTML = '<i class="fas fa-copy text-gray-500 dark:text-gray-400"></i>';
                    }, 2000);
                })
                .catch(err => console.error("Failed to copy:", err));
        }

        @if (auth()->user()->hasRole('Admin'))
        // Location Modal Functions
        function openLocationModal() {
            document.getElementById('locationModal').classList.remove('hidden');
            loadLocationFilters();
        }

        function closeLocationModal() {
            document.getElementById('locationModal').classList.add('hidden');
        }

        async function loadLocationFilters() {
            try {
                const res = await fetch("{{ route('admin.usersByLocation') }}");
                const data = await res.json();

                const countrySelect = document.getElementById('countryFilter');
                countrySelect.innerHTML = '<option value="">All Countries</option>';
                data.countries.forEach(country => {
                    if (country) countrySelect.innerHTML += `<option value="${country}">${country}</option>`;
                });

                const stateSelect = document.getElementById('stateFilter');
                stateSelect.innerHTML = '<option value="">All States</option>';
                data.states.forEach(state => {
                    if (state) stateSelect.innerHTML += `<option value="${state}">${state}</option>`;
                });

                const citySelect = document.getElementById('cityFilter');
                citySelect.innerHTML = '<option value="">All Cities</option>';
                data.cities.forEach(city => {
                    if (city) citySelect.innerHTML += `<option value="${city}">${city}</option>`;
                });
            } catch (err) {
                console.error('Failed to load filters', err);
            }
        }

        async function filterUsers() {
            const country = document.getElementById('countryFilter').value;
            const state = document.getElementById('stateFilter').value;

            const params = new URLSearchParams();
            if (country) params.append('country', country);
            if (state) params.append('state', state);

            try {
                const res = await fetch("{{ route('admin.usersByLocation') }}?" + params.toString());
                const data = await res.json();

                document.getElementById('userCount').textContent = data.count;

                const tbody = document.getElementById('userTableBody');
                if (data.users.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="5" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No users found</td></tr>';
                    return;
                }

                tbody.innerHTML = data.users.map(user => `
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <td class="py-3 px-4 text-gray-900 dark:text-white">${user.name || '-'}</td>
                        <td class="py-3 px-4 text-gray-600 dark:text-gray-400">${user.email || '-'}</td>
                        <td class="py-3 px-4 text-gray-600 dark:text-gray-400">${user.country || '-'}</td>
                        <td class="py-3 px-4 text-gray-600 dark:text-gray-400">${user.state || '-'}</td>
                        <td class="py-3 px-4 text-gray-600 dark:text-gray-400">${user.city || '-'}</td>
                    </tr>
                `).join('');
            } catch (err) {
                console.error('Failed to filter users', err);
            }
        }

        // View Details Modal
        document.querySelectorAll('.view-details').forEach(btn => {
            btn.addEventListener('click', async () => {
                const id = btn.getAttribute('data-user');
                document.getElementById('modalContent').innerHTML = '<div class="flex justify-center py-8"><div class="animate-spin rounded-full h-8 w-8 border-4 border-indigo-500 border-t-transparent"></div></div>';
                document.getElementById('detailsModal').classList.remove('hidden');

                const res = await fetch(`/admin/user/${id}/details`);
                const html = await res.text();
                document.getElementById('modalContent').innerHTML = html;
            });
        });
        @endif

        // Edit Name functionality
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.edit-name-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    const td = btn.closest('td');
                    td.querySelector('.user-name-display').parentElement.classList.add('hidden');
                    td.querySelector('.edit-name-form').classList.remove('hidden');
                    td.querySelector('input[name="name"]').focus();
                });
            });

            document.querySelectorAll('.cancel-edit-name-btn').forEach(cancel => {
                cancel.addEventListener('click', () => {
                    const td = cancel.closest('td');
                    td.querySelector('.edit-name-form').classList.add('hidden');
                    td.querySelector('.user-name-display').parentElement.classList.remove('hidden');
                });
            });

            document.querySelectorAll('.edit-name-form').forEach(form => {
                form.addEventListener('submit', async e => {
                    e.preventDefault();
                    const formData = new FormData(form);
                    const action = form.getAttribute('action');
                    const td = form.closest('td');

                    const res = await fetch(action, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': form.querySelector('[name="_token"]').value,
                        },
                        body: formData
                    });

                    if (res.ok) {
                        const data = await res.json();
                        td.querySelector('.user-name-display').textContent = data.name;
                        td.querySelector('.edit-name-form').classList.add('hidden');
                        td.querySelector('.user-name-display').parentElement.classList.remove('hidden');
                    } else {
                        alert('Failed to update name');
                    }
                });
            });

            // Edit Phone functionality
            document.querySelectorAll('.edit-phone-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    const td = btn.closest('td');
                    td.querySelector('.user-phone-display').parentElement.classList.add('hidden');
                    td.querySelector('.edit-phone-form').classList.remove('hidden');
                    td.querySelector('input[name="phone"]').focus();
                });
            });

            document.querySelectorAll('.cancel-edit-phone-btn').forEach(cancel => {
                cancel.addEventListener('click', () => {
                    const td = cancel.closest('td');
                    td.querySelector('.edit-phone-form').classList.add('hidden');
                    td.querySelector('.user-phone-display').parentElement.classList.remove('hidden');
                });
            });

            document.querySelectorAll('.edit-phone-form').forEach(form => {
                form.addEventListener('submit', async e => {
                    e.preventDefault();
                    const formData = new FormData(form);
                    const action = form.getAttribute('action');
                    const td = form.closest('td');

                    const res = await fetch(action, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': form.querySelector('[name="_token"]').value,
                        },
                        body: formData
                    });

                    if (res.ok) {
                        const data = await res.json();
                        td.querySelector('.user-phone-display').textContent = data.phone;
                        td.querySelector('.edit-phone-form').classList.add('hidden');
                        td.querySelector('.user-phone-display').parentElement.classList.remove('hidden');
                    } else {
                        alert('Failed to update phone');
                    }
                });
            });
        });
    </script>
    @endpush
</x-admin-layout>
