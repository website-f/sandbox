<x-app-layout>
<x-slot name="header">
    <h2 class="font-extrabold text-3xl text-gray-900 tracking-tight">
        Welcome, {{ auth()->user()->name }}
    </h2>
    <p class="mt-1 text-gray-500">Manage your referral network and accounts here</p>
</x-slot>

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-3 gap-8">

        <div class="bg-white rounded-2xl shadow-lg p-6 hover:shadow-2xl transition-shadow duration-300">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">Your Referral Link & QR</h3>
            <div class="mb-4 relative">
                <input class="w-full p-3 pr-12 rounded-xl border border-gray-200 bg-gray-50 font-mono text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-400"
                    readonly value="{{ route('register',['ref'=>auth()->user()->referral?->ref_code]) }}">
                <button class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-indigo-600" onclick="copyToClipboard(this)">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M8 2a2 2 0 00-2 2v2H4a2 2 0 00-2 2v8a2 2 0 002 2h8a2 2 0 002-2v-2h2a2 2 0 002-2V4a2 2 0 00-2-2h-8zM6 4a1 1 0 011-1h6a1 1 0 011 1v2H7a1 1 0 00-1 1v6H4a1 1 0 01-1-1V8a1 1 0 011-1h2V4zm6 6a1 1 0 011-1h4a1 1 0 011 1v8a1 1 0 01-1 1h-4a1 1 0 01-1-1v-8z"/>
                    </svg>
                </button>
            </div>
            <div class="flex justify-center">
                <img src="{{ route('referrals.qr') }}" alt="QR Code" class="w-36 h-36 rounded-xl border border-gray-200 shadow-md">
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg p-6 hover:shadow-2xl transition-shadow duration-300 md:col-span-2">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">Account Status</h3>
            @if(session('error'))
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
                            $expires = $account->expires_at ? \Carbon\Carbon::parse($account->expires_at) : null;
                
                            if ($account->active) {
                                $indicatorColor = 'bg-green-500';
                                $indicatorText = 'active';
                                $showButton = false;
                                if ($expires) $expiryText = 'Valid until ' . $expires->toFormattedDateString();
                                if ($account->serial_number) $serialText = "Serial: {$account->serial_number}";
                            } elseif ($account->type === 'sandbox' && $subscription && $subscription->installments_paid > 0 && $subscription->installments_paid < $subscription->installments_total) {
                                $indicatorColor = 'bg-yellow-500';
                                $indicatorText = 'pending';
                            }
                        }
                
                        $basePrice = $account->type === 'sandbox' ? 300 : 20;
                        $tax = round($basePrice * 0.08, 2);
                        $fpx = 1.00;
                
                        // Progress is based **only on basePrice**
                        $showProgress = $account->type === 'sandbox' 
                            && $subscription 
                            && $subscription->installments_paid > 0 
                            && $subscription->installments_paid < $subscription->installments_total;
                
                        if ($showProgress) {
                            $perInstallmentBase = round($basePrice / $subscription->installments_total, 2); // Base only
                            $paidBase = round($subscription->installments_paid * $perInstallmentBase, 2);
                            $progressPercent = ($subscription->installments_paid / $subscription->installments_total) * 100;
                
                            // Next installment **with tax + FPX**
                            $nextAmount = round(($basePrice + ($basePrice * 0.08)) / $subscription->installments_total + $fpx, 2);
                        } else {
                            $perInstallmentBase = 0;
                            $paidBase = 0;
                            $progressPercent = 0;
                            $nextAmount = round($basePrice + $tax + $fpx, 2); // First payment
                        }
                
                        // Full price for one-time payment (base + tax + fpx)
                        $fullPrice = round($basePrice + $tax + $fpx, 2);
                    @endphp
                
                    <li class="flex flex-col md:flex-row justify-between items-center bg-white rounded-2xl shadow-lg p-5 gap-4 md:gap-6">
                        <div class="flex-shrink-0">
                            <img src="{{ $logos[$account->type] }}" alt="{{ ucfirst($account->type) }} Logo" class="w-12 h-12 rounded-full border border-gray-200 shadow-sm object-cover">
                        </div>
                
                        <div class="flex-1 flex flex-col gap-2">
                            <span class="font-semibold text-gray-800 text-lg">{{ ucfirst($account->type) }} @if($account->type === 'sandbox') Malaysia @endif</span>
                            <span class="flex items-center gap-2 text-sm text-gray-500">
                                <span class="inline-block w-3 h-3 rounded-full {{ $indicatorColor }}"></span>
                                {{ ucfirst($indicatorText) }}
                            </span>
                
                            @if($expiryText)
                                <span class="text-xs text-gray-400">{{ $expiryText }}</span>
                            @endif
                            @if($serialText)
                                <span class="text-xs font-semibold text-indigo-600">{{ $serialText }}</span>
                            @endif
                
                            {{-- Sandbox progress --}}
                            @if($showProgress)
                                <div class="mt-2 w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                                    <div class="bg-indigo-600 h-3 transition-all duration-300" style="width: {{ $progressPercent }}%"></div>
                                </div>
                                <p class="text-xs text-gray-600 mt-1">
                                    RM {{ number_format($paidBase, 2) }} paid / RM {{ number_format($basePrice, 2) }} total
                                </p>
                            @endif
                
                            {{-- Buttons --}}
                            <div>
                                @if($showButton)
                                    @if ($account->type === 'sandbox')
                                        @if($showProgress)
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
                                @endif
                            </div>
                        </div>
                    </li>
                @endforeach
                
                                
            </ul>

            
            {{-- Modal --}}

            <div
                x-data="{ 
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
                x-show="open"
                x-cloak
                style="display:none"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75 backdrop-blur-sm p-4">
            
                <div @click.away="open = false" class="bg-white rounded-2xl shadow-2xl w-full max-w-lg p-8 space-y-6 transform transition-all duration-300">
                    <h2 class="text-2xl font-bold text-gray-800 border-b pb-3" x-text="label + ' Subscription'"></h2>
            
                    <p class="text-lg font-semibold text-gray-700">Choose your payment plan:</p>
                    
                    <div class="grid grid-cols-2 gap-4">
                        {{-- Option 1: Full Payment --}}
                        <button 
                            @click="installments = 1" 
                            :class="{'border-indigo-600 ring-2 ring-indigo-300': installments == 1, 'border-gray-300 hover:border-indigo-400': installments != 1}"
                            class="p-4 border-2 rounded-xl text-left transition duration-200">
                            <p class="font-bold text-lg" :class="{'text-indigo-600': installments == 1}">Full Payment</p>
                            <p class="text-xl font-extrabold mt-1">RM <span x-text="fullFinal.toFixed(2)"></span></p>
                            <p class="text-xs text-gray-500">One-time charge. Full access.</p>
                        </button>
            
                        {{-- Option 2: 3 Installments --}}
                        <button 
                            @click="installments = 3" 
                            :class="{'border-indigo-600 ring-2 ring-indigo-300': installments == 3, 'border-gray-300 hover:border-indigo-400': installments != 3}"
                            class="p-4 border-2 rounded-xl text-left transition duration-200">
                            <p class="font-bold text-lg" :class="{'text-indigo-600': installments == 3}">3 Installments</p>
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
                        <h3 class="font-bold text-base text-gray-800 mb-2" x-text="'Payment Breakdown: ' + (installments == 1 ? 'Full Payment' : 'First Installment')"></h3>
                        
                        <div x-show="installments == 1">
                            <div class="flex justify-between"><span>Base Price</span> <span>RM <span x-text="fullBase.toFixed(2)"></span></span></div>
                            <div class="flex justify-between"><span>Tax (8%)</span> <span>RM <span x-text="fullTax.toFixed(2)"></span></span></div>
                            <div class="flex justify-between"><span>FPX Charge</span> <span>RM <span x-text="fullFpx.toFixed(2)"></span></span></div>
                        </div>
            
                        <div x-show="installments > 1">
                            <div class="flex justify-between"><span>Base Price (per installment)</span> <span>RM <span x-text="installmentBase"></span></span></div>
                            <div class="flex justify-between"><span>Tax (per installment)</span> <span>RM <span x-text="installmentTax"></span></span></div>
                            <div class="flex justify-between"><span>FPX Charge (per installment)</span> <span>RM <span x-text="installmentFpx"></span></span></div>
                        </div>
            
                        <div class="border-t pt-2 flex justify-between font-semibold text-gray-800">
                            <span>Total Subscription Cost</span> <span>RM <span x-text="fullFinal.toFixed(2)"></span></span>
                        </div>
            
                        <div class="pt-2 flex justify-between font-extrabold text-indigo-600 text-lg">
                            <span x-text="installments == 1 ? 'Total to Pay Now' : 'Amount for First Installment'"></span> 
                            <span>RM <span x-text="installmentAmount"></span></span>
                        </div>
                    </div>
            
                    <div class="flex justify-end gap-3 pt-4">
                        <button @click="open = false" class="px-5 py-2 rounded-lg text-gray-600 border border-gray-300 hover:bg-gray-100 font-semibold transition">Cancel</button>
                        <form method="POST" :action="'/subscribe/' + plan">
                            @csrf
                            <input type="hidden" name="installments" x-model="installments">
                            <button type="submit" class="px-5 py-2 rounded-lg bg-indigo-600 text-white font-bold shadow-md hover:bg-indigo-700 transition">
                                Pay RM <span x-text="installmentAmount"></span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
                        
            <div 
                x-data="{ 
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
                x-show="open"
                x-cloak
                style="display:none"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75 backdrop-blur-sm p-4">
            
                <div @click.away="open = false" class="bg-white rounded-2xl shadow-2xl w-full max-w-lg p-8 space-y-6 transform transition-all duration-300">
                    <h2 class="text-2xl font-bold text-gray-800 border-b pb-3">Pay Subscription</h2>
            
                    <p class="text-md font-semibold text-gray-700">Payment Progress: <span class="font-bold text-indigo-600" x-text="paidCount + ' / ' + totalInstallments"></span></p>
            
                    {{-- Payment Option Selection --}}
                    <div class="grid grid-cols-2 gap-4">
                        {{-- Option 1: Next Installment --}}
                        <button 
                            @click="selectedOption = 'next'" 
                            :class="{'border-indigo-600 ring-2 ring-indigo-300': selectedOption === 'next', 'border-gray-300 hover:border-indigo-400': selectedOption !== 'next'}"
                            class="p-4 border-2 rounded-xl text-left transition duration-200">
                            <p class="font-bold text-lg" :class="{'text-indigo-600': selectedOption === 'next'}">Next Installment</p>
                            <p class="text-xl font-extrabold mt-1">RM <span x-text="Number(installmentAmount).toFixed(2)"></span></p>
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
                        <h3 class="font-bold text-base text-gray-800 mb-2" x-text="'Breakdown for: ' + (selectedOption === 'full' ? 'Full Settlement' : 'Next Installment')"></h3>
                        
                        <div class="flex justify-between"><span>Base Price</span> <span>RM <span x-text="currentBase.toFixed(2)"></span></span></div>
                        <div class="flex justify-between"><span>Tax (8%)</span> <span>RM <span x-text="tax.toFixed(2)"></span></span></div>
                        <div class="flex justify-between"><span>FPX Charge</span> <span>RM <span x-text="fpx.toFixed(2)"></span></span></div>
            
                        <div class="border-t pt-2 flex justify-between font-extrabold text-indigo-600 text-lg">
                            <span x-text="selectedOption === 'full' ? 'Total Settlement Amount' : 'Total Installment Amount'"></span> 
                            <span>RM <span x-text="currentAmount.toFixed(2)"></span></span>
                        </div>
                    </div>
            
                    <div class="flex justify-end gap-3 pt-4">
                        <button @click="open = false" class="px-5 py-2 rounded-lg text-gray-600 border border-gray-300 hover:bg-gray-100 font-semibold transition">Cancel</button>
            
                        <form method="POST" :action="'/subscribe/pay-next/' + subscriptionId">
                            @csrf
                            <input type="hidden" name="full_settlement" :value="selectedOption === 'full' ? 1 : 0">
                            <button type="submit" 
                                    :disabled="!selectedOption"
                                    class="px-5 py-2 rounded-lg bg-indigo-600 text-white font-bold shadow-md hover:bg-indigo-700 transition disabled:opacity-50">
                                Pay RM <span x-text="currentAmount.toFixed(2)"></span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <div 
                x-data="{ open: false, plan: '', label: '', base: 0, tax: 0, fpx: 0, final: 0 }"
                x-on:open-modal.window="open = true; plan = $event.detail.plan; label = $event.detail.label; base = $event.detail.base; tax = $event.detail.tax; fpx = $event.detail.fpx; final = $event.detail.final"
                x-show="open"
                style="display: none"
                class="fixed inset-0 z-50 flex items-center justify-center 
                 bg-white backdrop-blur-sm transition-opacity">
                
                <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6 space-y-6">
                    <h2 class="text-lg font-bold text-gray-800" x-text="label + ' Subscription'"></h2>
            
                    <div class="space-y-2 text-sm text-gray-600">
                        <div class="flex justify-between"><span>Base Price</span> <span>RM <span x-text="base"></span></span></div>
                        <div class="flex justify-between"><span>Tax (8%)</span> <span>RM <span x-text="tax"></span></span></div>
                        <div class="flex justify-between"><span>FPX Charge</span> <span>RM <span x-text="fpx"></span></span></div>
                        <div class="border-t pt-2 flex justify-between font-semibold text-gray-800">
                            <span>Total</span> <span>RM <span x-text="final"></span></span>
                        </div>
                    </div>
            
                    <div class="flex justify-end gap-3">
                        <button @click="open = false" class="px-4 py-2 rounded-lg text-gray-600 hover:bg-gray-100">Cancel</button>
                        
                        <form method="POST" :action="'/subscribe/' + plan">
                            @csrf
                            <button type="submit" class="px-4 py-2 rounded-lg bg-indigo-600 text-white font-semibold hover:bg-indigo-700">
                                Confirm & Pay
                            </button>
                        </form>
                    </div>
                </div>
            </div>



            </ul>
        </div>



        @if(auth()->user()->hasRole('Admin'))
            <div class="lg:col-span-3 bg-white rounded-2xl shadow-lg p-6 hover:shadow-2xl transition-shadow duration-300">
                <h3 class="text-xl font-semibold text-gray-800 mb-4">All Users</h3>
                {{-- Search form --}}
                <form method="GET" action="{{ route('dashboard') }}" class="mb-6 flex">
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Search by name, email, or serial number..."
                           class="w-full px-4 py-2 border rounded-l-lg focus:ring-indigo-500 focus:border-indigo-500">
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-r-lg hover:bg-indigo-700">
                        Search
                    </button>
                </form>
        
                <table class="w-full border border-gray-200 rounded-xl overflow-hidden">
                    <thead class="bg-gray-50 text-left text-sm font-semibold text-gray-600">
                        <tr>
                            <th class="px-4 py-3">RM No</th>
                            <th class="px-4 py-3">SB No</th>
                            <th class="px-4 py-3">Name</th>
                            <th class="px-4 py-3">Email</th>
                            <th class="px-4 py-3">Phone</th>
                            <th class="px-4 py-3">Referrer</th>
                            <th class="px-4 py-3">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($users as $u)
                            @php
                                $rizqmall = $u->accounts->firstWhere('type', 'rizqmall');
                                $sandbox  = $u->accounts->firstWhere('type', 'sandbox');
                            @endphp
                            <tr>
                                <td class="px-4 py-3 text-sm {{ $rizqmall && $rizqmall->serial_number ? 'text-green-600 font-semibold' : 'text-red-600 font-semibold' }}">
                                    {{ $rizqmall->serial_number ?? 'inactive' }}
                                </td>
                                <td class="px-4 py-3 text-sm {{ $sandbox && $sandbox->serial_number ? 'text-green-600 font-semibold' : 'text-red-600 font-semibold' }}">
                                    {{ $sandbox->serial_number ?? 'inactive' }}
                                </td>
                                <td class="px-4 py-3">
                                     {{ $u->profile?->full_name ?? $u->name }}
                                 </td>
                                <td class="px-4 py-3">{{ $u->email }}</td>
                                <td class="px-4 py-3">{{ $u->profile->phone ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $u->referral?->parent?->name ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    <!-- View Details Button -->
                                    <button 
                                        data-user="{{ $u->id }}"
                                        class="view-details px-3 py-1 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold">
                                        View Details
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {{-- Pagination --}}
                <div class="mt-6">
                    {{ $users->links() }}
                </div>
            </div>
        
            <!-- Details Modal -->
            <div id="detailsModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white rounded-2xl shadow-lg w-full max-w-2xl p-6 relative">
                    <!-- X button -->
                    <button 
                        onclick="document.getElementById('detailsModal').classList.add('hidden')" 
                        class="absolute top-3 right-3 text-black hover:text-gray-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
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
            <div class="lg:col-span-3 bg-white rounded-2xl shadow-lg p-6 hover:shadow-2xl transition-shadow duration-300">
                <h3 class="text-xl font-semibold text-gray-800 mb-4">Your Referrals</h3>
                {{-- Search form --}}
                <form method="GET" action="{{ route('dashboard') }}" class="mb-6 flex">
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Search by name, email, or serial number..."
                           class="w-full px-4 py-2 border rounded-l-lg focus:ring-indigo-500 focus:border-indigo-500">
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-r-lg hover:bg-indigo-700">
                        Search
                    </button>
                </form>
                <table class="w-full border border-gray-200 rounded-xl overflow-hidden">
                    <thead class="bg-gray-50 text-left text-sm font-semibold text-gray-600">
                        <tr>
                            <th class="px-4 py-3">RM No</th>
                            <th class="px-4 py-3">SB No</th>
                            <th class="px-4 py-3">Name</th>
                            <th class="px-4 py-3">Email</th>
                            <th class="px-4 py-3">Phone</th>
                            <th class="px-4 py-3">Referrer</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($users as $u)
                            @php
                                $rizqmall = $u->accounts->firstWhere('type', 'rizqmall');
                                $sandbox  = $u->accounts->firstWhere('type', 'sandbox');
                            @endphp
                            <tr>
                                <td class="px-4 py-3 text-sm {{ $rizqmall && $rizqmall->serial_number ? 'text-green-600 font-semibold' : 'text-red-600 font-semibold' }}">
                                    {{ $rizqmall->serial_number ?? 'inactive' }}
                                </td>
                                <td class="px-4 py-3 text-sm {{ $sandbox && $sandbox->serial_number ? 'text-green-600 font-semibold' : 'text-red-600 font-semibold' }}">
                                    {{ $sandbox->serial_number ?? 'inactive' }}
                                </td>
        
                                 <td class="px-4 py-3">
                                     {{ $u->profile?->full_name ?? $u->name }}
                                 </td>
                                <td class="px-4 py-3">{{ $u->email }}</td>
                                <td class="px-4 py-3">{{ $u->profile->phone ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    {{ $u->referral?->parent_id === auth()->id() ? 'YOU' : $u->referral?->parent?->name }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
        
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

</x-app-layout>