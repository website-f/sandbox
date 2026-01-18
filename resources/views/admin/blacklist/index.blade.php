<x-admin-layout>
    <x-slot name="pageTitle">Blacklisted Users</x-slot>
    <x-slot name="breadcrumb">Manage blacklisted accounts</x-slot>

    <div class="bg-white dark:bg-gray-800 rounded-2xl p-4 sm:p-6 border border-gray-100 dark:border-gray-700">
        <div class="flex justify-between items-center mb-4 sm:mb-6">
            <h3 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white">
                <i class="fas fa-ban text-red-500 mr-2"></i>
                Blacklist Entries
            </h3>
        </div>

        <div class="overflow-x-auto" style="-webkit-overflow-scrolling: touch;">
            <table id="blacklistTable" class="w-full min-w-[600px]">
                <thead>
                    <tr class="text-left text-xs sm:text-sm font-semibold text-gray-500 dark:text-gray-400 border-b border-gray-200 dark:border-gray-700">
                        <th class="pb-3 px-3 sm:px-4">Name</th>
                        <th class="pb-3 px-3 sm:px-4 hidden sm:table-cell">Email</th>
                        <th class="pb-3 px-3 sm:px-4 hidden md:table-cell">Phone</th>
                        <th class="pb-3 px-3 sm:px-4">Reason</th>
                        <th class="pb-3 px-3 sm:px-4 hidden sm:table-cell">Date Added</th>
                        <th class="pb-3 px-3 sm:px-4">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($blacklists as $entry)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <td class="py-3 sm:py-4 px-3 sm:px-4 font-medium text-gray-900 dark:text-white text-xs sm:text-sm">{{ $entry->name }}</td>
                        <td class="py-3 sm:py-4 px-3 sm:px-4 text-xs sm:text-sm text-gray-600 dark:text-gray-400 hidden sm:table-cell">
                            <span class="truncate block max-w-[150px]">{{ $entry->email }}</span>
                        </td>
                        <td class="py-3 sm:py-4 px-3 sm:px-4 text-xs sm:text-sm text-gray-600 dark:text-gray-400 hidden md:table-cell">{{ $entry->phone ?? '-' }}</td>
                        <td class="py-3 sm:py-4 px-3 sm:px-4 text-xs sm:text-sm text-gray-600 dark:text-gray-400">
                            <span class="px-1.5 sm:px-2 py-0.5 sm:py-1 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded text-[10px] sm:text-xs">{{ $entry->reason ?? 'N/A' }}</span>
                        </td>
                        <td class="py-3 sm:py-4 px-3 sm:px-4 text-xs sm:text-sm text-gray-600 dark:text-gray-400 hidden sm:table-cell">{{ $entry->created_at->format('d M Y') }}</td>
                        <td class="py-3 sm:py-4 px-3 sm:px-4">
                            <span class="text-[10px] sm:text-xs text-gray-400 italic">No actions</span>
                        </td>
                    </tr>
                    @empty
                    {{-- DataTables handles empty state better, but we leave this just in case --}}
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @push('scripts')
    <script>
        $(document).ready(function() {
            $('#blacklistTable').DataTable({
                responsive: false,
                order: [
                    [4, 'desc']
                ], // Sort by Date Added desc
                columnDefs: [{
                    targets: [5],
                    orderable: false
                }]
            });
        });
    </script>
    @endpush
</x-admin-layout>