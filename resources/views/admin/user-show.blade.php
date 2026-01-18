<x-admin-layout>
    <x-slot name="pageTitle">User Details</x-slot>
    <x-slot name="breadcrumb">View and manage user profile and accounts</x-slot>

    {{-- Header / Summary Card --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-100 dark:border-gray-700 mb-6">
        <div class="flex flex-col md:flex-row items-center md:items-start gap-6">
            {{-- User Avatar --}}
            <div class="flex-shrink-0">
                @if($user->photo)
                <img src="{{ asset('storage/' . $user->photo) }}" class="w-24 h-24 rounded-2xl object-cover shadow-lg ring-4 ring-gray-50 dark:ring-gray-700">
                @else
                <div class="w-24 h-24 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-500 flex items-center justify-center shadow-lg ring-4 ring-gray-50 dark:ring-gray-700">
                    <span class="text-3xl font-bold text-white">{{ substr($user->name, 0, 1) }}</span>
                </div>
                @endif
            </div>

            {{-- User Info --}}
            <div class="flex-1 text-center md:text-left w-full">
                <div class="flex flex-col md:flex-row justify-between items-center mb-2">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        {{ $user->profile?->full_name ?? $user->name }}
                        @if($user->checkBlacklist())
                        <span class="px-2 py-1 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 text-xs font-bold uppercase rounded-lg">Blacklisted</span>
                        @endif
                    </h2>

                    <div class="flex gap-2 mt-4 md:mt-0">
                        <a href="{{ route('admin.users.index') }}" class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl text-sm font-medium hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                            <i class="fas fa-arrow-left mr-2"></i> Back
                        </a>

                        <form action="{{ route('admin.users.toggleAdmin', $user->id) }}" method="POST" class="inline">
                            @csrf
                            <input type="hidden" name="role" value="{{ $user->hasRole('Admin') ? 'User' : 'Admin' }}">
                            <button type="submit" class="px-4 py-2 {{ $user->hasRole('Admin') ? 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 hover:bg-red-200' : 'bg-indigo-600 text-white hover:bg-indigo-700' }} rounded-xl text-sm font-medium transition-colors shadow-sm">
                                <i class="fas {{ $user->hasRole('Admin') ? 'fa-user-minus' : 'fa-user-shield' }} mr-2"></i>
                                {{ $user->hasRole('Admin') ? 'Remove Admin' : 'Make Admin' }}
                            </button>
                        </form>

                        <a href="{{ route('admin.users.edit', $user->id) }}" class="px-4 py-2 bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400 rounded-xl text-sm font-medium hover:bg-yellow-200 dark:hover:bg-yellow-600 transition-colors shadow-sm">
                            <i class="fas fa-edit mr-2"></i> Edit
                        </a>

                        @unless($user->checkBlacklist())
                        <button onclick="document.getElementById('blacklistModal').classList.remove('hidden')" class="px-4 py-2 bg-gray-800 dark:bg-gray-900 text-white rounded-xl text-sm font-medium hover:bg-black transition-colors shadow-sm">
                            <i class="fas fa-ban mr-2"></i> Blacklist
                        </button>
                        @endunless
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 text-sm mt-4">
                    <div class="flex items-center text-gray-600 dark:text-gray-400">
                        <i class="fas fa-envelope w-5 text-center mr-2"></i>
                        {{ $user->email }}
                    </div>
                    <div class="flex items-center text-gray-600 dark:text-gray-400">
                        <i class="fas fa-phone w-5 text-center mr-2"></i>
                        {{ $user->profile?->phone ?? 'Not set' }}
                    </div>
                    <div class="flex items-center text-gray-600 dark:text-gray-400">
                        <i class="fas fa-id-card w-5 text-center mr-2"></i>
                        {{ $user->profile?->nric ?? 'Not set' }}
                    </div>
                    <div class="flex items-center text-gray-600 dark:text-gray-400">
                        <i class="fas fa-calendar w-5 text-center mr-2"></i>
                        Joined {{ $user->created_at->format('d M Y') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabs --}}
    <div x-data="{ activeTab: 'overview' }" class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 overflow-hidden">
        {{-- Tab Headers --}}
        <div class="border-b border-gray-200 dark:border-gray-700 overflow-x-auto scroller-hide">
            <nav class="flex">
                <button @click="activeTab = 'overview'"
                    :class="{ 'border-indigo-500 text-indigo-600 dark:text-indigo-400': activeTab === 'overview', 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400': activeTab !== 'overview' }"
                    class="px-6 py-4 text-sm font-medium border-b-2 whitespace-nowrap transition-colors">
                    Overview
                </button>
                <button @click="activeTab = 'accounts'"
                    :class="{ 'border-indigo-500 text-indigo-600 dark:text-indigo-400': activeTab === 'accounts', 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400': activeTab !== 'accounts' }"
                    class="px-6 py-4 text-sm font-medium border-b-2 whitespace-nowrap transition-colors">
                    Accounts
                </button>
                <button @click="activeTab = 'wallet'"
                    :class="{ 'border-indigo-500 text-indigo-600 dark:text-indigo-400': activeTab === 'wallet', 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400': activeTab !== 'wallet' }"
                    class="px-6 py-4 text-sm font-medium border-b-2 whitespace-nowrap transition-colors">
                    Wallet & Payments
                </button>
                <button @click="activeTab = 'collection'"
                    :class="{ 'border-indigo-500 text-indigo-600 dark:text-indigo-400': activeTab === 'collection', 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400': activeTab !== 'collection' }"
                    class="px-6 py-4 text-sm font-medium border-b-2 whitespace-nowrap transition-colors">
                    Collection/Tabung
                </button>
                <button @click="activeTab = 'bank'"
                    :class="{ 'border-indigo-500 text-indigo-600 dark:text-indigo-400': activeTab === 'bank', 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400': activeTab !== 'bank' }"
                    class="px-6 py-4 text-sm font-medium border-b-2 whitespace-nowrap transition-colors">
                    Bank
                </button>
                <button @click="activeTab = 'audit'"
                    :class="{ 'border-indigo-500 text-indigo-600 dark:text-indigo-400': activeTab === 'audit', 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400': activeTab !== 'audit' }"
                    class="px-6 py-4 text-sm font-medium border-b-2 whitespace-nowrap transition-colors">
                    Audit
                </button>
                <button @click="activeTab = 'referrals'"
                    :class="{ 'border-indigo-500 text-indigo-600 dark:text-indigo-400': activeTab === 'referrals', 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400': activeTab !== 'referrals' }"
                    class="px-6 py-4 text-sm font-medium border-b-2 whitespace-nowrap transition-colors">
                    Referrals
                </button>
            </nav>
        </div>

        {{-- Tab Contents --}}
        <div class="p-6">
            {{-- OVERVIEW TAB --}}
            <div x-show="activeTab === 'overview'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                <div class="grid md:grid-cols-2 gap-6">
                    {{-- Profile Details --}}
                    <div class="space-y-6">
                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-5">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Personal Information</h3>
                            <dl class="space-y-3 text-sm">
                                <div class="flex justify-between">
                                    <dt class="text-gray-500 dark:text-gray-400">Full Name</dt>
                                    <dd class="font-medium text-gray-900 dark:text-white">{{ $user->profile?->full_name ?? '-' }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-gray-500 dark:text-gray-400">NRIC</dt>
                                    <dd class="font-medium text-gray-900 dark:text-white">{{ $user->profile?->nric ?? '-' }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-gray-500 dark:text-gray-400">Date of Birth</dt>
                                    <dd class="font-medium text-gray-900 dark:text-white">{{ $user->profile?->dob ?? '-' }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-gray-500 dark:text-gray-400">Phone</dt>
                                    <dd class="font-medium text-gray-900 dark:text-white">{{ $user->profile?->phone ?? '-' }}</dd>
                                </div>
                            </dl>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-5">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Address</h3>
                            <dl class="space-y-3 text-sm">
                                <div>
                                    <dt class="text-gray-500 dark:text-gray-400 mb-1">Home Address</dt>
                                    <dd class="font-medium text-gray-900 dark:text-white">{!! nl2br(e($user->profile?->home_address ?? '-')) !!}</dd>
                                </div>
                                <div class="grid grid-cols-2 gap-4 pt-2">
                                    <div>
                                        <dt class="text-gray-500 dark:text-gray-400">City</dt>
                                        <dd class="font-medium text-gray-900 dark:text-white">{{ $user->profile?->city ?? '-' }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-gray-500 dark:text-gray-400">State</dt>
                                        <dd class="font-medium text-gray-900 dark:text-white">{{ $user->profile?->state ?? '-' }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-gray-500 dark:text-gray-400">Country</dt>
                                        <dd class="font-medium text-gray-900 dark:text-white">{{ $user->profile?->country ?? '-' }}</dd>
                                    </div>
                                </div>
                            </dl>
                        </div>
                    </div>

                    {{-- Next of Kin --}}
                    <div class="space-y-6">
                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-5">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Next of Kin</h3>
                            @forelse(($user->nextOfKins ?? collect([])) as $nok)
                            <div class="mb-3 pb-3 border-b border-gray-200 dark:border-gray-600 last:border-0 last:mb-0 last:pb-0">
                                <p class="font-semibold text-gray-900 dark:text-white">{{ $nok->name }} <span class="text-xs font-normal text-gray-500">({{ $nok->relationship }})</span></p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $nok->phone }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">{{ $nok->address }}</p>
                            </div>
                            @empty
                            <p class="text-sm text-gray-500 italic">No next of kin recorded.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            {{-- ACCOUNTS TAB --}}
            <div x-show="activeTab === 'accounts'" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                @php
                $accountTypes = $user->accounts->pluck('type')->map(fn($t) => strtolower(trim($t ?? '')))->toArray();
                $hasRizqmall = in_array('rizqmall', $accountTypes);
                $hasSandbox = in_array('sandbox', $accountTypes);
                @endphp

                <div class="mb-6 p-5 bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-100 dark:border-indigo-800 rounded-xl flex flex-col md:flex-row items-center justify-between gap-4">
                    <div>
                        <h4 class="font-bold text-indigo-900 dark:text-indigo-300">Account Status</h4>
                        <p class="text-sm text-indigo-700 dark:text-indigo-400 mt-1">
                            @if($hasRizqmall && $hasSandbox)
                            All required accounts (RizqMall & Sandbox) are active.
                            @else
                            Missing:
                            @if(!$hasRizqmall) <span class="font-bold">RizqMall</span> @endif
                            @if(!$hasRizqmall && !$hasSandbox) & @endif
                            @if(!$hasSandbox) <span class="font-bold">Sandbox</span> @endif
                            @endif
                        </p>
                    </div>
                    <div class="flex gap-3">
                        @if(!$hasRizqmall)
                        <button onclick="window.createAccount('rizqmall')" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-semibold transition-colors shadow-sm">
                            <i class="fas fa-plus mr-2"></i> Add RizqMall
                        </button>
                        @endif
                        @if(!$hasSandbox)
                        <button onclick="window.createAccount('sandbox')" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg text-sm font-semibold transition-colors shadow-sm">
                            <i class="fas fa-plus mr-2"></i> Add Sandbox
                        </button>
                        @endif
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-6">
                    @forelse(($user->accounts ?? collect([])) as $account)
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-5 border {{ $account->active ? 'border-green-200 dark:border-green-800' : 'border-red-200 dark:border-red-800' }}">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h4 class="font-bold text-lg text-gray-900 dark:text-white capitalize flex items-center gap-2">
                                    {{ $account->type }} Account
                                    <span class="px-2 py-0.5 rounded text-xs {{ $account->active ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' }}">
                                        {{ $account->active ? 'Active' : 'Inactive' }}
                                    </span>
                                </h4>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 font-mono">{{ $account->serial_number ?? 'No Serial' }}</p>
                            </div>

                        </div>

                        <div class="space-y-3 pt-3 border-t border-gray-200 dark:border-gray-600">
                            {{-- Edit Serial Form --}}
                            <div x-data="{ editing: false, serial: '{{ $account->serial_number ?? '' }}' }" class="mb-3">
                                <button x-show="!editing" @click="editing = true" class="text-xs font-semibold text-indigo-600 hover:text-indigo-500 dark:text-indigo-400">Change Serial Number</button>
                                <div x-show="editing" class="flex items-center gap-2 mt-2">
                                    <input type="text" x-model="serial" class="w-full text-sm border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white" placeholder="Enter Serial">
                                    <button @click="updateSerial({{ $account->id }}, serial); editing = false;" class="p-1.5 bg-green-500 text-white rounded-lg hover:bg-green-600"><i class="fas fa-check"></i></button>
                                    <button @click="editing = false" class="p-1.5 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400"><i class="fas fa-times"></i></button>
                                </div>
                            </div>

                            <form action="{{ route('admin.users.toggleAccountActive', [$user->id, $account->id]) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full py-2 rounded-lg text-sm font-semibold transition-colors {{ $account->active ? 'bg-red-50 text-red-600 hover:bg-red-100 dark:bg-red-900/20 dark:text-red-400 dark:hover:bg-red-900/40' : 'bg-green-50 text-green-600 hover:bg-green-100 dark:bg-green-900/20 dark:text-green-400 dark:hover:bg-green-900/40' }}">
                                    {{ $account->active ? 'Deactivate Account' : 'Activate Account' }}
                                </button>
                            </form>
                        </div>
                    </div>
                    @empty
                    <div class="col-span-2 text-center py-8 text-gray-500">No accounts found.</div>
                    @endforelse
                </div>
            </div>

            {{-- WALLET TAB --}}
            <div x-show="activeTab === 'wallet'" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                <div class="grid md:grid-cols-2 gap-6">
                    <div class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl p-6 text-white shadow-lg">
                        <p class="text-indigo-100 text-sm font-medium mb-1">Current Balance</p>
                        <h3 class="text-4xl font-bold">RM {{ number_format(($user->wallet->balance ?? 0) / 100, 2) }}</h3>
                        <div class="mt-6 flex gap-3">
                            <button class="flex-1 py-2 bg-white/20 hover:bg-white/30 rounded-xl text-sm font-semibold backdrop-blur-sm transition-colors">
                                <i class="fas fa-plus mr-1"></i> Topup
                            </button>
                            <button class="flex-1 py-2 bg-white/20 hover:bg-white/30 rounded-xl text-sm font-semibold backdrop-blur-sm transition-colors">
                                <i class="fas fa-minus mr-1"></i> Deduct
                            </button>
                        </div>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-2xl p-6 h-64 overflow-y-auto custom-scrollbar">
                        <h4 class="font-bold text-gray-900 dark:text-white mb-4">Recent Transactions</h4>
                        <ul class="space-y-4">
                            @forelse(($user->wallet?->transactions()->latest()->take(10)->get() ?? collect([])) as $tx)
                            <li class="flex justify-between items-center">
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $tx->description }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $tx->created_at->format('d M Y, h:i A') }}</p>
                                </div>
                                <span class="font-bold text-sm {{ $tx->type === 'credit' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                    {{ $tx->type === 'credit' ? '+' : '-' }} RM {{ number_format($tx->amount / 100, 2) }}
                                </span>
                            </li>
                            @empty
                            <li class="text-center text-gray-500 py-4 text-sm">No transactions yet.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>

            {{-- COLLECTION TAB --}}
            <div x-show="activeTab === 'collection'" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                <div class="overflow-x-auto bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 mb-6">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400 border-b border-gray-200 dark:border-gray-700">
                            <tr>
                                <th class="px-6 py-3">Type</th>
                                <th class="px-6 py-3">Balance</th>
                                <th class="px-6 py-3">Pending</th>
                                <th class="px-6 py-3">Redeemed</th>
                                <th class="px-6 py-3">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach($user->collections as $collection)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                                    {{ ucfirst(str_replace('_', ' ', $collection->type)) }}
                                </td>
                                <td class="px-6 py-4 font-bold text-gray-900 dark:text-white">
                                    RM {{ number_format($collection->balance / 100, 2) }}
                                </td>
                                <td class="px-6 py-4 text-gray-500 dark:text-gray-400">
                                    RM {{ number_format($collection->pending_balance / 100, 2) }}
                                </td>
                                <td class="px-6 py-4">
                                    @if($collection->is_redeemed)
                                    <span class="px-2 py-1 bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 rounded text-xs font-semibold">Yes</span>
                                    @else
                                    <span class="px-2 py-1 bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-400 rounded text-xs font-semibold">No</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if(!$collection->is_redeemed)
                                    <form action="{{ route('admin.users.collection.redeem', [$user->id, $collection->type]) }}" method="POST">
                                        @csrf
                                        <button class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 font-semibold text-xs transition-colors">
                                            Redeem
                                        </button>
                                    </form>
                                    @else
                                    <span class="text-gray-400 text-xs text-center block">-</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Add Transaction Button --}}
                <div class="text-right mb-4">
                    <button onclick="document.getElementById('transactionModal').classList.remove('hidden')"
                        class="px-4 py-2 bg-gradient-to-r from-purple-500 to-indigo-600 text-white rounded-xl text-sm font-medium hover:opacity-90 transition shadow-lg">
                        <i class="fas fa-plus mr-2"></i> Add Transaction
                    </button>
                </div>

                {{-- Collection Transactions Table --}}
                <div class="overflow-x-auto bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400 border-b border-gray-200 dark:border-gray-700">
                            <tr>
                                <th class="px-6 py-3">Date</th>
                                <th class="px-6 py-3">Collection</th>
                                <th class="px-6 py-3">Amount</th>
                                <th class="px-6 py-3">Description</th>
                                <th class="px-6 py-3">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @php
                            $colTransactions = $user->collections->flatMap(function($collection) {
                            return $collection->transactions ?? collect();
                            })->sortByDesc('created_at');
                            @endphp
                            @forelse($colTransactions as $tx)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="px-6 py-4 text-gray-600 dark:text-gray-300 whitespace-nowrap">
                                    {{ $tx->created_at->format('d M Y') }}<br>
                                    <span class="text-xs text-gray-400">{{ $tx->created_at->format('h:i A') }}</span>
                                </td>
                                <td class="px-6 py-4 text-gray-900 dark:text-white font-medium capitalize">
                                    {{ str_replace('_', ' ', $tx->collection->type) }}
                                </td>
                                <td class="px-6 py-4 font-bold {{ $tx->type == 'credit' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                    {{ $tx->type == 'credit' ? '+' : '-' }} RM {{ number_format($tx->amount / 100, 2) }}
                                </td>
                                <td class="px-6 py-4 text-gray-600 dark:text-gray-300">
                                    {{ $tx->description }}
                                    @if($tx->slip_path)
                                    <a href="{{ Storage::url($tx->slip_path) }}" target="_blank" class="block text-indigo-500 hover:underline text-xs mt-1">View Slip</a>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <form action="{{ route('admin.collection-transactions.destroy', $tx->id) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                                        @csrf
                                        <button class="text-red-500 hover:text-red-700 dark:hover:text-red-400 transition-colors">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                    No transactions found.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- BANK TAB --}}
            <div x-show="activeTab === 'bank'" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-6">User Bank Details</h3>
                    <div class="grid md:grid-cols-2 gap-8">
                        <div class="relative overflow-hidden p-6 rounded-2xl bg-gradient-to-tr from-gray-900 to-gray-800 text-white shadow-xl">
                            <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white/10 rounded-full blur-xl"></div>
                            <div class="flex justify-between items-start mb-8">
                                <div>
                                    <p class="text-xs text-gray-400 uppercase tracking-wider">Bank Name</p>
                                    <p class="text-xl font-bold tracking-wide">{{ $user->bank?->bank_name ?? 'NOT SET' }}</p>
                                </div>
                                <i class="fas fa-university text-2xl text-gray-500"></i>
                            </div>
                            <div class="mb-6">
                                <p class="text-xs text-gray-400 uppercase tracking-wider">Account Number</p>
                                <p class="text-2xl font-mono tracking-widest">{{ $user->bank?->account_number ?? '0000 0000 0000' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 uppercase tracking-wider">Account Holder</p>
                                <p class="text-lg font-semibold truncate">{{ $user->bank?->account_holder ?? $user->name }}</p>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl border border-dashed border-gray-200 dark:border-gray-600">
                                <h4 class="font-bold text-sm text-gray-700 dark:text-gray-300 mb-2">Verification Status</h4>
                                <div class="flex items-center gap-2">
                                    @if($user->bank)
                                    <i class="fas fa-check-circle text-green-500"></i>
                                    <span class="text-sm text-green-700 dark:text-green-400 font-medium">Bank details added</span>
                                    @else
                                    <i class="fas fa-exclamation-circle text-yellow-500"></i>
                                    <span class="text-sm text-yellow-700 dark:text-yellow-400 font-medium">No bank details</span>
                                    @endif
                                </div>
                            </div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                To update these details, please use the <strong>Edit</strong> button at the top of the page.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- AUDIT TAB --}}
            <div x-show="activeTab === 'audit'" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-6 flex flex-col items-center justify-center min-h-[300px]">
                    <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-clipboard-list text-2xl text-gray-400"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Audit Logs</h3>
                    <p class="text-gray-500 dark:text-gray-400 text-sm mt-2 text-center max-w-md">
                        Detailed activity logs for this user (login history, profile updates, account changes) will typically appear here.
                    </p>
                    <div class="mt-8 w-full max-w-2xl">
                        <div class="border-l-2 border-gray-200 dark:border-gray-700 pl-4 space-y-6">
                            <div class="relative">
                                <div class="absolute -left-[21px] top-1 w-3 h-3 bg-indigo-500 rounded-full border-2 border-white dark:border-gray-800"></div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase font-bold mb-1">Created At</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">User account created</p>
                                <p class="text-xs text-gray-400 mt-1">{{ $user->created_at->format('d M Y, h:i A') }}</p>
                            </div>
                            <div class="relative">
                                <div class="absolute -left-[21px] top-1 w-3 h-3 bg-gray-300 dark:bg-gray-600 rounded-full border-2 border-white dark:border-gray-800"></div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase font-bold mb-1">Updated At</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">Last profile update</p>
                                <p class="text-xs text-gray-400 mt-1">{{ $user->updated_at->format('d M Y, h:i A') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- REFERRALS TAB --}}
            <div x-show="activeTab === 'referrals'" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-bold text-gray-900 dark:text-white">Referral Tree</h3>
                    <button class="px-3 py-1.5 bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400 rounded-lg text-xs font-semibold hover:bg-indigo-200 dark:hover:bg-indigo-900/50 transition-colors">
                        Load Full Tree
                    </button>
                </div>
                <div id="referral-tree" class="p-6 bg-gray-50 dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 h-96 overflow-auto custom-scrollbar">
                    {{-- Content loaded via JS --}}
                    <div class="text-center text-gray-500 mt-10">Tree visualization will be loaded here...</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modals --}}
    {{-- Blacklist Modal --}}
    <div id="blacklistModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center hidden z-50 p-4">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl w-full max-w-sm p-6">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Confirm Blacklist</h3>
            <p class="text-gray-500 dark:text-gray-400 text-sm mb-6">Are you sure you want to blacklist <span class="font-bold text-gray-900 dark:text-white">{{ $user->name }}</span>?</p>
            <div class="flex gap-3">
                <button onclick="document.getElementById('blacklistModal').classList.add('hidden')" class="flex-1 px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl font-medium hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">Cancel</button>
                <form action="{{ route('admin.users.addToBlacklist', $user->id) }}" method="POST" class="flex-1">
                    @csrf
                    <button type="submit" class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-xl font-medium shadow-lg transition-colors">Confirm</button>
                </form>
            </div>
        </div>
    </div>

    {{-- Transaction Modal --}}
    <div id="transactionModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto custom-scrollbar p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Add Transaction</h3>
                <button onclick="document.getElementById('transactionModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <form action="{{ route('admin.collection-transactions.store', $user->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Collection Type</label>
                        <select name="collection_type" required class="w-full rounded-xl border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Select...</option>
                            <option value="geran_asas">Geran Asas</option>
                            <option value="tabung_usahawan">Tabung Usahawan</option>
                            <option value="had_pembiayaan">Had Pembiayaan</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Type</label>
                        <select name="transaction_type" required class="w-full rounded-xl border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="credit">Credit (+)</option>
                            <option value="debit">Debit (-)</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Amount (RM)</label>
                    <input type="number" step="0.01" name="amount" required class="w-full rounded-xl border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500" placeholder="0.00">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date</label>
                    <input type="datetime-local" name="transaction_date" required class="w-full rounded-xl border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
                    <textarea name="description" rows="2" class="w-full rounded-xl border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Upload Slip</label>
                    <input type="file" name="slip" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 dark:file:bg-indigo-900/30 dark:file:text-indigo-400">
                </div>

                <div class="flex justify-end pt-4">
                    <button type="submit" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-medium shadow-lg transition-colors">Save Transaction</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        function updateSerial(accountId, newSerial) {
            // Placeholder for AJAX call to update serial
            // Implementing basic fetch logic
            fetch(`/admin/users/{{ $user->id }}/account/${accountId}/update-serial`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        serial_number: newSerial
                    })
                }).then(res => res.json())
                .then(data => {
                    if (data.success) {
                        alert('Serial updated!');
                        location.reload();
                    } else {
                        alert('Error updating serial');
                    }
                });
        }

        window.createAccount = function(type) {
            if (!confirm(`Create ${type} account for this user?`)) return;
            // Place logic to create account via fetch or form submission
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("admin.users.createAccount", $user->id) }}';

            const csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = '{{ csrf_token() }}';
            form.appendChild(csrf);

            const typeInput = document.createElement('input');
            typeInput.type = 'hidden';
            typeInput.name = 'type';
            typeInput.value = type;
            form.appendChild(typeInput);

            document.body.appendChild(form);
            form.submit();
        }
    </script>
    @endpush
</x-admin-layout>