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
                                <h4 class="font-semibold mb-2">Profile</h4>
                                <dl class="text-sm text-gray-700">
                                    <div><span class="font-medium">Full name:</span> {{ $user->profile?->full_name ?? '-' }}</div>
                                    <div><span class="font-medium">NRIC:</span> {{ $user->profile?->nric ?? '-' }}</div>
                                    <div><span class="font-medium">DOB:</span> {{ $user->profile?->dob ?? '-' }}</div>
                                    <div><span class="font-medium">Address:</span> {!! nl2br(e($user->profile?->home_address ?? '-')) !!}</div>
                                     <div><span class="font-medium">Country:</span> {{ $user->profile?->country ?? '-' }}</div>
                                      <div><span class="font-medium">State:</span> {{ $user->profile?->state ?? '-' }}</div>
                                       <div><span class="font-medium">City:</span> {{ $user->profile?->city ?? '-' }}</div>
                                </dl>
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
                                <div class="p-4 border rounded-lg">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h4 class="font-semibold capitalize">{{ $account->type ?? '-' }} account</h4>
                                            <p class="text-sm text-gray-600">Serial: <span id="serial-{{ $account->id }}">{{ $account->serial_number ?? '-' }}</span></p>
                                            <p class="text-sm text-gray-600">Status: <span id="status-{{ $account->id }}">{{ $account->active ? 'Active' : 'Inactive' }}</span></p>
                                            @if($account->expires_at)
                                                <p class="text-sm text-gray-600">Expires: {{ $account->expires_at?->format('d M Y') ?? '-' }}</p>
                                            @endif
                                        </div>

                                        <div class="space-y-2 text-right">
                                            <button data-account-id="{{ $account->id }}" class="toggle-account-btn px-3 py-1 rounded bg-gray-100 text-sm">
                                                {{ $account->active ? 'Deactivate' : 'Activate' }}
                                            </button>

                                            <button data-account-id="{{ $account->id }}" class="edit-serial-btn px-3 py-1 rounded bg-indigo-600 text-white text-sm">
                                                Edit Serial
                                            </button>
                                        </div>
                                    </div>

                                    {{-- inline serial edit form (hidden) --}}
                                    <div id="serial-edit-{{ $account->id }}" class="mt-3 hidden">
                                        <div class="flex items-center gap-2">
                                            <input type="text" id="serial-input-{{ $account->id }}" class="border rounded p-2 flex-1" value="{{ $account->serial_number ?? '' }}">
                                            <button data-account-id="{{ $account->id }}" class="save-serial-btn px-3 py-2 bg-green-600 text-white rounded">Save</button>
                                            <button onclick="document.getElementById('serial-edit-{{ $account->id }}').classList.add('hidden')" class="px-3 py-2 bg-gray-200 rounded">Cancel</button>
                                        </div>
                                        <div id="serial-error-{{ $account->id }}" class="text-red-500 text-sm mt-1"></div>
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
                        <table class="min-w-full border border-gray-300 rounded">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="p-2 border">Type</th>
                                        <th class="p-2 border">Balance (RM)</th>
                                        <th class="p-2 border">Pending (RM)</th>
                                        <th class="p-2 border">Limit (RM)</th>
                                        <th class="p-2 border">Redeemed</th>
                                        <th class="p-2 border">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($user->collections as $collection)
                                    <tr class="text-center">
                                        <td class="p-2 border">{{ ucfirst(str_replace('_', ' ', $collection->type)) }}</td>
                                        <td class="p-2 border">{{ number_format($collection->balance / 100, 2) }}</td>
                                        <td class="p-2 border">{{ number_format($collection->pending_balance / 100, 2) }}</td>
                                        <td class="p-2 border">{{ number_format($collection->limit / 100, 2) }}</td>
                                        <td class="p-2 border">
                                            @if($collection->is_redeemed)
                                                <span class="text-green-600 font-semibold">Yes</span>
                                            @else
                                                <span class="text-red-600 font-semibold">No</span>
                                            @endif
                                        </td>
                                        <td class="p-2 border">
                                            @if(!$collection->is_redeemed)
                                                <form action="{{ route('admin.users.collection.redeem', [$user->id, $collection->type]) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="px-3 py-1 bg-indigo-600 text-white rounded hover:bg-blue-700">
                                                        Redeem
                                                    </button>
                                                </form>
                                            @else
                                                <button disabled class="px-3 py-1 bg-gray-400 text-black rounded">Redeem</button>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
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
            btn.disabled = true;
            fetch(url, {method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}'}})
                .then(r=>r.json()).then(data=>{
                    if(data.ok){
                        const statusEl = document.getElementById('status-'+accId);
                        statusEl.textContent = data.active ? 'Active' : 'Inactive';
                        btn.textContent = data.active ? 'Deactivate' : 'Activate';
                    }
                }).finally(()=>btn.disabled=false);
        });
    });

    document.querySelectorAll('.edit-serial-btn').forEach(b=>{
        b.addEventListener('click', function(){
            const id = this.dataset.accountId;
            document.getElementById('serial-edit-'+id).classList.remove('hidden');
        });
    });

    document.querySelectorAll('.save-serial-btn').forEach(b=>{
        b.addEventListener('click', function(){
            const id = this.dataset.accountId;
            const val = document.getElementById('serial-input-'+id).value.trim();
            const err = document.getElementById('serial-error-'+id);
            if(!val){ err.textContent = 'Serial cannot be empty'; return; }
            b.disabled = true;
            fetch("{{ url('admin/users') }}/{{ $user->id }}/account/"+id+"/update-serial", {
                method:'POST',
                headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},
                body: JSON.stringify({ serial_number: val })
            }).then(r=>r.json()).then(resp=>{
                if(resp.ok){
                    document.getElementById('serial-'+id).textContent = resp.serial;
                    document.getElementById('serial-edit-'+id).classList.add('hidden');
                    err.textContent = '';
                } else {
                    err.textContent = resp.error || 'Failed to update';
                }
            }).catch(()=>err.textContent = 'Request failed').finally(()=>b.disabled=false);
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


</x-app-layout>
