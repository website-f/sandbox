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

            <a href="{{ route('admin.users.create') }}"
               class="ml-4 px-4 text-white py-2 rounded-lg shadow" style="background-color: #0d6efd">
               + Add User
            </a>

            <table class="w-full border border-gray-200 rounded-xl mt-4 overflow-hidden">
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
                            <td class="px-4 py-3">
                                {{ $user->profile?->full_name ?? $user->name }} <br>
                                @if($user->checkBlacklist())
                                <span class="px-2 py-1 bg-red-500 text-white text-xs rounded">Blacklisted</span>
                                @endif
                            </td>

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
                            <td class="px-4 py-3 flex gap-2">
                                <a href="{{ route('admin.users.show', $user) }}"
                                   class="px-3 py-1 rounded-lg text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700">
                                   View
                                </a>
                            
                                <button 
                                    data-user-id="{{ $user->id }}" 
                                    class="delete-user-btn px-3 py-1 rounded-lg text-sm font-semibold text-white bg-red-600 hover:bg-red-700">
                                    Delete
                                </button>
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

    <!-- Delete User Modal -->
    <div id="deleteUserModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-lg p-6 w-96">
            <h3 class="text-lg font-semibold mb-4">Confirm Delete</h3>
            <p>Are you sure you want to delete this user? This action cannot be undone.</p>
            <div class="mt-4 flex justify-end gap-2">
                <button id="cancelDeleteUser" class="px-3 py-2 bg-gray-200 rounded">Cancel</button>
                <button id="confirmDeleteUser" class="px-3 py-2 bg-red-500 text-white rounded">Delete</button>
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
        let deleteUserId = null;

    document.querySelectorAll('.delete-user-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            deleteUserId = this.dataset.userId;
            document.getElementById('deleteUserModal').classList.remove('hidden');
        });
    });
    
    document.getElementById('cancelDeleteUser').addEventListener('click', function() {
        deleteUserId = null;
        document.getElementById('deleteUserModal').classList.add('hidden');
    });
    
    document.getElementById('confirmDeleteUser').addEventListener('click', function() {
        if (!deleteUserId) return;
        this.disabled = true;
        fetch(`/admin/users/${deleteUserId}/delete`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
        }).then(res => res.json())
          .then(resp => {
              if(resp.ok){
                  const alertDiv = document.createElement('div');
                  alertDiv.className = 'fixed top-5 right-5 bg-green-500 text-white px-4 py-2 rounded shadow-lg z-50';
                  alertDiv.textContent = 'User deleted successfully!';
                  document.body.appendChild(alertDiv);
                  location.reload();
              } else {
                  alert(resp.error || 'Failed to delete user.');
              }
          }).finally(() => this.disabled = false);
    });
    
    </script>
    
    
    <script>
        function openReferralModal(userId, userName) {
            selectedUserId = userId;
            document.getElementById("modalUserName").innerText = userName;
            document.getElementById("referralModal").classList.remove("hidden");
        
            loadReferralList();
        }

        function closeReferralModal() {

            document.getElementById("referralModal").classList.add("hidden");
        
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
