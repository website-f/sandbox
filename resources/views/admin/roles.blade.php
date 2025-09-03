<x-app-layout>
    <x-slot name="header">
        <h2 class="font-extrabold text-2xl text-gray-900">
            Manage User Roles
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto bg-white shadow rounded-2xl p-6">
            
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

            {{-- Users table --}}
            <table class="w-full border border-gray-200 rounded-xl overflow-hidden">
                <thead class="bg-gray-50 text-left text-sm font-semibold text-gray-600">
                    <tr>
                        <th class="px-4 py-3">Name</th>
                        <th class="px-4 py-3">Email</th>
                        <th class="px-4 py-3">Role</th>
                        <th class="px-4 py-3">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($users as $user)
                        <tr>
                            <td class="px-4 py-3">{{ $user->name }}</td>
                            <td class="px-4 py-3">{{ $user->email }}</td>
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
                            <td colspan="4" class="px-4 py-6 text-center text-gray-500">No users found.</td>
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
</x-app-layout>
