<x-admin-layout>
    <x-slot name="pageTitle">Rekod Hutang / Debt Records</x-slot>
    <x-slot name="breadcrumb">Urus hutang anda sebelum pendaftaran / Manage your debts before registration</x-slot>

    @php
        $maxLimit = 50000000; // RM500,000 in cents
        $usedPercentage = ($totalHutang / $maxLimit) * 100;
    @endphp

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-6 sm:mb-8">
        {{-- Total Hutang --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 sm:p-6 border border-gray-100 dark:border-gray-700 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 sm:w-16 sm:h-16 bg-gradient-to-br from-red-500 to-rose-600 rounded-2xl flex items-center justify-center shadow-lg flex-shrink-0">
                    <i class="fas fa-file-invoice-dollar text-white text-xl sm:text-2xl"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-sm sm:text-base text-gray-500 dark:text-gray-400 font-medium">Jumlah Hutang</p>
                    <p class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">RM {{ number_format($totalHutang / 100, 2) }}</p>
                    <p class="text-xs text-gray-400">Total Debt</p>
                </div>
            </div>
        </div>

        {{-- Unsettled --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 sm:p-6 border border-gray-100 dark:border-gray-700 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 sm:w-16 sm:h-16 bg-gradient-to-br from-amber-500 to-orange-600 rounded-2xl flex items-center justify-center shadow-lg flex-shrink-0">
                    <i class="fas fa-clock text-white text-xl sm:text-2xl"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-sm sm:text-base text-gray-500 dark:text-gray-400 font-medium">Belum Selesai</p>
                    <p class="text-xl sm:text-2xl font-bold text-amber-600 dark:text-amber-400">RM {{ number_format($unsettledTotal / 100, 2) }}</p>
                    <p class="text-xs text-gray-400">Unsettled</p>
                </div>
            </div>
        </div>

        {{-- Settled --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 sm:p-6 border border-gray-100 dark:border-gray-700 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 sm:w-16 sm:h-16 bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl flex items-center justify-center shadow-lg flex-shrink-0">
                    <i class="fas fa-check-circle text-white text-xl sm:text-2xl"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-sm sm:text-base text-gray-500 dark:text-gray-400 font-medium">Sudah Selesai</p>
                    <p class="text-xl sm:text-2xl font-bold text-green-600 dark:text-green-400">RM {{ number_format($settledTotal / 100, 2) }}</p>
                    <p class="text-xs text-gray-400">Settled</p>
                </div>
            </div>
        </div>

        {{-- Remaining Limit --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 sm:p-6 border border-gray-100 dark:border-gray-700 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 sm:w-16 sm:h-16 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center shadow-lg flex-shrink-0">
                    <i class="fas fa-wallet text-white text-xl sm:text-2xl"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-sm sm:text-base text-gray-500 dark:text-gray-400 font-medium">Baki Had</p>
                    <p class="text-xl sm:text-2xl font-bold text-blue-600 dark:text-blue-400">RM {{ number_format($remainingLimit / 100, 2) }}</p>
                    <p class="text-xs text-gray-400">Remaining Limit</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Limit Progress Bar --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 sm:p-6 border border-gray-100 dark:border-gray-700 shadow-sm mb-6 sm:mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
            <div>
                <h3 class="text-lg sm:text-xl font-semibold text-gray-900 dark:text-white">
                    Had Hutang / Debt Limit
                </h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Maksimum RM500,000 / Maximum RM500,000</p>
            </div>
            <span class="text-2xl sm:text-3xl font-bold {{ $usedPercentage >= 90 ? 'text-red-600' : ($usedPercentage >= 70 ? 'text-amber-600' : 'text-green-600') }}">
                {{ number_format($usedPercentage, 1) }}%
            </span>
        </div>
        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-4 sm:h-5 overflow-hidden">
            <div class="h-full rounded-full transition-all duration-500 {{ $usedPercentage >= 90 ? 'bg-gradient-to-r from-red-500 to-rose-600' : ($usedPercentage >= 70 ? 'bg-gradient-to-r from-amber-500 to-orange-600' : 'bg-gradient-to-r from-green-500 to-emerald-600') }}"
                style="width: {{ min(100, $usedPercentage) }}%"></div>
        </div>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
            RM {{ number_format($totalHutang / 100, 2) }} / RM {{ number_format($maxLimit / 100, 2) }}
        </p>
    </div>

    {{-- Action Bar --}}
    <div class="flex flex-col sm:flex-row gap-4 mb-6 sm:mb-8">
        <button onclick="openAddModal()" class="flex-1 sm:flex-none inline-flex items-center justify-center gap-3 px-6 py-4 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-2xl font-semibold text-lg shadow-lg hover:shadow-xl hover:scale-[1.02] transition-all duration-300">
            <i class="fas fa-plus-circle text-xl"></i>
            <span>Tambah Hutang / Add Debt</span>
        </button>

        <a href="{{ route('collection.index') }}" class="flex-1 sm:flex-none inline-flex items-center justify-center gap-3 px-6 py-4 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 rounded-2xl font-semibold text-lg border-2 border-gray-200 dark:border-gray-600 hover:border-indigo-500 hover:text-indigo-600 transition-all duration-300">
            <i class="fas fa-arrow-left text-xl"></i>
            <span>Kembali / Back</span>
        </a>
    </div>

    {{-- Filter Tabs --}}
    <div class="flex gap-2 mb-6 overflow-x-auto pb-2">
        <a href="{{ route('hutang.index') }}"
           class="px-5 py-3 rounded-xl font-semibold text-base whitespace-nowrap transition-all {{ !request('status') ? 'bg-indigo-600 text-white shadow-lg' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200' }}">
            Semua / All
        </a>
        <a href="{{ route('hutang.index', ['status' => 'unsettled']) }}"
           class="px-5 py-3 rounded-xl font-semibold text-base whitespace-nowrap transition-all {{ request('status') === 'unsettled' ? 'bg-amber-600 text-white shadow-lg' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200' }}">
            Belum Selesai / Unsettled
        </a>
        <a href="{{ route('hutang.index', ['status' => 'settled']) }}"
           class="px-5 py-3 rounded-xl font-semibold text-base whitespace-nowrap transition-all {{ request('status') === 'settled' ? 'bg-green-600 text-white shadow-lg' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200' }}">
            Sudah Selesai / Settled
        </a>
    </div>

    {{-- Hutang List --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
        @if($hutangList->isEmpty())
            <div class="p-12 text-center">
                <div class="w-24 h-24 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-file-invoice text-4xl text-gray-400"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Tiada Rekod Hutang</h3>
                <p class="text-gray-500 dark:text-gray-400 mb-6">No debt records found</p>
                <button onclick="openAddModal()" class="inline-flex items-center gap-2 px-6 py-3 bg-indigo-600 text-white rounded-xl font-semibold hover:bg-indigo-700 transition-colors">
                    <i class="fas fa-plus"></i>
                    Tambah Hutang Pertama / Add First Debt
                </button>
            </div>
        @else
            {{-- Mobile Cards View --}}
            <div class="block lg:hidden divide-y divide-gray-100 dark:divide-gray-700">
                @foreach($hutangList as $hutang)
                    <div class="p-4 sm:p-5">
                        <div class="flex items-start justify-between gap-4 mb-3">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="px-3 py-1 text-sm font-semibold rounded-lg {{ $hutang->is_settled ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400' }}">
                                        {{ $hutang->is_settled ? 'Selesai' : 'Belum Selesai' }}
                                    </span>
                                    @if($hutang->reference)
                                        <span class="text-sm text-gray-500">#{{ $hutang->reference }}</span>
                                    @endif
                                </div>
                                <p class="text-lg font-bold text-gray-900 dark:text-white">
                                    RM {{ number_format($hutang->amount / 100, 2) }}
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $hutang->hutang_date->format('d/m/Y') }}
                                </p>
                            </div>
                        </div>

                        <p class="text-base text-gray-700 dark:text-gray-300 mb-3">
                            {{ $hutang->description }}
                        </p>

                        @if($hutang->documents->count() > 0)
                            <div class="flex flex-wrap gap-2 mb-3">
                                @foreach($hutang->documents as $doc)
                                    <a href="{{ Storage::url($doc->file_path) }}" target="_blank"
                                       class="inline-flex items-center gap-1 px-3 py-1.5 bg-gray-100 dark:bg-gray-700 rounded-lg text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-200 transition-colors">
                                        <i class="fas {{ $doc->is_pdf ? 'fa-file-pdf text-red-500' : 'fa-file-image text-blue-500' }}"></i>
                                        <span class="max-w-[100px] truncate">{{ $doc->file_name }}</span>
                                    </a>
                                @endforeach
                            </div>
                        @endif

                        <div class="flex gap-2">
                            <form action="{{ route('hutang.toggle-settled', $hutang) }}" method="POST" class="flex-1">
                                @csrf
                                <button type="submit" class="w-full px-4 py-2.5 rounded-xl font-semibold text-sm {{ $hutang->is_settled ? 'bg-amber-100 text-amber-700 hover:bg-amber-200' : 'bg-green-100 text-green-700 hover:bg-green-200' }} transition-colors">
                                    <i class="fas {{ $hutang->is_settled ? 'fa-undo' : 'fa-check' }} mr-2"></i>
                                    {{ $hutang->is_settled ? 'Batal Selesai' : 'Tandakan Selesai' }}
                                </button>
                            </form>
                            <button onclick="confirmDelete({{ $hutang->id }})" class="px-4 py-2.5 rounded-xl font-semibold text-sm bg-red-100 text-red-700 hover:bg-red-200 transition-colors">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Desktop Table View --}}
            <div class="hidden lg:block overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="text-left text-sm font-semibold text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-700/50">
                            <th class="py-4 px-6">Tarikh / Date</th>
                            <th class="py-4 px-6">Rujukan / Ref</th>
                            <th class="py-4 px-6">Penerangan / Description</th>
                            <th class="py-4 px-6 text-right">Jumlah / Amount</th>
                            <th class="py-4 px-6 text-center">Dokumen / Docs</th>
                            <th class="py-4 px-6 text-center">Status</th>
                            <th class="py-4 px-6 text-center">Tindakan / Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach($hutangList as $hutang)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <td class="py-4 px-6">
                                    <p class="font-medium text-gray-900 dark:text-white">{{ $hutang->hutang_date->format('d/m/Y') }}</p>
                                    <p class="text-sm text-gray-500">{{ $hutang->hutang_date->format('l') }}</p>
                                </td>
                                <td class="py-4 px-6">
                                    <span class="text-gray-900 dark:text-white">{{ $hutang->reference ?: '-' }}</span>
                                </td>
                                <td class="py-4 px-6">
                                    <p class="text-gray-900 dark:text-white max-w-xs truncate">{{ $hutang->description }}</p>
                                    @if($hutang->notes)
                                        <p class="text-sm text-gray-500 truncate max-w-xs">{{ $hutang->notes }}</p>
                                    @endif
                                </td>
                                <td class="py-4 px-6 text-right">
                                    <p class="text-lg font-bold text-gray-900 dark:text-white">
                                        RM {{ number_format($hutang->amount / 100, 2) }}
                                    </p>
                                </td>
                                <td class="py-4 px-6 text-center">
                                    @if($hutang->documents->count() > 0)
                                        <div class="flex items-center justify-center gap-1">
                                            @foreach($hutang->documents->take(3) as $doc)
                                                <a href="{{ Storage::url($doc->file_path) }}" target="_blank"
                                                   class="w-8 h-8 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center hover:bg-gray-200 transition-colors"
                                                   title="{{ $doc->file_name }}">
                                                    <i class="fas {{ $doc->is_pdf ? 'fa-file-pdf text-red-500' : 'fa-file-image text-blue-500' }} text-sm"></i>
                                                </a>
                                            @endforeach
                                            @if($hutang->documents->count() > 3)
                                                <span class="text-sm text-gray-500">+{{ $hutang->documents->count() - 3 }}</span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="py-4 px-6 text-center">
                                    <span class="px-3 py-1.5 text-sm font-semibold rounded-lg {{ $hutang->is_settled ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400' }}">
                                        {{ $hutang->is_settled ? 'Selesai' : 'Belum Selesai' }}
                                    </span>
                                </td>
                                <td class="py-4 px-6">
                                    <div class="flex items-center justify-center gap-2">
                                        <form action="{{ route('hutang.toggle-settled', $hutang) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="w-10 h-10 rounded-xl flex items-center justify-center {{ $hutang->is_settled ? 'bg-amber-100 text-amber-600 hover:bg-amber-200' : 'bg-green-100 text-green-600 hover:bg-green-200' }} transition-colors"
                                                    title="{{ $hutang->is_settled ? 'Batal Selesai' : 'Tandakan Selesai' }}">
                                                <i class="fas {{ $hutang->is_settled ? 'fa-undo' : 'fa-check' }}"></i>
                                            </button>
                                        </form>
                                        <button onclick="confirmDelete({{ $hutang->id }})"
                                                class="w-10 h-10 rounded-xl bg-red-100 text-red-600 hover:bg-red-200 flex items-center justify-center transition-colors"
                                                title="Padam / Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($hutangList->hasPages())
                <div class="p-4 border-t border-gray-100 dark:border-gray-700">
                    {{ $hutangList->links() }}
                </div>
            @endif
        @endif
    </div>

    {{-- Add Hutang Modal --}}
    <div id="addModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="min-h-screen px-4 flex items-center justify-center">
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" onclick="closeAddModal()"></div>

            <div class="relative bg-white dark:bg-gray-800 rounded-3xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
                {{-- Modal Header --}}
                <div class="sticky top-0 bg-gradient-to-r from-indigo-600 to-purple-600 text-white p-6 rounded-t-3xl">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-2xl font-bold">Tambah Hutang Baru</h2>
                            <p class="text-white/80 text-sm mt-1">Add New Debt Record</p>
                        </div>
                        <button onclick="closeAddModal()" class="w-10 h-10 rounded-full bg-white/20 hover:bg-white/30 flex items-center justify-center transition-colors">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                </div>

                {{-- Modal Body --}}
                <form action="{{ route('hutang.store') }}" method="POST" enctype="multipart/form-data" class="p-6 sm:p-8">
                    @csrf

                    {{-- Important Notice --}}
                    <div class="p-4 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-2xl mb-6">
                        <div class="flex gap-3">
                            <div class="w-10 h-10 bg-amber-100 dark:bg-amber-900/50 rounded-full flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-exclamation-triangle text-amber-600 dark:text-amber-400"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-amber-800 dark:text-amber-400">Perhatian / Notice</p>
                                <p class="text-sm text-amber-700 dark:text-amber-500">
                                    Tarikh hutang mesti <strong>SEBELUM</strong> tarikh pendaftaran anda ({{ Auth::user()->created_at->format('d/m/Y') }}).
                                    <br>
                                    Debt date must be <strong>BEFORE</strong> your registration date.
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Date --}}
                    <div class="mb-5">
                        <label class="block text-base font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-calendar-alt text-indigo-600 mr-2"></i>
                            Tarikh Hutang / Debt Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="hutang_date" required
                               max="{{ Auth::user()->created_at->subDay()->format('Y-m-d') }}"
                               value="{{ old('hutang_date') }}"
                               class="w-full px-4 py-4 text-lg border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white transition-all">
                        @error('hutang_date')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Reference --}}
                    <div class="mb-5">
                        <label class="block text-base font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-hashtag text-indigo-600 mr-2"></i>
                            Nombor Rujukan / Reference Number
                        </label>
                        <input type="text" name="reference" placeholder="Cth: INV-2024-001"
                               value="{{ old('reference') }}"
                               class="w-full px-4 py-4 text-lg border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white transition-all">
                    </div>

                    {{-- Description --}}
                    <div class="mb-5">
                        <label class="block text-base font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-align-left text-indigo-600 mr-2"></i>
                            Penerangan / Description <span class="text-red-500">*</span>
                        </label>
                        <textarea name="description" rows="3" required placeholder="Terangkan hutang anda / Describe your debt"
                                  class="w-full px-4 py-4 text-lg border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white transition-all resize-none">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Amount --}}
                    <div class="mb-5">
                        <label class="block text-base font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-money-bill-wave text-indigo-600 mr-2"></i>
                            Jumlah (RM) / Amount (RM) <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-xl font-bold text-gray-500">RM</span>
                            <input type="number" name="amount" step="0.01" min="0.01" max="500000" required
                                   placeholder="0.00"
                                   value="{{ old('amount') }}"
                                   class="w-full pl-16 pr-4 py-4 text-xl font-bold border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white transition-all">
                        </div>
                        <p class="mt-2 text-sm text-gray-500">
                            Baki had: RM {{ number_format($remainingLimit / 100, 2) }} / Remaining limit: RM {{ number_format($remainingLimit / 100, 2) }}
                        </p>
                        @error('amount')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Notes --}}
                    <div class="mb-5">
                        <label class="block text-base font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-sticky-note text-indigo-600 mr-2"></i>
                            Catatan Tambahan / Additional Notes
                        </label>
                        <textarea name="notes" rows="2" placeholder="Catatan tambahan (pilihan) / Additional notes (optional)"
                                  class="w-full px-4 py-4 text-lg border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white transition-all resize-none">{{ old('notes') }}</textarea>
                    </div>

                    {{-- Documents --}}
                    <div class="mb-6">
                        <label class="block text-base font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-paperclip text-indigo-600 mr-2"></i>
                            Dokumen Sokongan / Supporting Documents
                        </label>
                        <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl p-6 text-center hover:border-indigo-500 transition-colors">
                            <input type="file" name="documents[]" multiple accept=".pdf,.jpg,.jpeg,.png,.gif"
                                   id="documentInput"
                                   class="hidden"
                                   onchange="updateFileList()">
                            <label for="documentInput" class="cursor-pointer">
                                <div class="w-16 h-16 bg-indigo-100 dark:bg-indigo-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-cloud-upload-alt text-2xl text-indigo-600 dark:text-indigo-400"></i>
                                </div>
                                <p class="text-lg font-medium text-gray-700 dark:text-gray-300">Klik untuk muat naik</p>
                                <p class="text-sm text-gray-500">Click to upload</p>
                                <p class="text-xs text-gray-400 mt-2">PDF, JPG, PNG, GIF (Maks 5MB setiap fail / Max 5MB per file)</p>
                            </label>
                            <div id="fileList" class="mt-4 hidden">
                                <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Fail dipilih / Selected files:</p>
                                <ul id="fileListItems" class="text-sm text-gray-600 dark:text-gray-400 space-y-1"></ul>
                            </div>
                        </div>
                        @error('documents.*')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Submit Button --}}
                    <div class="flex gap-4">
                        <button type="button" onclick="closeAddModal()"
                                class="flex-1 px-6 py-4 text-lg font-semibold border-2 border-gray-200 dark:border-gray-600 rounded-xl text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            Batal / Cancel
                        </button>
                        <button type="submit"
                                class="flex-1 px-6 py-4 text-lg font-semibold bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl hover:shadow-lg hover:scale-[1.02] transition-all">
                            <i class="fas fa-save mr-2"></i>
                            Simpan / Save
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    <div id="deleteModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="min-h-screen px-4 flex items-center justify-center">
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" onclick="closeDeleteModal()"></div>

            <div class="relative bg-white dark:bg-gray-800 rounded-3xl shadow-2xl w-full max-w-md p-8 text-center">
                <div class="w-20 h-20 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-exclamation-triangle text-4xl text-red-600 dark:text-red-400"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Padam Hutang?</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-6">Delete this debt record?</p>
                <p class="text-sm text-gray-500 mb-8">Tindakan ini tidak boleh dibatalkan. / This action cannot be undone.</p>

                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="flex gap-4">
                        <button type="button" onclick="closeDeleteModal()"
                                class="flex-1 px-6 py-4 text-lg font-semibold border-2 border-gray-200 dark:border-gray-600 rounded-xl text-gray-700 dark:text-gray-300 hover:bg-gray-100 transition-colors">
                            Batal / Cancel
                        </button>
                        <button type="submit"
                                class="flex-1 px-6 py-4 text-lg font-semibold bg-red-600 text-white rounded-xl hover:bg-red-700 transition-colors">
                            <i class="fas fa-trash mr-2"></i>
                            Padam / Delete
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Error Modal (for invalid date) --}}
    @if($errors->any())
    <div id="errorModal" class="fixed inset-0 z-50 overflow-y-auto">
        <div class="min-h-screen px-4 flex items-center justify-center">
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" onclick="closeErrorModal()"></div>

            <div class="relative bg-white dark:bg-gray-800 rounded-3xl shadow-2xl w-full max-w-md p-8 text-center">
                <div class="w-20 h-20 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-times-circle text-4xl text-red-600 dark:text-red-400"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Ralat / Error</h3>
                <div class="text-left bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4 mb-6">
                    <ul class="text-sm text-red-700 dark:text-red-400 space-y-2">
                        @foreach($errors->all() as $error)
                            <li class="flex items-start gap-2">
                                <i class="fas fa-exclamation-circle mt-0.5 flex-shrink-0"></i>
                                <span>{{ $error }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
                <button onclick="closeErrorModal()"
                        class="w-full px-6 py-4 text-lg font-semibold bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-200 transition-colors">
                    Faham / OK
                </button>
            </div>
        </div>
    </div>
    @endif

    @push('scripts')
    <script>
        // Add Modal
        function openAddModal() {
            document.getElementById('addModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeAddModal() {
            document.getElementById('addModal').classList.add('hidden');
            document.body.style.overflow = '';
        }

        // Delete Modal
        function confirmDelete(id) {
            document.getElementById('deleteForm').action = '/hutang/' + id;
            document.getElementById('deleteModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
            document.body.style.overflow = '';
        }

        // Error Modal
        function closeErrorModal() {
            const modal = document.getElementById('errorModal');
            if (modal) {
                modal.classList.add('hidden');
                document.body.style.overflow = '';
            }
        }

        // File List Update
        function updateFileList() {
            const input = document.getElementById('documentInput');
            const fileList = document.getElementById('fileList');
            const fileListItems = document.getElementById('fileListItems');

            if (input.files.length > 0) {
                fileList.classList.remove('hidden');
                fileListItems.innerHTML = '';

                for (let i = 0; i < input.files.length; i++) {
                    const li = document.createElement('li');
                    li.className = 'flex items-center gap-2';
                    li.innerHTML = '<i class="fas fa-file text-indigo-500"></i>' + input.files[i].name;
                    fileListItems.appendChild(li);
                }
            } else {
                fileList.classList.add('hidden');
            }
        }

        // Close modals on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeAddModal();
                closeDeleteModal();
                closeErrorModal();
            }
        });

        // Show add modal if there were validation errors
        @if($errors->any() && old('hutang_date'))
            openAddModal();
        @endif
    </script>
    @endpush
</x-admin-layout>
