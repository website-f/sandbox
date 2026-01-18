<x-admin-layout>
    <x-slot name="pageTitle">Manage User Roles</x-slot>
    <x-slot name="breadcrumb">Manage users, assign roles, and handle referrals</x-slot>

    <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-100 dark:border-gray-700">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                <i class="fas fa-users-cog text-indigo-600 dark:text-indigo-400 mr-2"></i>
                All Users
            </h3>
            <a href="{{ route('admin.users.create') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-semibold transition-colors shadow-md">
                <i class="fas fa-user-plus mr-2"></i> Add User
            </a>
        </div>

        {{-- Status flash --}}
        @if(session('status'))
        <div class="mb-6 p-4 rounded-xl bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 font-semibold flex items-center">
            <i class="fas fa-check-circle mr-3 text-green-500"></i>
            {{ session('status') }}
        </div>
        @endif

        <div class="overflow-x-auto">
            <table id="rolesTable" class="w-full">
                <thead>
                    <tr class="text-left text-sm font-semibold text-gray-500 dark:text-gray-400 border-b border-gray-200 dark:border-gray-700">
                        <th class="pb-3 px-4">RM No</th>
                        <th class="pb-3 px-4">SB No</th>
                        <th class="pb-3 px-4">Name</th>
                        <th class="pb-3 px-4">Email</th>
                        <th class="pb-3 px-4">Phone</th>
                        <th class="pb-3 px-4">Referrer</th>
                        <th class="pb-3 px-4 no-export">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach($users as $user)
                    @php
                    $rizqmall = $user->accounts->find($user->accounts->where('type', 'rizqmall')->first()?->id);
                    $sandbox = $user->accounts->find($user->accounts->where('type', 'sandbox')->first()?->id);
                    @endphp
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <td class="py-4 px-4 text-sm">
                            @if($rizqmall && $rizqmall->active)
                            <span class="px-2 py-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 text-xs font-semibold rounded-lg">{{ $rizqmall->serial_number }}</span>
                            @else
                            <span class="px-2 py-1 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 text-xs font-semibold rounded-lg">Inactive</span>
                            @endif
                        </td>
                        <td class="py-4 px-4 text-sm">
                            @if($sandbox && $sandbox->active)
                            <span class="px-2 py-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 text-xs font-semibold rounded-lg">{{ $sandbox->serial_number }}</span>
                            @else
                            <span class="px-2 py-1 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 text-xs font-semibold rounded-lg">Inactive</span>
                            @endif
                        </td>
                        <td class="py-4 px-4">
                            <div class="font-medium text-gray-900 dark:text-white">{{ $user->profile?->full_name ?? $user->name }}</div>
                            @if($user->checkBlacklist())
                            <span class="inline-block mt-1 px-2 py-0.5 bg-red-500 text-white text-[10px] font-bold uppercase tracking-wider rounded">Blacklisted</span>
                            @endif
                        </td>
                        <td class="py-4 px-4 text-sm text-gray-600 dark:text-gray-400">{{ $user->email }}</td>
                        <td class="py-4 px-4 text-sm text-gray-600 dark:text-gray-400">{{ $user->profile->phone ?? '-' }}</td>
                        <td class="py-4 px-4">
                            <div class="flex items-center gap-2">
                                <button
                                    type="button"
                                    class="px-2 py-1 rounded-lg text-xs font-medium transition-colors border {{ $user->referral?->parent ? 'bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400 border-green-200 dark:border-green-800' : 'bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300 border-gray-200 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-600' }}"
                                    onclick="openReferralModal({{ $user->id }}, '{{ addslashes($user->name) }}')">
                                    {{ $user->referral?->parent?->name ?? 'Assign' }}
                                </button>

                                @if($user->referral?->parent)
                                <button
                                    type="button"
                                    onclick="openRemoveReferralModal({{ $user->id }}, '{{ addslashes($user->name) }}')"
                                    class="p-1 rounded-lg bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-900/40 transition-colors">
                                    <i class="fas fa-times text-xs"></i>
                                </button>
                                @endif
                            </div>
                        </td>
                        <td class="py-4 px-4">
                            <div class="flex gap-2">
                                <a href="{{ route('admin.users.show', $user) }}"
                                    class="p-2 rounded-lg bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-100 dark:hover:bg-indigo-900/40 transition-colors">
                                    <i class="fas fa-eye"></i>
                                </a>

                                <button
                                    data-user-id="{{ $user->id }}"
                                    class="delete-user-btn p-2 rounded-lg bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-900/40 transition-colors">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
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

    <!-- Modals -->
    {{-- Delete User Modal --}}
    <div id="deleteUserModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center hidden z-50 p-4">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl w-full max-w-sm p-6 transform transition-all">
            <div class="text-center mb-6">
                <div class="w-16 h-16 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-exclamation-triangle text-2xl text-red-600 dark:text-red-400"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Confirm Delete</h3>
                <p class="text-gray-500 dark:text-gray-400 text-sm">Are you sure you want to delete this user? This action cannot be undone.</p>
            </div>
            <div class="flex gap-3">
                <button id="cancelDeleteUser" class="flex-1 px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl font-medium hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">Cancel</button>
                <button id="confirmDeleteUser" class="flex-1 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-xl font-medium shadow-lg transition-colors">Delete</button>
            </div>
        </div>
    </div>

    {{-- Referral Modal --}}
    <div id="referralModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl w-full max-w-lg max-h-[80vh] flex flex-col p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white">Assign Referrer to <span id="modalUserName" class="text-indigo-600 dark:text-indigo-400"></span></h2>
                <button onclick="closeReferralModal()" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <div class="relative mb-4">
                <input type="text" id="referralSearch" placeholder="Search referrer..."
                    class="w-full pl-10 pr-4 py-3 rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                    onkeyup="filterReferralList()">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
            </div>

            <div id="referralList" class="overflow-y-auto space-y-2 flex-1 pr-2 custom-scrollbar"></div>

            <form id="assignReferralForm" method="POST" class="hidden">
                @csrf
                <input type="hidden" name="referrer_id" id="referrerId">
            </form>
        </div>
    </div>

    {{-- Remove Referral Modal --}}
    <div id="removeReferralModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl w-full max-w-sm p-6">
            <div class="text-center mb-6">
                <div class="w-16 h-16 bg-orange-100 dark:bg-orange-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-user-minus text-2xl text-orange-600 dark:text-orange-400"></i>
                </div>
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Remove Referrer</h2>
                <p class="text-gray-500 dark:text-gray-400 text-sm">
                    Are you sure you want to remove the referrer for
                    <span id="removeReferralUserName" class="font-bold text-gray-900 dark:text-white"></span>?
                </p>
            </div>

            <div class="flex justify-end gap-3">
                <button onclick="closeRemoveReferralModal()"
                    class="flex-1 px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl font-medium hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                    Cancel
                </button>
                <form id="removeReferralForm" method="POST" class="flex-1">
                    @csrf
                    <button type="submit"
                        class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-xl font-medium shadow-lg transition-colors">
                        Remove
                    </button>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        $(document).ready(function() {
            // Initialize DataTable
            $('#rolesTable').DataTable({
                responsive: true,
                order: [
                    [2, 'asc']
                ], // Sort by Name
                columnDefs: [{
                        targets: 'no-export',
                        exportable: false
                    },
                    {
                        targets: [6],
                        orderable: false
                    } // Disable sort on Action column
                ]
            });
        });

        // Delete User Logic
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
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

            fetch(`/admin/users/${deleteUserId}/delete`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                }).then(res => res.json())
                .then(resp => {
                    if (resp.ok) {
                        window.location.reload();
                    } else {
                        alert(resp.error || 'Failed to delete user.');
                        this.disabled = false;
                        this.textContent = 'Delete';
                    }
                }).catch(() => {
                    this.disabled = false;
                    this.textContent = 'Delete';
                });
        });

        // Referral Logic
        let selectedUserId = null;
        let removeReferralUserId = null;

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
            const container = document.getElementById("referralList");
            container.innerHTML = '<div class="text-center py-4"><i class="fas fa-spinner fa-spin text-indigo-500"></i></div>';

            fetch(`{{ route('admin.users.referralList') }}?search=${encodeURIComponent(query)}&page=${page}`)
                .then(res => res.text())
                .then(html => {
                    container.innerHTML = html;
                    // Rebind pagination links
                    container.querySelectorAll(".pagination a").forEach(link => {
                        link.addEventListener("click", function(e) {
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
    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 2px;
        }

        .dark .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #475569;
        }
    </style>
    @endpush
</x-admin-layout>