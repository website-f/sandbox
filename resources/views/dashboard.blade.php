<x-app-layout>
    <x-slot name="header">
        <h2 class="font-extrabold text-3xl text-gray-900 tracking-tight">
            Welcome, {{ auth()->user()->name }}
        </h2>
        <p class="mt-1 text-gray-500">Manage your referral network and accounts here</p>
    </x-slot>

    <div class="py-12">
        {{-- Admin Stats Cards (Add right after the opening div of py-12) --}}
        @if (auth()->user()->hasRole('Admin'))
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mb-8">
                <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-3 gap-4">
                    {{-- Total Users --}}
                    <button onclick="openLocationModal()"
                        class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl shadow-lg p-6 hover:shadow-2xl transition-all duration-300 text-left">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-blue-100 text-sm font-medium">Total Users</p>
                                <p class="text-white text-3xl font-bold mt-2">{{ number_format($stats['total_users']) }}
                                </p>
                            </div>
                            <div class="bg-white bg-opacity-20 rounded-full p-3">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                        </div>
                        <p class="text-blue-100 text-xs mt-2">Click to view by location</p>
                    </button>

                    {{-- Active RizqMall --}}
                    <div
                        class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl shadow-lg p-6 hover:shadow-2xl transition-all duration-300">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-green-100 text-sm font-medium">Active RizqMall</p>
                                <p class="text-white text-3xl font-bold mt-2">
                                    {{ number_format($stats['total_rizqmall']) }}</p>
                            </div>
                            <div class="bg-white bg-opacity-20 rounded-full p-3">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    {{-- Active Sandbox --}}
                    <div
                        class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl shadow-lg p-6 hover:shadow-2xl transition-all duration-300">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-purple-100 text-sm font-medium">Active Sandbox</p>
                                <p class="text-white text-3xl font-bold mt-2">
                                    {{ number_format($stats['total_sandbox']) }}</p>
                            </div>
                            <div class="bg-white bg-opacity-20 rounded-full p-3">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    {{-- Total Profit --}}
                    <div
                        class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-2xl shadow-lg p-6 hover:shadow-2xl transition-all duration-300">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-yellow-100 text-sm font-medium">Total Profit</p>
                                <p class="text-white text-3xl font-bold mt-2">RM
                                    {{ number_format($stats['total_profit'], 2) }}</p>
                            </div>
                            <div class="bg-white bg-opacity-20 rounded-full p-3">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>


                    {{-- Total Subscriptions --}}
                    <div
                        class="bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-2xl shadow-lg p-6 hover:shadow-2xl transition-all duration-300">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-indigo-100 text-sm font-medium">Subscriptions</p>
                                <p class="text-white text-3xl font-bold mt-2">
                                    {{ number_format($stats['total_subscriptions']) }}</p>
                            </div>
                            <div class="bg-white bg-opacity-20 rounded-full p-3">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Location Filter Modal --}}
            <div id="locationModal"
                class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
                <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl p-6 relative max-h-[90vh] overflow-y-auto">
                    <button onclick="closeLocationModal()"
                        class="absolute top-3 right-3 text-gray-600 hover:text-gray-800">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>

                    <h3 class="text-2xl font-bold text-gray-800 mb-6">Users by Location</h3>

                    {{-- Filters --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Country</label>
                            <select id="countryFilter" class="w-full border border-gray-300 rounded-lg px-4 py-2">
                                <option value="">All Countries</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">State</label>
                            <select id="stateFilter" class="w-full border border-gray-300 rounded-lg px-4 py-2">
                                <option value="">All States</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">City</label>
                            <select id="cityFilter" class="w-full border border-gray-300 rounded-lg px-4 py-2">
                                <option value="">All Cities</option>
                            </select>
                        </div>
                    </div>

                    <button onclick="filterUsers()"
                        class="mb-4 px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                        Apply Filters
                    </button>

                    {{-- Results --}}
                    <div class="mb-4">
                        <p class="text-lg font-semibold text-gray-700">
                            Total Users: <span id="userCount" class="text-indigo-600">0</span>
                        </p>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full border border-gray-200 rounded-lg">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Name</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Email</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Country</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">State</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">City</th>
                                </tr>
                            </thead>
                            <tbody id="userTableBody" class="divide-y divide-gray-100">
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                        Click "Apply Filters" to load users
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <script>
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

                        // Populate country filter
                        const countrySelect = document.getElementById('countryFilter');
                        countrySelect.innerHTML = '<option value="">All Countries</option>';
                        data.countries.forEach(country => {
                            if (country) countrySelect.innerHTML += `<option value="${country}">${country}</option>`;
                        });

                        // Populate state filter
                        const stateSelect = document.getElementById('stateFilter');
                        stateSelect.innerHTML = '<option value="">All States</option>';
                        data.states.forEach(state => {
                            if (state) stateSelect.innerHTML += `<option value="${state}">${state}</option>`;
                        });

                        // Populate city filter
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
                            tbody.innerHTML =
                                '<tr><td colspan="5" class="px-4 py-8 text-center text-gray-500">No users found</td></tr>';
                            return;
                        }

                        tbody.innerHTML = data.users.map(user => `
                    <tr>
                        <td class="px-4 py-3 text-sm">${user.name || '-'}</td>
                        <td class="px-4 py-3 text-sm">${user.email || '-'}</td>
                        <td class="px-4 py-3 text-sm">${user.country || '-'}</td>
                        <td class="px-4 py-3 text-sm">${user.state || '-'}</td>
                        <td class="px-4 py-3 text-sm">${user.city || '-'}</td>
                    </tr>
                `).join('');
                    } catch (err) {
                        console.error('Failed to filter users', err);
                    }
                }
            </script>
        @endif
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-3 gap-8">

            <div class="bg-white rounded-2xl shadow-lg p-6 hover:shadow-2xl transition-shadow duration-300">
                <h3 class="text-xl font-semibold text-gray-800 mb-4">Your Referral Link & QR</h3>
                <div class="mb-4 relative">
                    <input
                        class="w-full p-3 pr-12 rounded-xl border border-gray-200 bg-gray-50 font-mono text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-400"
                        readonly value="{{ route('register', ['ref' => auth()->user()->referral?->ref_code]) }}">
                    <button class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-indigo-600"
                        onclick="copyToClipboard(this)">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M8 2a2 2 0 00-2 2v2H4a2 2 0 00-2 2v8a2 2 0 002 2h8a2 2 0 002-2v-2h2a2 2 0 002-2V4a2 2 0 00-2-2h-8zM6 4a1 1 0 011-1h6a1 1 0 011 1v2H7a1 1 0 00-1 1v6H4a1 1 0 01-1-1V8a1 1 0 011-1h2V4zm6 6a1 1 0 011-1h4a1 1 0 011 1v8a1 1 0 01-1 1h-4a1 1 0 01-1-1v-8z" />
                        </svg>
                    </button>
                </div>
                <div class="flex justify-center">
                    <img src="{{ route('referrals.qr') }}" alt="QR Code"
                        class="w-36 h-36 rounded-xl border border-gray-200 shadow-md">
                </div>
            </div>

            {{-- RizqMall Shopping Card --}}
            <div
                class="bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl shadow-lg p-6 hover:shadow-2xl transition-all duration-300 text-white">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-semibold">Shop on RizqMall</h3>
                    <div class="bg-white bg-opacity-20 rounded-full p-3">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                    </div>
                </div>
                <p class="text-emerald-100 mb-6">Browse products, add to cart, and shop from verified vendors</p>
                <form method="POST" action="{{ route('rizqmall.customer-redirect') }}">
                    @csrf
                    <button type="submit"
                        class="w-full px-5 py-3 bg-white text-emerald-600 rounded-xl hover:bg-emerald-50 transition font-semibold shadow-md">
                        Start Shopping
                    </button>
                </form>
            </div>

            <div
                class="bg-white rounded-2xl shadow-lg p-6 hover:shadow-2xl transition-shadow duration-300 md:col-span-2">
                <h3 class="text-xl font-semibold text-gray-800 mb-4">Account Status</h3>
                @if (session('error'))
                    <div class="bg-red-100 text-red-600 px-4 py-2 rounded mb-4">
                        {{ session('error') }}
                    </div>
                @endif

                <ul class="space-y-4">
                    @php
                        $today = now();
                    @endphp

                    <ul class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        @php
                            $logos = [
                                'rizqmall' => asset('rizqmall.jpeg'),
                                'sandbox' => asset('sandboxlogo.png'),
                            ];
                        @endphp

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
                                $expiryText = '';
                                $serialText = '';
                                $showButton = true;

                                if ($account) {
                                    $expires = $account->expires_at
                                        ? \Carbon\Carbon::parse($account->expires_at)
                                        : null;

                                    if ($account->active) {
                                        $indicatorColor = 'bg-green-500';
                                        $indicatorText = 'active';
                                        $showButton = false;
                                        if ($expires) {
                                            $expiryText = 'Valid until ' . $expires->toFormattedDateString();
                                        }
                                        if ($account->serial_number) {
                                            $serialText = "Serial: {$account->serial_number}";
                                        }
                                    } elseif (
                                        $account->type === 'sandbox' &&
                                        $subscription &&
                                        $subscription->installments_paid > 0 &&
                                        $subscription->installments_paid < $subscription->installments_total
                                    ) {
                                        $indicatorColor = 'bg-yellow-500';
                                        $indicatorText = 'pending';
                                    }
                                }

                                $basePrice = $account->type === 'sandbox' ? 300 : 20;
                                $tax = round($basePrice * 0.08, 2);
                                $fpx = 1.0;

                                // Progress is based **only on basePrice**
                                $showProgress =
                                    $account->type === 'sandbox' &&
                                    $subscription &&
                                    $subscription->installments_paid > 0 &&
                                    $subscription->installments_paid < $subscription->installments_total;

                                if ($showProgress) {
                                    $perInstallmentBase = round($basePrice / $subscription->installments_total, 2); // Base only
                                    $paidBase = round($subscription->installments_paid * $perInstallmentBase, 2);
                                    $progressPercent =
                                        ($subscription->installments_paid / $subscription->installments_total) * 100;

                                    // Next installment **with tax + FPX**
                                    $nextAmount = round(
                                        ($basePrice + $basePrice * 0.08) / $subscription->installments_total + $fpx,
                                        2,
                                    );
                                } else {
                                    $perInstallmentBase = 0;
                                    $paidBase = 0;
                                    $progressPercent = 0;
                                    $nextAmount = round($basePrice + $tax + $fpx, 2); // First payment
                                }

                                // Full price for one-time payment (base + tax + fpx)
                                $fullPrice = round($basePrice + $tax + $fpx, 2);
                            @endphp

                            <li
                                class="flex flex-col md:flex-row justify-between items-center bg-white rounded-2xl shadow-lg p-5 gap-4 md:gap-6">
                                <div class="flex-shrink-0">
                                    <img src="{{ $logos[$account->type] }}" alt="{{ ucfirst($account->type) }} Logo"
                                        class="w-12 h-12 rounded-full border border-gray-200 shadow-sm object-cover">
                                </div>

                                <div class="flex-1 flex flex-col gap-2">
                                    <span class="font-semibold text-gray-800 text-lg">{{ ucfirst($account->type) }}
                                        @if ($account->type === 'sandbox')
                                            Malaysia
                                        @endif
                                    </span>
                                    <span class="flex items-center gap-2 text-sm text-gray-500">
                                        <span class="inline-block w-3 h-3 rounded-full {{ $indicatorColor }}"></span>
                                        {{ ucfirst($indicatorText) }}
                                    </span>

                                    @if ($expiryText)
                                        <span class="text-xs text-gray-400">{{ $expiryText }}</span>
                                    @endif
                                    @if ($serialText)
                                        <span class="text-xs font-semibold text-indigo-600">{{ $serialText }}</span>
                                    @endif

                                    {{-- Sandbox progress --}}
                                    @if ($showProgress)
                                        <div class="mt-2 w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                                            <div class="bg-indigo-600 h-3 transition-all duration-300"
                                                style="width: {{ $progressPercent }}%"></div>
                                        </div>
                                        <p class="text-xs text-gray-600 mt-1">
                                            RM {{ number_format($paidBase, 2) }} paid / RM
                                            {{ number_format($basePrice, 2) }} total
                                        </p>
                                    @endif

                                    {{-- Buttons --}}
                                    <div>
                                        @if ($showButton)
                                            @if ($account->type === 'sandbox')
                                                @if ($showProgress)
                                                    <button x-data
                                                        @click="$dispatch('open-pay-next-modal', {
                                                   subscriptionId: {{ $subscription->id }},
                                                   base: {{ $perInstallmentBase }},          // base per installment
                                                   fullFinal: {{ $fullPrice }},             // full price with tax+fpx
                                                   paidCount: {{ $subscription->installments_paid }},
                                                   totalInstallments: {{ $subscription->installments_total }}
                                               })"
                                                        class="px-5 py-2 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition font-semibold shadow">
                                                        Pay Next Installment
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
                                                        class="px-5 py-2 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition font-semibold shadow">
                                                        Subscribe
                                                    </button>
                                                @endif
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
                                                    class="px-5 py-2 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition font-semibold shadow">
                                                    Subscribe
                                                </button>
                                            @endif
                                        @else
                                            @if ($account->type === 'rizqmall')
                                                <form method="POST" action="{{ route('rizqmall.redirect') }}"
                                                    class="inline">
                                                    @csrf
                                                    <button type="submit"
                                                        class="px-5 py-2 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition font-semibold shadow">
                                                        Go to Store
                                                    </button>
                                                </form>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            </li>
                        @endforeach


                    </ul>


                    {{-- Modal --}}

                    <div x-data="{
                        open: false,
                        plan: '',
                        label: '',
                        // Full subscription cost details
                        fullBase: 0,
                        fullTax: 0,
                        fullFpx: 0,
                        fullFinal: 0,
                        installments: 1,
                    
                        // Computed properties for the currently selected installment amount breakdown
                        get installmentBase() {
                            return (this.fullBase / this.installments).toFixed(2);
                        },
                        get installmentTax() {
                            return (this.fullTax / this.installments).toFixed(2);
                        },
                        get installmentFpx() {
                            return this.fullFpx.toFixed(2); // ✅ Fixed FPX per installment
                        },
                        get installmentAmount() {
                            // Recalculate based on installment component prices to ensure accuracy
                            return (parseFloat(this.installmentBase) + parseFloat(this.installmentTax) + parseFloat(this.installmentFpx)).toFixed(2);
                        }
                    }"
                        x-on:open-installment-modal.window="
                    open = true; 
                    plan = $event.detail.plan; 
                    label = $event.detail.label; 
                    fullBase = parseFloat($event.detail.base); 
                    fullTax = parseFloat($event.detail.tax); 
                    fullFpx = parseFloat($event.detail.fpx); 
                    fullFinal = parseFloat($event.detail.final); 
                    installments = 1; // Default to full payment
                "
                        x-show="open" x-cloak style="display:none"
                        class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75 backdrop-blur-sm p-4">

                        <div @click.away="open = false"
                            class="bg-white rounded-2xl shadow-2xl w-full max-w-lg p-8 space-y-6 transform transition-all duration-300">
                            <h2 class="text-2xl font-bold text-gray-800 border-b pb-3"
                                x-text="label + ' Subscription'"></h2>

                            <p class="text-lg font-semibold text-gray-700">Choose your payment plan:</p>

                            <div class="grid grid-cols-2 gap-4">
                                {{-- Option 1: Full Payment --}}
                                <button @click="installments = 1"
                                    :class="{
                                        'border-indigo-600 ring-2 ring-indigo-300': installments ==
                                            1,
                                        'border-gray-300 hover:border-indigo-400': installments != 1
                                    }"
                                    class="p-4 border-2 rounded-xl text-left transition duration-200">
                                    <p class="font-bold text-lg" :class="{ 'text-indigo-600': installments == 1 }">Full
                                        Payment</p>
                                    <p class="text-xl font-extrabold mt-1">RM <span
                                            x-text="fullFinal.toFixed(2)"></span></p>
                                    <p class="text-xs text-gray-500">One-time charge. Full access.</p>
                                </button>

                                {{-- Option 2: 3 Installments --}}
                                <button @click="installments = 3"
                                    :class="{
                                        'border-indigo-600 ring-2 ring-indigo-300': installments ==
                                            3,
                                        'border-gray-300 hover:border-indigo-400': installments != 3
                                    }"
                                    class="p-4 border-2 rounded-xl text-left transition duration-200">
                                    <p class="font-bold text-lg" :class="{ 'text-indigo-600': installments == 3 }">3
                                        Installments</p>
                                    <p class="text-xl font-extrabold mt-1">
                                        RM
                                        <span
                                            x-text="(
                                       (fullBase + fullTax) / 3 + fullFpx
                                   ).toFixed(2)">
                                        </span>
                                    </p>

                                    <p class="text-xs text-gray-500">per payment (3x total)</p>
                                </button>
                            </div>

                            {{-- Payment Breakdown for selected option --}}
                            <div class="bg-gray-50 p-4 rounded-xl space-y-2 text-sm">
                                <h3 class="font-bold text-base text-gray-800 mb-2"
                                    x-text="'Payment Breakdown: ' + (installments == 1 ? 'Full Payment' : 'First Installment')">
                                </h3>

                                <div x-show="installments == 1">
                                    <div class="flex justify-between"><span>Base Price</span> <span>RM <span
                                                x-text="fullBase.toFixed(2)"></span></span></div>
                                    <div class="flex justify-between"><span>Tax (8%)</span> <span>RM <span
                                                x-text="fullTax.toFixed(2)"></span></span></div>
                                    <div class="flex justify-between"><span>FPX Charge</span> <span>RM <span
                                                x-text="fullFpx.toFixed(2)"></span></span></div>
                                </div>

                                <div x-show="installments > 1">
                                    <div class="flex justify-between"><span>Base Price (per installment)</span>
                                        <span>RM <span x-text="installmentBase"></span></span>
                                    </div>
                                    <div class="flex justify-between"><span>Tax (per installment)</span> <span>RM <span
                                                x-text="installmentTax"></span></span></div>
                                    <div class="flex justify-between"><span>FPX Charge (per installment)</span>
                                        <span>RM <span x-text="installmentFpx"></span></span>
                                    </div>
                                </div>

                                <div class="border-t pt-2 flex justify-between font-semibold text-gray-800">
                                    <span>Total Subscription Cost</span> <span>RM <span
                                            x-text="fullFinal.toFixed(2)"></span></span>
                                </div>

                                <div class="pt-2 flex justify-between font-extrabold text-indigo-600 text-lg">
                                    <span
                                        x-text="installments == 1 ? 'Total to Pay Now' : 'Amount for First Installment'"></span>
                                    <span>RM <span x-text="installmentAmount"></span></span>
                                </div>
                            </div>

                            <div class="flex justify-end gap-3 pt-4">
                                <button @click="open = false"
                                    class="px-5 py-2 rounded-lg text-gray-600 border border-gray-300 hover:bg-gray-100 font-semibold transition">Cancel</button>
                                <form method="POST" :action="'/subscribe/' + plan">
                                    @csrf
                                    <input type="hidden" name="installments" x-model="installments">
                                    <button type="submit"
                                        class="px-5 py-2 rounded-lg bg-indigo-600 text-white font-bold shadow-md hover:bg-indigo-700 transition">
                                        Pay RM <span x-text="installmentAmount"></span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div x-data="{
                        open: false,
                        subscriptionId: null,
                        selectedOption: null,
                    
                        // Data passed in
                        fullFinal: 0,
                        paidCount: 0,
                        totalInstallments: 0,
                    
                        // Calculated details (Full subscription cost)
                        base: 100,
                        get tax() { return this.base * 0.08; },
                        get fpx() { return 1.00; },
                    
                        // Calculated amounts
                        get installmentAmount() {
                            if (this.totalInstallments === 0) return 0;
                            // For next installment, use full base for one installment
                            return this.base + this.tax + this.fpx;
                        },
                    
                    
                        get remainingAmount() {
                            return this.installmentAmount * (this.totalInstallments - this.paidCount);
                        },
                    
                        // Breakdown properties for the currently selected option
                        get currentAmount() {
                            return this.selectedOption === 'full' ? this.remainingAmount : this.installmentAmount;
                        },
                        get currentBase() {
                            return this.base;
                        },
                    }"
                        x-on:open-pay-next-modal.window="
                    open = true; 
                    subscriptionId = $event.detail.subscriptionId ?? null; 
                    base = parseFloat($event.detail.base) || 0;           // ✅ important
                    fullFinal = parseFloat($event.detail.fullFinal) || 0;
                    paidCount = parseInt($event.detail.paidCount) || 0;
                    totalInstallments = parseInt($event.detail.totalInstallments) || 0;
                    selectedOption = 'next';
                "
                        x-show="open" x-cloak style="display:none"
                        class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75 backdrop-blur-sm p-4">

                        <div @click.away="open = false"
                            class="bg-white rounded-2xl shadow-2xl w-full max-w-lg p-8 space-y-6 transform transition-all duration-300">
                            <h2 class="text-2xl font-bold text-gray-800 border-b pb-3">Pay Subscription</h2>

                            <p class="text-md font-semibold text-gray-700">Payment Progress: <span
                                    class="font-bold text-indigo-600"
                                    x-text="paidCount + ' / ' + totalInstallments"></span></p>

                            {{-- Payment Option Selection --}}
                            <div class="grid grid-cols-2 gap-4">
                                {{-- Option 1: Next Installment --}}
                                <button @click="selectedOption = 'next'"
                                    :class="{ 'border-indigo-600 ring-2 ring-indigo-300': selectedOption === 'next', 'border-gray-300 hover:border-indigo-400': selectedOption !== 'next' }"
                                    class="p-4 border-2 rounded-xl text-left transition duration-200">
                                    <p class="font-bold text-lg"
                                        :class="{ 'text-indigo-600': selectedOption === 'next' }">Next Installment
                                    </p>
                                    <p class="text-xl font-extrabold mt-1">RM <span
                                            x-text="Number(installmentAmount).toFixed(2)"></span></p>
                                    <p class="text-xs text-gray-500">Pay one installment.</p>
                                </button>

                                {{-- Option 2: Full Remaining (Only show if remaining amount is positive) --}}
                                {{-- <template x-if="remainingAmount > installmentAmount">
                            <button 
                                @click="selectedOption = 'full'" 
                                :class="{'border-indigo-600 ring-2 ring-indigo-300': selectedOption === 'full', 'border-gray-300 hover:border-indigo-400': selectedOption !== 'full'}"
                                class="p-4 border-2 rounded-xl text-left transition duration-200">
                                <p class="font-bold text-lg" :class="{'text-indigo-600': selectedOption === 'full'}">Settle Remaining</p>
                                <p class="text-xl font-extrabold mt-1">RM <span x-text="Number(remainingAmount).toFixed(2)"></span></p>
                                <p class="text-xs text-gray-500">Settle all remaining payments.</p>
                            </button>
                        </template> --}}
                            </div>

                            {{-- Payment Breakdown for selected option --}}
                            <div class="bg-gray-50 p-4 rounded-xl space-y-2 text-sm" x-show="selectedOption">
                                <h3 class="font-bold text-base text-gray-800 mb-2"
                                    x-text="'Breakdown for: ' + (selectedOption === 'full' ? 'Full Settlement' : 'Next Installment')">
                                </h3>

                                <div class="flex justify-between"><span>Base Price</span> <span>RM <span
                                            x-text="currentBase.toFixed(2)"></span></span></div>
                                <div class="flex justify-between"><span>Tax (8%)</span> <span>RM <span
                                            x-text="tax.toFixed(2)"></span></span></div>
                                <div class="flex justify-between"><span>FPX Charge</span> <span>RM <span
                                            x-text="fpx.toFixed(2)"></span></span></div>

                                <div class="border-t pt-2 flex justify-between font-extrabold text-indigo-600 text-lg">
                                    <span
                                        x-text="selectedOption === 'full' ? 'Total Settlement Amount' : 'Total Installment Amount'"></span>
                                    <span>RM <span x-text="currentAmount.toFixed(2)"></span></span>
                                </div>
                            </div>

                            <div class="flex justify-end gap-3 pt-4">
                                <button @click="open = false"
                                    class="px-5 py-2 rounded-lg text-gray-600 border border-gray-300 hover:bg-gray-100 font-semibold transition">Cancel</button>

                                <form method="POST" :action="'/subscribe/pay-next/' + subscriptionId">
                                    @csrf
                                    <input type="hidden" name="full_settlement"
                                        :value="selectedOption === 'full' ? 1 : 0">
                                    <button type="submit" :disabled="!selectedOption"
                                        class="px-5 py-2 rounded-lg bg-indigo-600 text-white font-bold shadow-md hover:bg-indigo-700 transition disabled:opacity-50">
                                        Pay RM <span x-text="currentAmount.toFixed(2)"></span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div x-data="{ open: false, plan: '', label: '', base: 0, tax: 0, fpx: 0, final: 0 }"
                        x-on:open-modal.window="open = true; plan = $event.detail.plan; label = $event.detail.label; base = $event.detail.base; tax = $event.detail.tax; fpx = $event.detail.fpx; final = $event.detail.final"
                        x-show="open" style="display: none"
                        class="fixed inset-0 z-50 flex items-center justify-center 
                 bg-white backdrop-blur-sm transition-opacity">

                        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6 space-y-6">
                            <h2 class="text-lg font-bold text-gray-800" x-text="label + ' Subscription'"></h2>

                            <div class="space-y-2 text-sm text-gray-600">
                                <div class="flex justify-between"><span>Base Price</span> <span>RM <span
                                            x-text="base"></span></span></div>
                                <div class="flex justify-between"><span>Tax (8%)</span> <span>RM <span
                                            x-text="tax"></span></span></div>
                                <div class="flex justify-between"><span>FPX Charge</span> <span>RM <span
                                            x-text="fpx"></span></span></div>
                                <div class="border-t pt-2 flex justify-between font-semibold text-gray-800">
                                    <span>Total</span> <span>RM <span x-text="final"></span></span>
                                </div>
                            </div>

                            <div class="flex justify-end gap-3">
                                <button @click="open = false"
                                    class="px-4 py-2 rounded-lg text-gray-600 hover:bg-gray-100">Cancel</button>

                                <form method="POST" :action="'/subscribe/' + plan">
                                    @csrf
                                    <button type="submit"
                                        class="px-4 py-2 rounded-lg bg-indigo-600 text-white font-semibold hover:bg-indigo-700">
                                        Confirm & Pay
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>



                </ul>
            </div>



            @if (auth()->user()->hasRole('Admin'))
                <div
                    class="lg:col-span-3 bg-white rounded-2xl shadow-lg p-6 hover:shadow-2xl transition-shadow duration-300">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4">All Users</h3>

                    {{-- Search form --}}
                    <form method="GET" action="{{ route('dashboard') }}" class="mb-6 flex">
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Search by name, email, or serial number..."
                            class="w-full px-4 py-2 border rounded-l-lg focus:ring-indigo-500 focus:border-indigo-500">
                        <button type="submit"
                            class="px-4 py-2 bg-indigo-600 text-white rounded-r-lg hover:bg-indigo-700">
                            Search
                        </button>
                    </form>

                    {{-- Add scrollable wrapper --}}
                    <div class="overflow-x-auto -mx-6 px-6">
                        <table class="w-full border border-gray-200 rounded-xl overflow-hidden min-w-max">
                            <thead class="bg-gray-50 text-left text-sm font-semibold text-gray-600">
                                <tr>
                                    <th class="px-4 py-3 whitespace-nowrap">RM No</th>
                                    <th class="px-4 py-3 whitespace-nowrap">SB No</th>
                                    <th class="px-4 py-3 whitespace-nowrap">Name</th>
                                    <th class="px-4 py-3 whitespace-nowrap">Email</th>
                                    <th class="px-4 py-3 whitespace-nowrap">Phone</th>
                                    <th class="px-4 py-3 whitespace-nowrap">Referrer</th>
                                    <th class="px-4 py-3 whitespace-nowrap">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach ($users as $u)
                                    @php
                                        $rizqmall = $u->accounts->firstWhere('type', 'rizqmall');
                                        $sandbox = $u->accounts->firstWhere('type', 'sandbox');
                                    @endphp
                                    <tr>
                                        <td
                                            class="px-4 py-3 text-sm whitespace-nowrap {{ $rizqmall && $rizqmall->serial_number ? 'text-green-600 font-semibold' : 'text-red-600 font-semibold' }}">
                                            {{ $rizqmall->serial_number ?? 'inactive' }}
                                        </td>
                                        <td
                                            class="px-4 py-3 text-sm whitespace-nowrap {{ $sandbox && $sandbox->serial_number ? 'text-green-600 font-semibold' : 'text-red-600 font-semibold' }}">
                                            {{ $sandbox->serial_number ?? 'inactive' }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <div class="flex items-center gap-2">
                                                <span
                                                    class="user-name-display">{{ $u->profile?->full_name ?? $u->name }}</span>
                                                <button class="edit-name-btn text-gray-500 hover:text-indigo-600"
                                                    data-user-id="{{ $u->id }}">✏️</button>
                                            </div>

                                            <form action="{{ route('admin.users.updateName', $u->id) }}"
                                                method="POST"
                                                class="edit-name-form hidden flex items-center gap-1 mt-1">
                                                @csrf
                                                @method('PUT')
                                                <input type="text" name="name"
                                                    value="{{ $u->profile?->full_name ?? $u->name }}"
                                                    class="border rounded px-2 py-1 text-sm w-40">
                                                <button type="submit"
                                                    class="px-2 py-1 bg-green-600 text-white rounded text-sm">✅</button>
                                                <button type="button"
                                                    class="cancel-edit-name-btn px-2 py-1 bg-gray-300 rounded text-sm">❌</button>
                                            </form>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">{{ $u->email }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <div class="flex items-center gap-2">
                                                <span
                                                    class="user-phone-display">{{ $u->profile?->phone ?? '-' }}</span>
                                                <button class="edit-phone-btn text-gray-500 hover:text-indigo-600"
                                                    data-phone-id="{{ $u->id }}">✏️</button>
                                            </div>

                                            <form action="{{ route('admin.users.updatePhone', $u->id) }}"
                                                method="POST"
                                                class="edit-phone-form hidden flex items-center gap-1 mt-1">
                                                @csrf
                                                @method('PUT')
                                                <input type="text" name="phone"
                                                    value="{{ $u->profile?->phone ?? '-' }}"
                                                    class="border rounded px-2 py-1 text-sm w-40">
                                                <button type="submit"
                                                    class="px-2 py-1 bg-green-600 text-white rounded text-sm">✅</button>
                                                <button type="button"
                                                    class="cancel-edit-phone-btn px-2 py-1 bg-gray-300 rounded text-sm">❌</button>
                                            </form>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            {{ $u->referral?->parent?->name ?? '-' }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <button data-user="{{ $u->id }}"
                                                class="view-details px-3 py-1 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold">
                                                View Details
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    <div class="mt-6">
                        {{ $users->links() }}
                    </div>
                </div>

                <!-- Details Modal -->
                <div id="detailsModal"
                    class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                    <div class="bg-white rounded-2xl shadow-lg w-full max-w-2xl p-6 relative">
                        <!-- X button -->
                        <button onclick="document.getElementById('detailsModal').classList.add('hidden')"
                            class="absolute top-3 right-3 text-black hover:text-gray-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>

                        <h3 class="text-lg font-semibold mb-4">User Details</h3>
                        <div id="modalContent" class="text-sm text-gray-700">
                            Loading...
                        </div>
                        <div class="mt-4 text-right">
                            <button onclick="document.getElementById('detailsModal').classList.add('hidden')"
                                class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg">
                                Close
                            </button>
                        </div>
                    </div>
                </div>


                <script>
                    document.querySelectorAll('.view-details').forEach(btn => {
                        btn.addEventListener('click', async () => {
                            const id = btn.getAttribute('data-user');
                            const res = await fetch(`/admin/user/${id}/details`);
                            const html = await res.text();
                            document.getElementById('modalContent').innerHTML = html;
                            document.getElementById('detailsModal').classList.remove('hidden');
                        });
                    });
                </script>
            @else
                <div
                    class="lg:col-span-3 bg-white rounded-2xl shadow-lg p-6 hover:shadow-2xl transition-shadow duration-300">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4">Your Referrals</h3>

                    {{-- Search form --}}
                    <form method="GET" action="{{ route('dashboard') }}" class="mb-6 flex">
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Search by name, email, or serial number..."
                            class="w-full px-4 py-2 border rounded-l-lg focus:ring-indigo-500 focus:border-indigo-500">
                        <button type="submit"
                            class="px-4 py-2 bg-indigo-600 text-white rounded-r-lg hover:bg-indigo-700">
                            Search
                        </button>
                    </form>

                    {{-- Add scrollable wrapper --}}
                    <div class="overflow-x-auto -mx-6 px-6">
                        <table class="w-full border border-gray-200 rounded-xl overflow-hidden min-w-max">
                            <thead class="bg-gray-50 text-left text-sm font-semibold text-gray-600">
                                <tr>
                                    <th class="px-4 py-3 whitespace-nowrap">RM No</th>
                                    <th class="px-4 py-3 whitespace-nowrap">SB No</th>
                                    <th class="px-4 py-3 whitespace-nowrap">Name</th>
                                    <th class="px-4 py-3 whitespace-nowrap">Email</th>
                                    <th class="px-4 py-3 whitespace-nowrap">Phone</th>
                                    <th class="px-4 py-3 whitespace-nowrap">Referrer</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach ($users as $u)
                                    @php
                                        $rizqmall = $u->accounts->firstWhere('type', 'rizqmall');
                                        $sandbox = $u->accounts->firstWhere('type', 'sandbox');
                                    @endphp
                                    <tr>
                                        <td
                                            class="px-4 py-3 text-sm whitespace-nowrap {{ $rizqmall && $rizqmall->serial_number ? 'text-green-600 font-semibold' : 'text-red-600 font-semibold' }}">
                                            {{ $rizqmall->serial_number ?? 'inactive' }}
                                        </td>
                                        <td
                                            class="px-4 py-3 text-sm whitespace-nowrap {{ $sandbox && $sandbox->serial_number ? 'text-green-600 font-semibold' : 'text-red-600 font-semibold' }}">
                                            {{ $sandbox->serial_number ?? 'inactive' }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <div class="flex items-center gap-2">
                                                <span
                                                    class="user-name-display">{{ $u->profile?->full_name ?? $u->name }}</span>
                                                <button class="edit-name-btn text-gray-500 hover:text-indigo-600"
                                                    data-user-id="{{ $u->id }}">✏️</button>
                                            </div>

                                            <form action="{{ route('admin.users.updateName', $u->id) }}"
                                                method="POST"
                                                class="edit-name-form hidden flex items-center gap-1 mt-1">
                                                @csrf
                                                @method('PUT')
                                                <input type="text" name="name"
                                                    value="{{ $u->profile?->full_name ?? $u->name }}"
                                                    class="border rounded px-2 py-1 text-sm w-40">
                                                <button type="submit"
                                                    class="px-2 py-1 bg-green-600 text-white rounded text-sm">✅</button>
                                                <button type="button"
                                                    class="cancel-edit-name-btn px-2 py-1 bg-gray-300 rounded text-sm">❌</button>
                                            </form>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">{{ $u->email }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap">{{ $u->profile->phone ?? '-' }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            {{ $u->referral?->parent_id === auth()->id() ? 'YOU' : $u->referral?->parent?->name }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    <div class="mt-6">
                        {{ $users->links() }}
                    </div>
                </div>
            @endif




        </div>
    </div>
    <script>
        function copyToClipboard(button) {
            const input = button.previousElementSibling; // get the <input>
            input.select();
            input.setSelectionRange(0, 99999); // for mobile
            navigator.clipboard.writeText(input.value)
                .then(() => {
                    // Optional: small feedback
                    button.classList.add("text-green-600");
                    setTimeout(() => button.classList.remove("text-green-600"), 1500);
                })
                .catch(err => console.error("Failed to copy:", err));
        }
    </script>
    <script>
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

            // Optional AJAX submit to avoid page reload
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
                        td.querySelector('.user-name-display').parentElement.classList.remove(
                            'hidden');
                    } else {
                        alert('Failed to update name');
                    }
                });
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
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
                    td.querySelector('.user-phone-display').parentElement.classList.remove(
                        'hidden');
                });
            });

            // Optional AJAX submit to avoid page reload
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
                        td.querySelector('.user-phone-display').parentElement.classList.remove(
                            'hidden');
                    } else {
                        alert('Failed to update phone');
                    }
                });
            });
        });
    </script>
</x-app-layout>
