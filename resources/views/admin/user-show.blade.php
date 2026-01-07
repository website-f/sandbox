<x-app-layout>
<style>
    #referral-tree ul, #sandbox-tree ul {
        list-style-type: none;
        margin-left: 20px;
        position: relative;
    }

    #referral-tree ul::before, #sandbox-tree ul::before {
        content: '';
        border-left: 1px solid #ccc;
        position: absolute;
        top: 0;
        bottom: 0;
        left: 0;
    }

    #referral-tree li, #sandbox-tree li {
        margin: 0;
        padding: 0 0 0 20px;
        line-height: 1.5em;
        position: relative;
    }

    #referral-tree li::before, #sandbox-tree li::before {
        content: '';
        border-top: 1px solid #ccc;
        position: absolute;
        top: 0.8em;
        left: 0;
        width: 20px;
    }
</style>
    <x-slot name="header">
        <h2 class="font-extrabold text-2xl text-gray-900">User Details</h2>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-7xl sm:max-w-6xl mx-auto bg-white shadow rounded-2xl p-4 sm:p-6">
            <div class="flex flex-col sm:flex-row items-start gap-4 sm:gap-6">
                <div class="flex-shrink-0 mx-auto sm:mx-0">
                    <div class="h-20 w-20 bg-gray-100 rounded-full flex items-center justify-center text-2xl text-gray-500">
                        {{ strtoupper(substr($user->name,0,1)) }}
                    </div>
                </div>

                <div class="flex-1 w-full">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                        <div class="w-full break-words">
                            <div class="flex items-center gap-2">
                                <h3 id="userNameDisplay" class="text-xl font-semibold">
                                    {{ $user->profile?->full_name ?? $user->name }}
                                </h3>
                                <button id="editUserNameBtn" class="text-gray-500 hover:text-indigo-600">
                                    ✏️
                                </button>
                            </div>
                            
                            <form id="editUserNameForm" action="{{ route('admin.users.updateName', $user->id) }}" method="POST" class="hidden flex items-center gap-2">
                                @csrf
                                @method('PUT')
                                <input type="text" name="name" id="userNameInput" class="border rounded px-2 py-1 text-sm w-48"
                                       value="{{ $user->profile?->full_name ?? $user->name }}" required>
                                <button type="submit" class="px-2 py-1 bg-green-600 text-white rounded text-sm">✅</button>
                                <button type="button" id="cancelEditUserNameBtn" class="px-2 py-1 bg-gray-300 rounded text-sm">❌</button>
                            </form>
                            
                            @if($user->checkBlacklist())
                                <span class="px-2 py-1 bg-red-500 text-white text-xs rounded">Blacklisted</span>
                            @endif

                            <p class="text-sm text-gray-500">{{ $user->email }}</p>
                            <p class="text-sm text-gray-500 mt-1">
                                Phone: {{ $user->profile?->phone ?? '-' }} • NRIC: {{ $user->profile?->nric ?? '-' }}
                            </p>
                        </div>

                        <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
                             <button id="toggleAdminBtn"
                                     class="px-3 py-2 rounded-lg text-sm font-semibold {{ $user->hasRole('Admin') ? 'bg-red-500' : 'bg-indigo-600' }} text-white">
                                 {{ $user->hasRole('Admin') ? 'Remove Admin' : 'Make Admin' }}
                             </button>
                         
                             @unless($user->checkBlacklist())
                                 <button id="addBlacklistBtn"
                                         class="px-3 py-2 rounded-lg text-sm font-semibold bg-gray-800 text-white">
                                     Add to Blacklist
                                 </button>
                             @endunless
                         
                             <a href="{{ route('admin.users.index') }}" class="px-3 py-2 rounded-lg border">Back</a>
                         
                             @if($user->checkBlacklist())
                                 <span class="px-2 py-1 bg-red-500 text-white text-xs rounded">Blacklisted</span>
                             @endif
                         </div>


                        <!-- Modal -->
                            <div id="blacklistModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
                                <div class="bg-white rounded-lg p-6 w-96">
                                    <h3 class="text-lg font-semibold mb-4">Confirm Blacklist</h3>
                                    <p>Are you sure you want to add <span class="font-medium">{{ $user->email }}</span> to the blacklist?</p>
                                    <div class="mt-4 flex justify-end gap-2">
                                        <button id="cancelBlacklist" class="px-3 py-2 bg-gray-200 rounded">Cancel</button>
                                        <button id="confirmBlacklist" class="px-3 py-2 bg-red-500 text-white rounded">Confirm</button>
                                    </div>
                                </div>
                            </div>
                    </div>

                    <p class="mt-4 text-sm text-gray-600">Joined: {{ $user->created_at?->format('d M Y H:i') ?? '-' }}</p>
                </div>
            </div>

            {{-- Tabs --}}
            <div class="mt-6">
                <nav class="flex space-x-2 border-b overflow-x-auto whitespace-nowrap">
                    <button data-tab="overview" class="py-3 px-4 -mb-px border-b-2 border-indigo-600 text-indigo-600">Overview</button>
                    <button data-tab="accounts" class="py-3 px-4 text-gray-600">Accounts</button>
                    <button data-tab="bank" class="py-3 px-4 text-gray-600">Bank Details</button>
                    <button data-tab="wallet" class="py-3 px-4 text-gray-600">Wallet & Payments</button>
                    <button data-tab="collection" class="py-3 px-4 text-gray-600">Collection/Tabung</button>
                    <button data-tab="referrals" class="py-3 px-4 text-gray-600">Referrals</button>
                    <button data-tab="sandbox" class="py-3 px-4 text-gray-600">Sandbox Tree</button>
                    <button data-tab="edit" class="py-3 px-4 text-gray-600">Edit / Audit</button>
                </nav>

                <div class="mt-6">
                    {{-- OVERVIEW --}}
                    <div data-panel="overview" class="tab-panel">
                        <div class="grid md:grid-cols-2 gap-6">
                            {{-- Profile --}}
                            <div class="p-4 border rounded-lg">
    <div class="flex justify-between items-center mb-2">
        <h4 class="font-semibold">Profile</h4>
        <button id="editProfileBtn" class="text-gray-500 hover:text-indigo-600">
            ✏️ Edit
        </button>
    </div>
    
    {{-- Display Mode --}}
    <div id="profileDisplay">
        <dl class="text-sm text-gray-700 space-y-1">
            <div><span class="font-medium">Full name:</span> <span id="display-full_name">{{ $user->profile?->full_name ?? '-' }}</span></div>
            <div><span class="font-medium">NRIC:</span> <span id="display-nric">{{ $user->profile?->nric ?? '-' }}</span></div>
            <div><span class="font-medium">DOB:</span> <span id="display-dob">{{ $user->profile?->dob ?? '-' }}</span></div>
            <div><span class="font-medium">Phone:</span> <span id="display-phone">{{ $user->profile?->phone ?? '-' }}</span></div>
            <div><span class="font-medium">Address:</span> <span id="display-home_address">{!! nl2br(e($user->profile?->home_address ?? '-')) !!}</span></div>
            <div><span class="font-medium">Country:</span> <span id="display-country">{{ $user->profile?->country ?? '-' }}</span></div>
            <div><span class="font-medium">State:</span> <span id="display-state">{{ $user->profile?->state ?? '-' }}</span></div>
            <div><span class="font-medium">City:</span> <span id="display-city">{{ $user->profile?->city ?? '-' }}</span></div>
        </dl>
    </div>

    {{-- Edit Mode --}}
    <form id="editProfileForm" action="{{ route('admin.users.updateProfile', $user->id) }}" method="POST" class="hidden space-y-3">
        @csrf
        @method('PUT')
        
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Full Name</label>
            <input type="text" name="full_name" id="input-full_name" 
                   class="border rounded px-2 py-1 text-sm w-full"
                   value="{{ $user->profile?->full_name ?? '' }}">
        </div>

        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">NRIC</label>
            <input type="text" name="nric" id="input-nric" 
                   class="border rounded px-2 py-1 text-sm w-full"
                   value="{{ $user->profile?->nric ?? '' }}">
        </div>

        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Date of Birth</label>
            <input type="date" name="dob" id="input-dob" 
                   class="border rounded px-2 py-1 text-sm w-full"
                   value="{{ $user->profile?->dob ?? '' }}">
        </div>

        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Phone</label>
            <input type="text" name="phone" id="input-phone" 
                   class="border rounded px-2 py-1 text-sm w-full"
                   value="{{ $user->profile?->phone ?? '' }}">
        </div>

        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Address</label>
            <textarea name="home_address" id="input-home_address" 
                      class="border rounded px-2 py-1 text-sm w-full" rows="2">{{ $user->profile?->home_address ?? '' }}</textarea>
        </div>

        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Country</label>
            <select name="country" id="input-country" 
                    class="border rounded px-2 py-1 text-sm w-full">
                <option value="">-- Select Country --</option>
            </select>
        </div>

        <div id="state-wrapper-edit" class="hidden">
            <label class="block text-xs font-medium text-gray-600 mb-1">State</label>
            <select name="state" id="input-state" 
                    class="border rounded px-2 py-1 text-sm w-full">
                <option value="">-- Select State --</option>
            </select>
        </div>

        <div id="city-wrapper-edit" class="hidden">
            <label class="block text-xs font-medium text-gray-600 mb-1">City</label>
            <select name="city" id="input-city" 
                    class="border rounded px-2 py-1 text-sm w-full">
                <option value="">-- Select City --</option>
            </select>
        </div>

        <div class="flex gap-2 pt-2">
            <button type="submit" class="px-3 py-1 bg-green-600 text-white rounded text-sm">✅ Save</button>
            <button type="button" id="cancelEditProfileBtn" class="px-3 py-1 bg-gray-300 rounded text-sm">❌ Cancel</button>
        </div>
    </form>
</div>

                            {{-- Business / Education --}}
                            <div class="p-4 border rounded-lg space-y-3">
                                <div>
                                    <h4 class="font-semibold mb-2">Businesses</h4>
                                    @forelse(($user->businesses ?? collect([])) as $b)
                                        <div class="text-sm text-gray-700 mb-2">
                                            <div class="font-medium">{{ $b->company_name ?? '-' }} ({{ $b->ssm_no ?? '' }})</div>
                                            <div class="text-xs text-gray-500">{{ $b->industry ?? '-' }} • {{ $b->business_model ?? '-' }}</div>
                                        </div>
                                    @empty
                                        <div class="text-sm text-gray-500">No businesses recorded.</div>
                                    @endforelse
                                </div>

                                <div>
                                    <h4 class="font-semibold mb-2">Education / Courses</h4>
                                    <div class="text-sm text-gray-700">
                                        <div>Primary: {{ $user->educations?->first()?->primary ?? '-' }}</div>
                                        <div>Secondary: {{ $user->educations?->first()?->secondary ?? '-' }}</div>
                                        <div>Higher: {{ $user->educations?->first()?->higher ?? '-' }}</div>
                                    </div>

                                    @if(($user->courses ?? collect([]))->count())
                                        <div class="mt-2">
                                            <h5 class="text-sm font-medium">Courses</h5>
                                            <ul class="text-sm text-gray-700 list-disc ml-5">
                                                @foreach(($user->courses ?? collect([])) as $course)
                                                    <li>{{ $course->title ?? '-' }} — {{ $course->provider ?? '-' }} ({{ $course->year ?? '-' }})</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @else
                                        <div class="text-sm text-gray-500">No courses recorded.</div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Next of kin & affiliations --}}
                        <div class="mt-6 grid md:grid-cols-2 gap-6">
                            <div class="p-4 border rounded-lg">
                                <h4 class="font-semibold mb-2">Next of Kin</h4>
                                @forelse(($user->nextOfKins ?? collect([])) as $nok)
                                    <div class="text-sm text-gray-700 mb-2">
                                        <div class="font-medium">{{ $nok->name ?? '-' }} ({{ $nok->relationship ?? '-' }})</div>
                                        <div class="text-xs text-gray-500">{{ $nok->phone ?? '-' }} • {{ $nok->address ?? '-' }}</div>
                                    </div>
                                @empty
                                    <div class="text-sm text-gray-500">No next-of-kin recorded.</div>
                                @endforelse
                            </div>

                            <div class="p-4 border rounded-lg">
                                <h4 class="font-semibold mb-2">Affiliations</h4>
                                @forelse(($user->affiliations ?? collect([])) as $a)
                                    <div class="text-sm text-gray-700 mb-2">
                                        <div class="font-medium">{{ $a->organization ?? '-' }}</div>
                                        <div class="text-xs text-gray-500">{{ $a->position ?? '-' }}</div>
                                    </div>
                                @empty
                                    <div class="text-sm text-gray-500">No affiliations recorded.</div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    {{-- ACCOUNTS --}}
                    <div data-panel="accounts" class="tab-panel hidden">
                        <div class="grid md:grid-cols-2 gap-6">
                            @forelse(($user->accounts ?? collect([])) as $account)
                                <div class="p-4 border rounded-lg {{ $account->active ? 'border-green-200 bg-green-50' : 'border-gray-200' }}">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h4 class="font-semibold capitalize flex items-center gap-2">
                                                {{ $account->type ?? '-' }} Account
                                                @if($account->active)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">Active</span>
                                                @else
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600">Inactive</span>
                                                @endif
                                            </h4>
                                            <p class="text-sm text-gray-600 mt-2">
                                                <span class="font-medium">Serial:</span>
                                                <span id="serial-{{ $account->id }}" class="font-mono {{ $account->serial_number ? 'text-indigo-600' : 'text-gray-400' }}">
                                                    {{ $account->serial_number ?? 'Not assigned' }}
                                                </span>
                                            </p>
                                            <p class="text-sm text-gray-600">
                                                <span class="font-medium">Status:</span>
                                                <span id="status-{{ $account->id }}">{{ $account->active ? 'Active' : 'Inactive' }}</span>
                                            </p>
                                            <p class="text-sm text-gray-600" id="expires-row-{{ $account->id }}" style="{{ $account->expires_at ? '' : 'display:none;' }}">
                                                <span class="font-medium">Expires:</span>
                                                <span id="expires-{{ $account->id }}">{{ $account->expires_at?->format('d M Y') ?? '-' }}</span>
                                            </p>
                                        </div>

                                        <div class="space-y-2 text-right">
                                            <button data-account-id="{{ $account->id }}"
                                                    class="toggle-account-btn px-3 py-1.5 rounded text-sm font-medium transition-colors
                                                           {{ $account->active ? 'bg-red-100 text-red-700 hover:bg-red-200' : 'bg-green-600 text-white hover:bg-green-700' }}">
                                                {{ $account->active ? 'Deactivate' : 'Activate' }}
                                            </button>

                                            <button data-account-id="{{ $account->id }}"
                                                    class="edit-serial-btn px-3 py-1.5 rounded bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700 transition-colors">
                                                Edit Serial
                                            </button>
                                        </div>
                                    </div>

                                    {{-- inline serial edit form (hidden) --}}
                                    <div id="serial-edit-{{ $account->id }}" class="mt-3 hidden">
                                        <div class="flex items-center gap-2">
                                            <input type="text" id="serial-input-{{ $account->id }}"
                                                   class="border rounded p-2 flex-1 font-mono text-sm"
                                                   value="{{ $account->serial_number ?? '' }}"
                                                   placeholder="e.g., RM2601070001">
                                            <button data-account-id="{{ $account->id }}" class="save-serial-btn px-3 py-2 bg-green-600 text-white rounded text-sm font-medium hover:bg-green-700">Save</button>
                                            <button onclick="document.getElementById('serial-edit-{{ $account->id }}').classList.add('hidden')" class="px-3 py-2 bg-gray-200 rounded text-sm">Cancel</button>
                                        </div>
                                        <div id="serial-error-{{ $account->id }}" class="text-red-500 text-sm mt-1"></div>
                                        <p class="text-xs text-gray-500 mt-1">Format: RM/SB + YYMMDD + 4-digit number (e.g., RM2601070001)</p>
                                    </div>
                                </div>
                            @empty
                                <p class="text-sm text-gray-500">No accounts recorded.</p>
                            @endforelse
                        </div>
                    </div>

                    <div data-panel="bank" class="tab-panel hidden">
                        <div class="grid md:grid-cols-2 gap-6">
                            <div class="p-4 border rounded-lg">
                                <h4 class="font-semibold mb-2">Bank Details</h4>
                                <dl class="text-sm text-gray-700">
                                    <div><span class="font-medium">Bank Name:</span> {{ $user->bank?->bank_name ?? '-' }}</div>
                                    <div><span class="font-medium">Account Number:</span> {{ $user->bank?->account_number ?? '-' }}</div>
                                    <div><span class="font-medium">Account Holder:</span> {{ $user->bank?->account_holder ?? '-' }}</div>
                                    
                                </dl>
                            </div>
                        </div>
                    </div>

                    {{-- WALLET & PAYMENTS --}}
                    <div data-panel="wallet" class="tab-panel hidden">
                        <div class="grid md:grid-cols-2 gap-6">
                            <div class="p-4 border rounded-lg">
                                <h4 class="font-semibold mb-2">Wallet</h4>
                                <p class="text-2xl font-bold">{{ number_format(($user->wallet?->balance ?? 0) / 100, 2) }} RM</p>

                                <div class="mt-4">
                                    <h5 class="font-semibold">Transactions (latest)</h5>
                                    <ul class="text-sm mt-2">
                                        @forelse(($walletTransactions ?? collect([])) as $t)
                                            <li class="py-2 border-b flex justify-between">
                                                <span>{{ $t->type ?? '-' }} — {{ $t->description ?? '-' }}</span>
                                                <span>{{ number_format(($t->amount ?? 0) / 100, 2) }}</span>
                                            </li>
                                        @empty
                                            <li class="text-sm text-gray-500">No transactions.</li>
                                        @endforelse
                                    </ul>
                                </div>
                            </div>

                            <div class="p-4 border rounded-lg">
                                <h4 class="font-semibold mb-2">Payments (latest)</h4>
                                <ul class="text-sm">
                                    @forelse(($payments ?? collect([])) as $p)
                                        <li class="py-2 border-b">
                                            <div class="flex justify-between">
                                                <div>
                                                    <div class="font-medium">{{ $p->provider ?? '-' }} — {{ $p->status ?? '-' }}</div>
                                                    <div class="text-xs text-gray-500">{{ $p->created_at?->format('d M Y H:i') ?? '-' }}</div>
                                                </div>
                                                <div class="text-sm">{{ number_format(($p->amount ?? 0) / 100, 2) }}</div>
                                            </div>
                                        </li>
                                    @empty
                                        <li class="text-sm text-gray-500">No payments found.</li>
                                    @endforelse
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div data-panel="collection" class="tab-panel hidden">
    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="mb-4 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded">
            {{ session('error') }}
        </div>
    @endif

    <!-- Collections Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-8">
        <div class="px-6 py-4 bg-gradient-to-r from-indigo-600 to-purple-600 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-white">Collection Balances</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Balance</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pending</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Limit</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Redeemed</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($user->collections as $collection)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800">
                                {{ ucfirst(str_replace('_', ' ', $collection->type)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                            RM {{ number_format($collection->balance / 100, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            RM {{ number_format($collection->pending_balance / 100, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ $collection->limit ? 'RM ' . number_format($collection->limit / 100, 2) : 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($collection->is_redeemed)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Yes
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    No
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @if(!$collection->is_redeemed)
                                <form action="{{ route('admin.users.collection.redeem', [$user->id, $collection->type]) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition">
                                        Redeem
                                    </button>
                                </form>
                            @else
                                <button disabled class="inline-flex items-center px-4 py-2 bg-gray-300 text-gray-500 text-sm font-medium rounded-md cursor-not-allowed">
                                    Redeemed
                                </button>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Transaction History Section -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 bg-gradient-to-r from-purple-600 to-pink-600 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-white">Transaction History</h3>
            <button onclick="openTransactionModal()" class="inline-flex items-center px-4 py-2 bg-white text-purple-600 text-sm font-medium rounded-md hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Add Transaction
            </button>
        </div>
        
        <div class="overflow-x-auto">
            @php
               $allTransactions = $user->collections->flatMap(function($collection) {
    return $collection->transactions;
})->sortByDesc(function($transaction) {
    return $transaction->transaction_date ?? $transaction->created_at;
});
            @endphp

            @if($allTransactions->count() > 0)
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Collection</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Slip</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created By</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($allTransactions as $transaction)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $transaction->transaction_date ? $transaction->transaction_date->format('d M Y, h:i A') : $transaction->created_at->format('d M Y, h:i A') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    {{ ucfirst(str_replace('_', ' ', $transaction->collection->type)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($transaction->type === 'credit')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v3.586L7.707 9.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 10.586V7z" clip-rule="evenodd"/>
                                        </svg>
                                        Credit
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v3.586l-1.293-1.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 10.586V7z" clip-rule="evenodd"/>
                                        </svg>
                                        Debit
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold {{ $transaction->type === 'credit' ? 'text-green-600' : 'text-red-600' }}">
                                {{ $transaction->type === 'credit' ? '+' : '-' }} RM {{ number_format($transaction->amount / 100, 2) }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 max-w-xs truncate">
                                {{ $transaction->description ?: 'N/A' }}
                                @if($transaction->admin_notes)
                                    <span class="block text-xs text-gray-400 italic mt-1">{{ $transaction->admin_notes }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @if($transaction->slip_path)
                                    <a href="{{ Storage::url($transaction->slip_path) }}" target="_blank" class="text-indigo-600 hover:text-indigo-900 font-medium">
                                        View Slip
                                    </a>
                                @else
                                    <span class="text-gray-400">No slip</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ $transaction->creator->name ?? 'System' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <form action="{{ route('admin.collection-transactions.destroy', $transaction->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this transaction {{$transaction->id}}? This will reverse the amount.')">
                                    @csrf
                                    <button type="submit" class="text-red-600 hover:text-red-900 font-medium">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="px-6 py-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No transactions</h3>
                    <p class="mt-1 text-sm text-gray-500">Get started by adding a new transaction.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Transaction Modal -->
<div id="transactionModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-75 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-2xl rounded-lg bg-white">
        <div class="flex justify-between items-center pb-4 border-b">
            <h3 class="text-2xl font-bold text-gray-900">Add New Transaction</h3>
            <button onclick="closeTransactionModal()" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form action="{{ route('admin.collection-transactions.store', $user->id) }}" method="POST" enctype="multipart/form-data" class="mt-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Collection Type -->
                <div>
                    <label for="collection_type" class="block text-sm font-medium text-gray-700 mb-2">Collection Type *</label>
                    <select name="collection_type" id="collection_type" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                        <option value="">Select Collection</option>
                        <option value="geran_asas">Geran Asas</option>
                        <option value="tabung_usahawan">Tabung Usahawan</option>
                        <option value="had_pembiayaan">Had Pembiayaan</option>
                    </select>
                    @error('collection_type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Transaction Type -->
                <div>
                    <label for="transaction_type" class="block text-sm font-medium text-gray-700 mb-2">Transaction Type *</label>
                    <select name="transaction_type" id="transaction_type" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                        <option value="">Select Type</option>
                        <option value="credit">Credit (Add)</option>
                        <option value="debit">Debit (Deduct)</option>
                    </select>
                    @error('transaction_type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Amount -->
                <div>
                    <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">Amount (RM) *</label>
                    <input type="number" name="amount" id="amount" step="0.01" min="0.01" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition" placeholder="0.00">
                    @error('amount')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Transaction Date -->
                <div>
                    <label for="transaction_date" class="block text-sm font-medium text-gray-700 mb-2">Transaction Date *</label>
                    <input type="datetime-local" name="transaction_date" id="transaction_date" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                    @error('transaction_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Description -->
            <div class="mt-6">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea name="description" id="description" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition" placeholder="Enter transaction description..."></textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Admin Notes -->
            <div class="mt-6">
                <label for="admin_notes" class="block text-sm font-medium text-gray-700 mb-2">Admin Notes (Internal)</label>
                <textarea name="admin_notes" id="admin_notes" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition" placeholder="Internal notes for admin reference..."></textarea>
                @error('admin_notes')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- File Upload -->
            <div class="mt-6">
                <label for="slip" class="block text-sm font-medium text-gray-700 mb-2">Upload Slip/Receipt</label>
                <div class="flex items-center justify-center w-full">
                    <label for="slip" class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition">
                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                            <svg class="w-10 h-10 mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                            </svg>
                            <p class="mb-2 text-sm text-gray-500"><span class="font-semibold">Click to upload</span> or drag and drop</p>
                            <p class="text-xs text-gray-500">PNG, JPG, PDF (MAX. 5MB)</p>
                        </div>
                        <input id="slip" name="slip" type="file" class="hidden" accept=".jpg,.jpeg,.png,.pdf"/>
                    </label>
                </div>
                @error('slip')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Form Actions -->
            <div class="mt-8 flex justify-end space-x-3 pt-6 border-t">
                <button type="button" onclick="closeTransactionModal()" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-medium transition">
                    Cancel
                </button>
                <button type="submit" class="px-6 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-lg hover:from-indigo-700 hover:to-purple-700 font-medium transition shadow-md">
                    Add Transaction
                </button>
            </div>
        </form>
    </div>
</div>

                    {{-- REFERRAL TREES --}}
                    {{-- REFERRAL TREES --}}
                    <div data-panel="referrals" class="tab-panel hidden">
                        <h4 class="font-semibold mb-3">Referral Tree (registered referrals)</h4>
                    
                        <div id="referral-tree"
                             class="p-4 border rounded min-h-[160px] text-sm text-gray-700 
                                    overflow-auto max-h-[500px] w-full"
                             style="white-space: nowrap;">
                        </div>
                    
                        <p class="text-xs text-gray-500 mt-2">Top 10 under this user are shown by default.</p>
                        <button id="expand-referral"
                                class="mt-3 px-3 py-1 bg-indigo-600 text-white rounded">
                            Load full tree
                        </button>
                    </div>


                    <div data-panel="sandbox" class="tab-panel hidden">
    <h4 class="font-semibold mb-3">Sandbox Referral Tree</h4>
    <div id="sandbox-tree" 
         class="p-4 border rounded min-h-[160px] text-sm text-gray-700 overflow-auto max-h-[500px] w-full"
         style="white-space: nowrap;">
    </div>
    
    <div class="mt-3 flex gap-2">
        <button id="expand-sandbox" 
                class="px-3 py-1 bg-indigo-600 text-white rounded hover:bg-indigo-700">
            Load full sandbox tree
        </button>
        
        <button id="sync-sandbox" 
                class="px-3 py-1 bg-green-600 text-white rounded hover:bg-green-700">
            Sync Rewards
        </button>
    </div>
    
    <div id="sync-message" class="mt-2 text-sm hidden"></div>
</div>

                    {{-- EDIT / AUDIT --}}
                    <div data-panel="edit" class="tab-panel hidden">
                        <h4 class="font-semibold mb-3">Edit User</h4>
                        <form id="edit-user-form" method="POST" action="{{ route('admin.users.store') }}">
                            <p class="text-sm text-gray-500">For editing profile, businesses and other fields, go to the edit page.</p>
                            <a href="{{ route('admin.users.edit', $user) ?? '#' }}" class="mt-3 inline-block px-3 py-2 bg-indigo-600 text-white rounded">Go to Edit</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const editBtn = document.getElementById('editProfileBtn');
    const displayDiv = document.getElementById('profileDisplay');
    const form = document.getElementById('editProfileForm');
    const cancelBtn = document.getElementById('cancelEditProfileBtn');

    let locationData = {};
    const currentCountry = "{{ $user->profile?->country ?? '' }}";
    const currentState = "{{ $user->profile?->state ?? '' }}";
    const currentCity = "{{ $user->profile?->city ?? '' }}";

    // Load location data
    $.getJSON("{{ asset('select.json') }}", function(response) {
        locationData = response;
        
        // Populate countries
        $.each(locationData, function(country) {
            const selected = country === currentCountry ? 'selected' : '';
            $("#input-country").append(`<option value="${country}" ${selected}>${country}</option>`);
        });

        // If Malaysia is selected, show states
        if (currentCountry === "Malaysia") {
            $("#state-wrapper-edit").removeClass("hidden");
            const states = locationData["Malaysia"] || {};
            $.each(states, function(state) {
                const selected = state === currentState ? 'selected' : '';
                $("#input-state").append(`<option value="${state}" ${selected}>${state}</option>`);
            });

            // If state is selected, show cities
            if (currentState) {
                const cities = locationData["Malaysia"][currentState] || [];
                if (cities.length > 0) {
                    $("#city-wrapper-edit").removeClass("hidden");
                    $.each(cities, function(i, city) {
                        const selected = city === currentCity ? 'selected' : '';
                        $("#input-city").append(`<option value="${city}" ${selected}>${city}</option>`);
                    });
                }
            }
        }
    });

    // Country change handler
    $("#input-country").on("change", function() {
        let country = $(this).val();
        let states = locationData[country] || {};

        $("#input-state").empty().append(new Option("-- Select State --", ""));
        $("#input-city").empty().append(new Option("-- Select City --", ""));
        $("#city-wrapper-edit").addClass("hidden");

        if (country === "Malaysia") {
            $("#state-wrapper-edit").removeClass("hidden");
            $.each(states, function(state) {
                $("#input-state").append(new Option(state, state));
            });
        } else {
            $("#state-wrapper-edit").addClass("hidden");
            $("#city-wrapper-edit").addClass("hidden");
        }
    });

    // State change handler
    $("#input-state").on("change", function() {
        let country = $("#input-country").val();
        let state = $(this).val();
        let cities = locationData[country][state] || [];

        $("#input-city").empty().append(new Option("-- Select City --", ""));

        if (cities.length > 0) {
            $("#city-wrapper-edit").removeClass("hidden");
            $.each(cities, function(i, city) {
                $("#input-city").append(new Option(city, city));
            });
        } else {
            $("#city-wrapper-edit").addClass("hidden");
        }
    });

    // Edit button
    editBtn.addEventListener('click', () => {
        displayDiv.classList.add('hidden');
        form.classList.remove('hidden');
    });

    // Cancel button
    cancelBtn.addEventListener('click', () => {
        form.classList.add('hidden');
        displayDiv.classList.remove('hidden');
    });

    // Form submission
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const submitBtn = this.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.textContent = 'Saving...';

        fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                // Update display values
                document.getElementById('display-full_name').textContent = data.profile.full_name || '-';
                document.getElementById('display-nric').textContent = data.profile.nric || '-';
                document.getElementById('display-dob').textContent = data.profile.dob || '-';
                document.getElementById('display-phone').textContent = data.profile.phone || '-';
                document.getElementById('display-home_address').innerHTML = (data.profile.home_address || '-').replace(/\n/g, '<br>');
                document.getElementById('display-country').textContent = data.profile.country || '-';
                document.getElementById('display-state').textContent = data.profile.state || '-';
                document.getElementById('display-city').textContent = data.profile.city || '-';

                // Switch back to display mode
                form.classList.add('hidden');
                displayDiv.classList.remove('hidden');

                // Show success message
                const alert = document.createElement('div');
                alert.className = 'fixed top-5 right-5 bg-green-500 text-white px-4 py-2 rounded shadow-lg z-50';
                alert.textContent = 'Profile updated successfully!';
                document.body.appendChild(alert);
                setTimeout(() => alert.remove(), 3000);
            } else {
                alert(data.message || 'Failed to update profile');
            }
        })
        .catch(err => {
            console.error(err);
            alert('An error occurred while updating the profile');
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.textContent = '✅ Save';
        });
    });
});
</script>
<script>
    // tab behavior
    document.querySelectorAll('[data-tab]').forEach(btn=>{
        btn.addEventListener('click', () => {
            document.querySelectorAll('[data-tab]').forEach(b=>b.classList.remove('border-indigo-600','text-indigo-600'));
            btn.classList.add('border-indigo-600','text-indigo-600');

            const tab = btn.getAttribute('data-tab');
            document.querySelectorAll('.tab-panel').forEach(panel => panel.classList.add('hidden'));
            document.querySelector('[data-panel="'+tab+'"]').classList.remove('hidden');
        });
    });
    // open first tab
    document.querySelector('[data-tab="overview"]').click();

    // toggle admin via AJAX
    document.getElementById('toggleAdminBtn').addEventListener('click', function(){
        const btn = this;
        btn.disabled = true;
        fetch("{{ route('admin.users.toggleAdminAjax', $user) }}", {
            method: 'POST',
            headers: {'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},
            body: JSON.stringify({})
        }).then(r=>r.json()).then(resp=>{
            if(resp.ok){
                if(resp.is_admin){
                    btn.textContent = 'Remove Admin';
                    btn.classList.remove('bg-indigo-600');
                    btn.classList.add('bg-red-500');
                } else {
                    btn.textContent = 'Make Admin';
                    btn.classList.remove('bg-red-500');
                    btn.classList.add('bg-indigo-600');
                }
            } else {
                alert('Failed');
            }
        }).finally(()=>btn.disabled = false);
    });

    // account toggle & serial edit
    document.querySelectorAll('.toggle-account-btn').forEach(b=>{
        b.addEventListener('click', function(){
            const accId = this.dataset.accountId;
            const url = "{{ url('admin/users') }}/{{ $user->id }}/account/"+accId+"/toggle-active";
            const btn = this;
            const card = btn.closest('.border.rounded-lg');
            btn.disabled = true;
            btn.textContent = 'Processing...';
            fetch(url, {method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}'}})
                .then(r=>r.json()).then(data=>{
                    if(data.ok){
                        const statusEl = document.getElementById('status-'+accId);
                        const serialEl = document.getElementById('serial-'+accId);
                        const expiresEl = document.getElementById('expires-'+accId);
                        const expiresRow = document.getElementById('expires-row-'+accId);

                        statusEl.textContent = data.active ? 'Active' : 'Inactive';
                        btn.textContent = data.active ? 'Deactivate' : 'Activate';

                        // Update button styling based on status
                        if(data.active){
                            btn.className = 'toggle-account-btn px-3 py-1.5 rounded text-sm font-medium transition-colors bg-red-100 text-red-700 hover:bg-red-200';
                            card.classList.remove('border-gray-200');
                            card.classList.add('border-green-200', 'bg-green-50');
                        } else {
                            btn.className = 'toggle-account-btn px-3 py-1.5 rounded text-sm font-medium transition-colors bg-green-600 text-white hover:bg-green-700';
                            card.classList.remove('border-green-200', 'bg-green-50');
                            card.classList.add('border-gray-200');
                        }

                        // Update status badge in header
                        const header = card.querySelector('h4');
                        const badge = header.querySelector('span');
                        if(badge){
                            if(data.active){
                                badge.className = 'inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800';
                                badge.textContent = 'Active';
                            } else {
                                badge.className = 'inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600';
                                badge.textContent = 'Inactive';
                            }
                        }

                        // Update serial number if returned
                        if(data.serial && serialEl){
                            serialEl.textContent = data.serial;
                            serialEl.classList.remove('text-gray-400');
                            serialEl.classList.add('text-indigo-600');
                            // Also update the input field if exists
                            const serialInput = document.getElementById('serial-input-'+accId);
                            if(serialInput) serialInput.value = data.serial;
                        }

                        // Update expiry date if returned
                        if(data.expires_at && expiresEl){
                            expiresEl.textContent = data.expires_at;
                            if(expiresRow) expiresRow.style.display = '';
                        }

                        // Show success toast
                        const toast = document.createElement('div');
                        toast.className = 'fixed top-5 right-5 bg-green-500 text-white px-4 py-2 rounded shadow-lg z-50';
                        toast.textContent = data.active ? 'Account activated successfully!' : 'Account deactivated';
                        document.body.appendChild(toast);
                        setTimeout(() => toast.remove(), 3000);
                    } else {
                        alert('Failed to toggle account status');
                    }
                }).catch(err=>{
                    console.error(err);
                    alert('Request failed');
                }).finally(()=>{
                    btn.disabled = false;
                    // Reset button text if it's still "Processing..."
                    if(btn.textContent === 'Processing...'){
                        const statusEl = document.getElementById('status-'+accId);
                        btn.textContent = statusEl.textContent === 'Active' ? 'Deactivate' : 'Activate';
                    }
                });
        });
    });

    document.querySelectorAll('.edit-serial-btn').forEach(b=>{
        b.addEventListener('click', function(){
            const id = this.dataset.accountId;
            document.getElementById('serial-edit-'+id).classList.remove('hidden');
        });
    });

    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    document.querySelectorAll('.save-serial-btn').forEach(b=>{
        b.addEventListener('click', function(){
            const id = this.dataset.accountId;
            const val = document.getElementById('serial-input-'+id).value.trim();
            const err = document.getElementById('serial-error-'+id);
            const btn = this;
            const originalText = btn.textContent;

            if(!val){
                err.textContent = 'Serial cannot be empty';
                return;
            }

            btn.disabled = true;
            btn.textContent = 'Saving...';
            err.textContent = '';

            fetch("{{ url('admin/users') }}/{{ $user->id }}/account/"+id+"/update-serial", {
                method:'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({ serial_number: val })
            }).then(r=>{
                if(!r.ok){
                    return r.json().then(data => { throw new Error(data.message || 'Server error'); });
                }
                return r.json();
            }).then(resp=>{
                if(resp.ok){
                    const serialEl = document.getElementById('serial-'+id);
                    serialEl.textContent = resp.serial;
                    serialEl.classList.remove('text-gray-400');
                    serialEl.classList.add('text-indigo-600');
                    document.getElementById('serial-edit-'+id).classList.add('hidden');
                    err.textContent = '';

                    // Show success toast
                    const toast = document.createElement('div');
                    toast.className = 'fixed top-5 right-5 bg-green-500 text-white px-4 py-2 rounded shadow-lg z-50';
                    toast.textContent = 'Serial number updated successfully!';
                    document.body.appendChild(toast);
                    setTimeout(() => toast.remove(), 3000);
                } else {
                    err.textContent = resp.error || 'Failed to update serial number';
                }
            }).catch(e=>{
                err.textContent = e.message || 'Request failed';
            }).finally(()=>{
                btn.disabled = false;
                btn.textContent = originalText;
            });
        });
    });

   function renderTreeElement(nodes, depth, max, isLast = false) {
    const ul = document.createElement('ul');
    ul.className = 'ml-4';
    nodes.slice(0, max).forEach((n, idx) => {
        const li = document.createElement('li');
        const isLastChild = idx === Math.min(nodes.length, max) - 1;
        const prefix = isLastChild ? '└── ' : '├── ';
        
        li.innerHTML = `<div class="py-1">
            <span class="text-gray-400">${prefix}</span>
            <span class="font-medium">${n.name}</span> 
            <span class="text-xs text-gray-500">(${n.user_id})</span>
        </div>`;
        
        if(n.children && n.children.length) {
            li.appendChild(renderTreeElement(n.children, depth+1, max, isLastChild));
        }
        ul.appendChild(li);
    });
    return ul;
}

function renderTree(node, depth=0, max=10){
    if(!node) return;
    const ul = document.createElement('ul');
    ul.className = 'ml-4';
    (Array.isArray(node) ? node : [node]).slice(0, max).forEach(n=>{
        const li = document.createElement('li');
        li.innerHTML = `<div class="py-1">
            <span class="font-medium">${n.name}</span> 
            <span class="text-xs text-gray-500">(${n.user_id})</span>
        </div>`;
        if(n.children && n.children.length) {
            li.appendChild(renderTreeElement(n.children, depth+1, max));
        }
        ul.appendChild(li);
    });
    return ul;
}

    // fetch referrals
    document.getElementById('expand-referral').addEventListener('click', function(){
        const c = document.getElementById('referral-tree');
        c.innerHTML = 'Loading...';
        fetch("{{ route('admin.users.referralTree', $user) }}")
            .then(r=>r.json())
            .then(d=>{
                c.innerHTML = '';
                if(!d.tree) { c.innerText = 'No referrals'; return;}
                const treeDom = renderTree(d.tree, 0, 10);
                if(treeDom) c.appendChild(treeDom);
            }).catch(()=> c.innerText = 'Failed to load');
    });

    // sandbox tree
    document.getElementById('expand-sandbox').addEventListener('click', function(){
        const c = document.getElementById('sandbox-tree');
        c.innerHTML = 'Loading...';
        fetch("{{ route('admin.users.sandboxReferralTree', $user) }}")
            .then(r=>r.json())
            .then(d=>{
                c.innerHTML = '';
                if(!d.tree) { c.innerText = 'No sandbox referrals'; return;}
                const treeDom = renderTree(d.tree, 0, 10);
                if(treeDom) c.appendChild(treeDom);
            }).catch(()=> c.innerText = 'Failed to load');
    });

    // lazy: load top-10 for referrals on panel open
    document.querySelector('[data-tab="referrals"]').addEventListener('click', ()=>document.getElementById('expand-referral').click());
    document.querySelector('[data-tab="sandbox"]').addEventListener('click', ()=>document.getElementById('expand-sandbox').click());
</script>

<!-- ADD TO BLACKLIST -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const addBtn = document.getElementById('addBlacklistBtn');
    const modal = document.getElementById('blacklistModal');
    const cancelBtn = document.getElementById('cancelBlacklist');
    const confirmBtn = document.getElementById('confirmBlacklist');

    // Open modal
    addBtn?.addEventListener('click', () => {
        modal.classList.remove('hidden');
    });

    // Cancel modal
    cancelBtn?.addEventListener('click', () => {
        modal.classList.add('hidden');
    });

    // Confirm blacklist
    confirmBtn?.addEventListener('click', function() {
        const btn = this;
        btn.disabled = true;
        btn.textContent = 'Adding...'; // loading effect

        fetch("{{ route('admin.users.addToBlacklist', $user) }}", {
            method: 'POST',
            headers: {
                'Content-Type':'application/json',
                'X-CSRF-TOKEN':'{{ csrf_token() }}'
            },
            body: JSON.stringify({})
        })
        .then(r => r.json())
        .then(resp => {
            if(resp.ok) {
                // close modal
                modal.classList.add('hidden');

                // show Tailwind alert
                const alertDiv = document.createElement('div');
                alertDiv.className = 'fixed top-5 right-5 bg-green-500 text-white px-4 py-2 rounded shadow-lg z-50';
                alertDiv.textContent = 'User added to blacklist!';
                document.body.appendChild(alertDiv);

                // fade out alert after 3s
                setTimeout(() => alertDiv.remove(), 3000);

                // reload page after short delay so badge/button updates
                setTimeout(() => location.reload(), 1000);

            } else {
                alert(resp.error || 'Failed to add to blacklist');
            }
        })
        .catch(() => alert('Request failed'))
        .finally(() => {
            btn.disabled = false;
            btn.textContent = 'Confirm';
        });
    });
});
</script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const editBtn = document.getElementById('editUserNameBtn');
    const displayName = document.getElementById('userNameDisplay');
    const form = document.getElementById('editUserNameForm');
    const cancelBtn = document.getElementById('cancelEditUserNameBtn');
    const input = document.getElementById('userNameInput');

    editBtn.addEventListener('click', () => {
        displayName.parentElement.classList.add('hidden');
        form.classList.remove('hidden');
        input.focus();
    });

    cancelBtn.addEventListener('click', () => {
        form.classList.add('hidden');
        displayName.parentElement.classList.remove('hidden');
        input.value = displayName.textContent.trim();
    });

    // Sync sandbox rewards
document.getElementById('sync-sandbox').addEventListener('click', function() {
    const btn = this;
    const msg = document.getElementById('sync-message');
    
    btn.disabled = true;
    btn.textContent = 'Syncing...';
    msg.className = 'mt-2 text-sm hidden';
    
    fetch("{{ route('admin.users.syncSandboxRewards', $user) }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(r => r.json())
    .then(d => {
        btn.disabled = false;
        btn.textContent = 'Sync Rewards';
        
        if (d.success) {
            msg.className = 'mt-2 text-sm p-3 bg-green-50 border border-green-200 rounded text-green-800';
            msg.innerHTML = `
                <strong>✓ Success:</strong> ${d.message}<br>
                <span class="text-xs">Geran Asas Balance: RM ${(d.geran_balance / 100).toFixed(2)} | 
                Pending: RM ${(d.pending_balance / 100).toFixed(2)}</span>
            `;
        } else {
            msg.className = 'mt-2 text-sm p-3 bg-red-50 border border-red-200 rounded text-red-800';
            msg.innerHTML = `<strong>✗ Error:</strong> ${d.message}`;
        }
        msg.classList.remove('hidden');
        
        // Refresh the tree to show updated data
        document.getElementById('expand-sandbox').click();
    })
    .catch(err => {
        btn.disabled = false;
        btn.textContent = 'Sync Rewards';
        msg.className = 'mt-2 text-sm p-3 bg-red-50 border border-red-200 rounded text-red-800';
        msg.textContent = 'Sync failed. Please try again.';
        msg.classList.remove('hidden');
    });
});
});
</script>
<script>
function openTransactionModal() {
    document.getElementById('transactionModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeTransactionModal() {
    document.getElementById('transactionModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// Close modal when clicking outside
document.getElementById('transactionModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeTransactionModal();
    }
});

// File upload preview
document.getElementById('slip')?.addEventListener('change', function(e) {
    const fileName = e.target.files[0]?.name;
    if (fileName) {
        const label = e.target.parentElement;
        label.querySelector('p').innerHTML = `<span class="font-semibold text-indigo-600">${fileName}</span>`;
    }
});
</script>


</x-app-layout>
