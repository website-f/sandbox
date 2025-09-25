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

        <div class="bg-white rounded-2xl shadow-lg p-6 hover:shadow-2xl transition-shadow duration-300">
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
                    
            @foreach (['rizqmall'=>'RizqMall','sandbox'=>'Sandbox'] as $k => $label)
                @php
                    $account = $accounts[$k] ?? null;
                    $indicatorColor = 'bg-red-500';
                    $indicatorText = 'inactive';
                    $expiryText = '';
                    $serialText = '';
                    $showButton = true;

                    // Check subscription progress for Sandbox
                    $subscription = isset($subscriptions[$k]) ? $subscriptions[$k]->last() : null;
                    $progressCount = ($subscription && $subscription->payment && $subscription->payment->status === 'success') ? 1 : 0;
                    $totalInstallments = $subscription ? $subscription->installments_total : 0;

            
                    if($account) {
                        $expires = $account->expires_at ? \Carbon\Carbon::parse($account->expires_at) : null;
            
                        if ($account->active) {
                            $indicatorColor = 'bg-green-500';
                            $indicatorText = 'active';
                            $showButton = false;
            
                            if ($expires) $expiryText = 'Valid until ' . $expires->toFormattedDateString();
                            if ($account->serial_number) $serialText = "Serial: {$account->serial_number}";
                        } elseif ($k === 'sandbox' && $subscription && $progressCount > 0) {
                            // Sandbox pending installments
                            $indicatorColor = 'bg-yellow-500';
                            $indicatorText = "pending ({$progressCount}/{$totalInstallments})";
                        }
                    }
            
                    // Pricing logic
                    $basePrice = $k === 'sandbox' ? 300 : 20; // RM
                    $tax = round($basePrice * 0.08, 2);
                    $fpx = 1.00;
                    $final = $basePrice + $tax + $fpx;
                @endphp
            
                <li class="flex justify-between items-center p-4 rounded-xl bg-gray-50 shadow-sm hover:shadow-md transition-shadow duration-200">
                    <div class="flex flex-col">
                        <span class="font-medium text-gray-700">{{ $label }}</span>
                        <span class="flex items-center gap-2 text-sm text-gray-500">
                            <span class="inline-block w-3 h-3 rounded-full {{ $indicatorColor }}"></span>
                            {{ ucfirst($indicatorText) }}
                        </span>
                        @if($expiryText)
                            <span class="text-xs text-gray-500 mt-1">{{ $expiryText }}</span>
                        @endif
                        @if($serialText)
                            <span class="text-xs font-semibold text-indigo-600 mt-1">{{ $serialText }}</span>
                        @endif
                    </div>
            
                    <div>
                        @php
                            $nextAmount = 0;
                            if ($subscription && $subscription->payment) {
                                $paidCount = $subscription->installments_paid;
                                $totalInstallments = $subscription->installments_total ?? 0;
                        
                                $basePrice = 300; // Sandbox base price
                                $tax = round($basePrice * 0.08, 2);
                                $fpx = 1.00;
                                $installmentAmount = ($basePrice + $tax + $fpx) / $totalInstallments;
                        
                                if ($paidCount < $totalInstallments) {
                                    $nextAmount = $installmentAmount;
                                }
                            }
                        @endphp

                       @if($showButton)
                           @if($k === 'sandbox' && isset($subscriptions[$k]))
                               @php $subscription = $subscriptions[$k]->last(); @endphp
                       
                               <p>
                                   Progress: {{ $subscription->installments_paid }}
                                   / {{ $subscription->installments_total }}
                               </p>
                       

                               <button 
                                  x-data
                                  @click="$dispatch('open-pay-next-modal', {
                                      subscriptionId: {{ json_encode($subscription->id) }},
                                      amount: {{ json_encode($nextAmount) }}
                                  })"
                                  class="px-3 py-2 bg-indigo-600 text-white rounded-lg">
                                  Pay Next Installment
                              </button>


                           @else
                               {{-- 🔹 Sandbox → installment modal, RizqMall → simple modal --}}
                               @if($k === 'sandbox')
                                   <button 
                                       x-data 
                                       @click="$dispatch('open-installment-modal', {
                                           plan: '{{ $k }}',
                                           label: '{{ $label }}',
                                           base: '{{ $basePrice }}',
                                           tax: '{{ $tax }}',
                                           fpx: '{{ $fpx }}',
                                           final: '{{ $final }}'
                                       })"
                                       class="px-4 py-2 text-sm font-semibold text-white rounded-full shadow bg-indigo-600 hover:bg-indigo-700">
                                       Subscribe
                                   </button>
                               @else
                                   <button 
                                       x-data 
                                       @click="$dispatch('open-modal', {
                                           plan: '{{ $k }}',
                                           label: '{{ $label }}',
                                           base: '{{ $basePrice }}',
                                           tax: '{{ $tax }}',
                                           fpx: '{{ $fpx }}',
                                           final: '{{ $final }}'
                                       })"
                                       class="px-4 py-2 text-sm font-semibold text-white rounded-full shadow bg-indigo-600 hover:bg-indigo-700">
                                       Subscribe
                                   </button>
                               @endif
                           @endif
                       @endif


                    </div>
                </li>
            @endforeach
            
            {{-- Modal --}}

            <div 
    x-data="{ open: false, plan: '', label: '', base: 0, tax: 0, fpx: 0, final: 0, installments: 1 }"
    x-on:open-installment-modal.window="open = true; plan = $event.detail.plan; label = $event.detail.label; base = $event.detail.base; tax = $event.detail.tax; fpx = $event.detail.fpx; final = $event.detail.final"
    x-show="open"
    style="display:none"
    class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
    
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6 space-y-6">
        <h2 class="text-lg font-bold text-gray-800" x-text="label + ' Subscription (Sandbox)'"></h2>

        <p class="text-gray-600">Choose payment option:</p>
        <div class="space-y-2">
            <label class="flex items-center space-x-2">
                <input type="radio" value="1" x-model="installments">
                <span>Full Payment (RM <span x-text="final"></span>)</span>
            </label>
            <label class="flex items-center space-x-2">
                <input type="radio" value="3" x-model="installments">
                <span>3 Payments (RM <span x-text="(final/3).toFixed(2)"></span> each)</span>
            </label>
            <label class="flex items-center space-x-2">
                <input type="radio" value="6" x-model="installments">
                <span>6 Payments (RM <span x-text="(final/6).toFixed(2)"></span> each)</span>
            </label>
        </div>

        <div class="flex justify-end gap-3">
            <button @click="open = false" class="px-4 py-2 rounded-lg text-gray-600 hover:bg-gray-100">Cancel</button>
            <form method="POST" :action="'/subscribe/' + plan">
                @csrf
                <input type="hidden" name="installments" x-model="installments">
                <button type="submit" class="px-4 py-2 rounded-lg bg-indigo-600 text-white font-semibold hover:bg-indigo-700">
                    Confirm & Pay
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Next Installment Modal -->
<div 
    x-data="{ open: false, subscriptionId: null, amount: 0 }"
    x-on:open-pay-next-modal.window="open = true; subscriptionId = $event.detail.subscriptionId; amount = $event.detail.amount"
    x-show="open"
    style="display:none"
    class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">

    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6 space-y-6">
        <h2 class="text-lg font-bold text-gray-800">Pay Next Installment</h2>

        <p class="text-gray-600">Are you sure you want to pay your next installment?</p>
        <p class="font-semibold text-indigo-600">
            Amount: RM <span x-text="amount.toFixed(2)"></span>
        </p>

        <div class="flex justify-end gap-3">
            <button @click="open = false" class="px-4 py-2 rounded-lg text-gray-600 hover:bg-gray-100">Cancel</button>

            <form method="POST" :action="'/subscribe/pay-next/' + subscriptionId">
                @csrf
                <button type="submit" class="px-4 py-2 rounded-lg bg-indigo-600 text-white font-semibold hover:bg-indigo-700">
                    Confirm & Pay
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