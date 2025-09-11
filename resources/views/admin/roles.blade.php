<x-app-layout>
    <x-slot name="header">
        <h2 class="font-extrabold text-2xl text-gray-900">
            Manage User Roles
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto bg-white shadow rounded-2xl p-6 overflow-y-auto">
            
            {{-- Status flash --}}
            @if(session('status'))
                <div class="mb-4 p-3 rounded bg-green-100 text-green-700">
                    {{ session('status') }}
                </div>
            @endif

            {{-- Search form --}}
            <form method="GET" action="{{ route('admin.users.index') }}" class="mb-6 flex">
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search by name or email..."
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
                        <th class="px-4 py-3">Role</th>
                        <th class="px-4 py-3">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($users as $user)
                        @php
                            $rizqmall = $user->accounts->firstWhere('type', 'rizqmall');
                            $sandbox  = $user->accounts->firstWhere('type', 'sandbox');
                        @endphp
                        <tr>
                            <td class="px-4 py-3 text-sm">
                                @if($rizqmall && $rizqmall->active)
                                    <span class="text-green-600 font-semibold">{{ $rizqmall->serial_number }}</span>
                                @else
                                    <span class="text-red-600 font-semibold italic">Inactive</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm">
                                @if($sandbox && $sandbox->active)
                                    <span class="text-green-600 font-semibold">{{ $sandbox->serial_number }}</span>
                                @else
                                    <span class="text-red-600 font-semibold italic">Inactive</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">{{ $user->name }}</td>
                            <td class="px-4 py-3">{{ $user->email }}</td>
                            <td class="px-4 py-3">{{ $user->profile->phone ?? '-' }}</td>
                            <td class="px-4 py-3">
    <div class="inline-flex rounded-md shadow-sm">
        {{-- Main button (assign or show referrer) --}}
        <button 
            type="button" 
            class="px-2 py-1 rounded-l-md {{ $user->referral?->parent ? 'bg-green-500 hover:bg-green-600' : 'bg-indigo-600 hover:bg-indigo-700' }} text-sm text-white font-medium"
            onclick="openReferralModal({{ $user->id }}, '{{ $user->name }}')">
            {{ $user->referral?->parent?->name ?? 'Assign Referrer' }}
        </button>

        {{-- X button (remove referrer) --}}
        @if($user->referral?->parent)
            <button 
                type="button"
                onclick="openRemoveReferralModal({{ $user->id }}, '{{ $user->name }}')"
                class="px-2 py-1 rounded-r-md bg-red-500 hover:bg-red-600 text-sm text-white font-bold">
                ×
            </button>
        @endif
    </div>
</td>



            
                            <td class="px-4 py-3">
                                @if($user->hasRole('Admin'))
                                    <span class="px-2 py-1 rounded-full bg-indigo-100 text-indigo-700 text-xs font-medium">Admin</span>
                                @else
                                    <span class="px-2 py-1 rounded-full bg-gray-100 text-gray-600 text-xs font-medium">User</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <form method="POST" action="{{ route('admin.users.toggleAdmin', $user) }}">
                                    @csrf
                                    <button class="px-3 py-1 rounded-lg text-sm font-semibold text-white 
                                        {{ $user->hasRole('Admin') ? 'bg-red-500 hover:bg-red-600' : 'bg-indigo-600 hover:bg-indigo-700' }}">
                                        {{ $user->hasRole('Admin') ? 'Remove Admin' : 'Make Admin' }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-6 text-center text-gray-500">No users found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>


            {{-- Pagination --}}
            <div class="mt-6">
                {{ $users->links() }}
            </div>
        </div>
    </div>

    <div id="referralModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-800 bg-opacity-50">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-xxl max-h-[80vh] flex flex-col p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold">Assign Referrer to <span id="modalUserName"></span></h2>
                <button onclick="closeReferralModal()" class="text-gray-500 hover:text-gray-700">&times;</button>
            </div>
    
            {{-- Search bar --}}
            <input type="text" id="referralSearch" placeholder="Search user..." 
                   class="w-full px-3 py-2 border rounded-lg mb-4" 
                   onkeyup="filterReferralList()">
    
            {{-- User list container with fixed height and overflow --}}
            <div id="referralList" class="overflow-y-auto space-y-2 max-h-72"></div>
    
            {{-- Hidden form --}}
            <form id="assignReferralForm" method="POST" class="hidden">
                @csrf
                <input type="hidden" name="referrer_id" id="referrerId">
            </form>
        </div>
    </div>

    <div id="removeReferralModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-800 bg-opacity-50">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-md p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Remove Referrer</h2>
            <p class="text-gray-600 mb-6">
                Are you sure you want to remove the referrer for 
                <span id="removeReferralUserName" class="font-semibold text-gray-900"></span>?
            </p>
    
            <div class="flex justify-end space-x-3">
                <button onclick="closeRemoveReferralModal()"
                    class="px-4 py-2 rounded-lg bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium">
                    Cancel
                </button>
                <form id="removeReferralForm" method="POST">
                    @csrf
                    <button type="submit" 
                        class="px-4 py-2 rounded-lg bg-red-500 hover:bg-red-600 text-white font-medium">
                        Remove
                    </button>
                </form>
            </div>
        </div>
    </div>

<script>
let removeReferralUserId = null;

function openRemoveReferralModal(userId, userName) {
    removeReferralUserId = userId;
    document.getElementById("removeReferralUserName").innerText = userName;

    const form = document.getElementById("removeReferralForm");
    form.action = `/admin/users/${userId}/remove-referral`;

    document.getElementById("removeReferralModal").classList.remove("hidden");
}

function closeRemoveReferralModal() {
    document.getElementById("removeReferralModal").classList.add("hidden");
}
</script>


    <script>
function openReferralModal(userId, userName) {
    selectedUserId = userId;
    document.getElementById("modalUserName").innerText = userName;
    document.getElementById("referralModal").classList.remove("hidden");

    loadReferralList();
}

function loadReferralList(query = '', page = 1) {
    fetch(`{{ route('admin.users.referralList') }}?search=${encodeURIComponent(query)}&page=${page}`)
        .then(res => res.text())
        .then(html => {
            const container = document.getElementById("referralList");
            container.innerHTML = html;

            // Rebind pagination links so they load via AJAX
            container.querySelectorAll(".pagination a").forEach(link => {
                link.addEventListener("click", function (e) {
                    e.preventDefault();
                    const url = new URL(this.href);
                    const page = url.searchParams.get("page") || 1;
                    loadReferralList(document.getElementById("referralSearch").value, page);
                });
            });
        });
}



function filterReferralList() {
    let q = document.getElementById("referralSearch").value;
    loadReferralList(q);
}

function assignReferrer(referrerId) {
    const form = document.getElementById("assignReferralForm");
    form.action = `/admin/users/${selectedUserId}/assign-referral`;
    document.getElementById("referrerId").value = referrerId;
    form.submit();
}
</script>



</x-app-layout>
