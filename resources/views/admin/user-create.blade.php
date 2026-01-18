<x-admin-layout>
    <x-slot name="pageTitle">Add New User</x-slot>
    <x-slot name="breadcrumb">Create a new user account</x-slot>

    <div class="bg-white dark:bg-gray-800 rounded-2xl p-4 sm:p-6 border border-gray-100 dark:border-gray-700">
        {{-- Manual Entry Form --}}
        <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-4 sm:space-y-6">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs sm:text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Name</label>
                    <input type="text" name="name" class="w-full border border-gray-300 dark:border-gray-600 rounded-xl p-2.5 sm:p-3 text-sm bg-white dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent" required>
                </div>

                <div>
                    <label class="block text-xs sm:text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Email</label>
                    <input type="email" id="email" name="email" required class="w-full border border-gray-300 dark:border-gray-600 rounded-xl p-2.5 sm:p-3 text-sm bg-white dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    <small id="email-message" class="text-red-500 text-xs sm:text-sm mt-1"></small>
                </div>
            </div>

            <div>
                <label class="block text-xs sm:text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Password</label>
                <input type="password" name="password" placeholder="Leave blank for default password123"
                       class="w-full border border-gray-300 dark:border-gray-600 rounded-xl p-2.5 sm:p-3 text-sm bg-white dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
            </div>

            {{-- Accounts section (rizqmall + sandbox) --}}
            <div class="space-y-4">
                {{-- Rizqmall --}}
                <div class="p-3 sm:p-4 border border-gray-200 dark:border-gray-600 rounded-xl">
                    <div class="flex items-center gap-2 sm:gap-3">
                        <input type="checkbox" id="rizqmall_active" name="rizqmall_active" value="1"
                               onclick="toggleSection('rizqmallSection')"
                               class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                        <label for="rizqmall_active" class="text-sm sm:text-base font-semibold text-gray-900 dark:text-white">Activate Rizqmall</label>
                    </div>

                    <div id="rizqmallSection" class="hidden mt-3 sm:mt-4 space-y-3 sm:space-y-4 pl-6 sm:pl-7 border-l-2 border-indigo-200 dark:border-indigo-800">
                        <div>
                            <label class="block text-xs sm:text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Start Date</label>
                            <input type="date" name="rizqmall_start" class="w-full border border-gray-300 dark:border-gray-600 rounded-xl p-2.5 sm:p-3 text-sm bg-white dark:bg-gray-700 dark:text-white">
                        </div>

                        <div>
                            <label class="block text-xs sm:text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Serial Number</label>

                            <div class="space-y-2">
                                <label class="flex items-center gap-2">
                                    <input type="radio" name="rizqmall_serial_mode" value="auto" checked
                                           onclick="toggleManual('rizqmall_serial', false)"
                                           class="w-4 h-4 text-indigo-600 border-gray-300 focus:ring-indigo-500">
                                    <span class="text-xs sm:text-sm text-gray-700 dark:text-gray-300">Auto-generate</span>
                                </label>
                                <label class="flex items-center gap-2">
                                    <input type="radio" name="rizqmall_serial_mode" value="manual"
                                           onclick="toggleManual('rizqmall_serial', true)"
                                           class="w-4 h-4 text-indigo-600 border-gray-300 focus:ring-indigo-500">
                                    <span class="text-xs sm:text-sm text-gray-700 dark:text-gray-300">Enter Manually</span>
                                </label>
                            </div>

                            <input type="text" name="rizqmall_serial" id="rizqmall_serial"
                                   class="w-full border border-gray-300 dark:border-gray-600 rounded-xl p-2.5 sm:p-3 text-sm bg-white dark:bg-gray-700 dark:text-white hidden mt-2"
                                   placeholder="Enter Rizqmall Serial">
                            <span id="rizqmall_serial_error" class="text-red-500 text-xs sm:text-sm"></span>
                        </div>
                    </div>
                </div>

                {{-- Sandbox --}}
                <div class="p-3 sm:p-4 border border-gray-200 dark:border-gray-600 rounded-xl">
                    <div class="flex items-center gap-2 sm:gap-3">
                        <input type="checkbox" id="sandbox_active" name="sandbox_active" value="1"
                               onclick="toggleSection('sandboxSection')"
                               class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                        <label for="sandbox_active" class="text-sm sm:text-base font-semibold text-gray-900 dark:text-white">Activate Sandbox</label>
                    </div>

                    <div id="sandboxSection" class="hidden mt-3 sm:mt-4 space-y-3 sm:space-y-4 pl-6 sm:pl-7 border-l-2 border-purple-200 dark:border-purple-800">
                        <div>
                            <label class="block text-xs sm:text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Serial Number</label>

                            <div class="space-y-2">
                                <label class="flex items-center gap-2">
                                    <input type="radio" name="sandbox_serial_mode" value="auto" checked
                                           onclick="toggleManual('sandbox_serial', false)"
                                           class="w-4 h-4 text-purple-600 border-gray-300 focus:ring-purple-500">
                                    <span class="text-xs sm:text-sm text-gray-700 dark:text-gray-300">Auto-generate</span>
                                </label>
                                <label class="flex items-center gap-2">
                                    <input type="radio" name="sandbox_serial_mode" value="manual"
                                           onclick="toggleManual('sandbox_serial', true)"
                                           class="w-4 h-4 text-purple-600 border-gray-300 focus:ring-purple-500">
                                    <span class="text-xs sm:text-sm text-gray-700 dark:text-gray-300">Enter Manually</span>
                                </label>
                            </div>

                            <input type="text" name="sandbox_serial" id="sandbox_serial"
                                   class="w-full border border-gray-300 dark:border-gray-600 rounded-xl p-2.5 sm:p-3 text-sm bg-white dark:bg-gray-700 dark:text-white hidden mt-2"
                                   placeholder="Enter Sandbox Serial">
                            <span id="sandbox_serial_error" class="text-red-500 text-xs sm:text-sm"></span>
                        </div>
                    </div>
                </div>
            </div>

            <button id="submitBtn" type="submit"
                    class="w-full py-2.5 sm:py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-semibold transition-colors shadow-lg text-sm sm:text-base">
                Create User
            </button>
        </form>

        {{-- Excel Upload --}}
        <div class="mt-8 sm:mt-10 border-t border-gray-200 dark:border-gray-700 pt-4 sm:pt-6">
            <h3 class="font-bold mb-3 text-gray-900 dark:text-white text-sm sm:text-base">Upload via Excel</h3>
            <form method="POST" action="{{ route('admin.users.import') }}" enctype="multipart/form-data" class="flex flex-col sm:flex-row gap-3">
                @csrf
                <input type="file" name="excel" accept=".xlsx,.xls,.csv"
                       class="flex-1 border border-gray-300 dark:border-gray-600 rounded-xl p-2.5 text-xs sm:text-sm bg-white dark:bg-gray-700 dark:text-white file:mr-2 sm:file:mr-4 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs sm:file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 dark:file:bg-indigo-900/30 dark:file:text-indigo-400">
                <button type="submit"
                        class="px-4 sm:px-6 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-xl font-semibold transition-colors text-sm">
                    <i class="fas fa-upload mr-2"></i> Upload
                </button>
            </form>
        </div>
    </div>

    @push('scripts')
    {{-- Toggle helpers --}}
    <script>
        function toggleSection(id) {
            document.getElementById(id).classList.toggle('hidden');
        }

        function toggleManual(inputId, show) {
            document.getElementById(inputId).classList.toggle('hidden', !show);
        }
    </script>

    {{-- Serial + Email uniqueness checks --}}
    <script>
    document.addEventListener("DOMContentLoaded", function () {
        const submitBtn = document.getElementById('submitBtn');
        let errors = { email: false, rizqmall: false, sandbox: false };

        function updateSubmitState() {
            // Disable if any field has error
            const hasError = Object.values(errors).some(v => v === true);
            submitBtn.disabled = hasError;
            submitBtn.classList.toggle('opacity-50', hasError);
            submitBtn.classList.toggle('cursor-not-allowed', hasError);
        }

        // Serial check for rizqmall + sandbox
        ['rizqmall_serial', 'sandbox_serial'].forEach(function (fieldId) {
            const input = document.getElementById(fieldId);
            const errorSpan = document.getElementById(fieldId + '_error');

            if (input) {
                input.addEventListener('blur', function () {
                    const value = input.value.trim();
                    if (!value) {
                        errorSpan.textContent = '';
                        input.classList.remove('border-red-500');
                        errors[fieldId.includes('rizqmall') ? 'rizqmall' : 'sandbox'] = false;
                        updateSubmitState();
                        return;
                    }

                    fetch("{{ route('admin.users.checkSerial') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        },
                        body: JSON.stringify({ serial: value })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.exists) {
                            errorSpan.textContent = "Serial number already exists!";
                            errorSpan.classList.remove("text-green-600");
                            errorSpan.classList.add("text-red-500");
                            input.classList.add('border-red-500');
                            errors[fieldId.includes('rizqmall') ? 'rizqmall' : 'sandbox'] = true;
                        } else {
                            errorSpan.textContent = "Available";
                            errorSpan.classList.remove("text-red-500");
                            errorSpan.classList.add("text-green-600");
                            input.classList.remove('border-red-500');
                            errors[fieldId.includes('rizqmall') ? 'rizqmall' : 'sandbox'] = false;
                        }
                        updateSubmitState();
                    })
                    .catch(err => console.error(err));
                });
            }
        });

        // Email check
        const emailInput = document.getElementById('email');
        const emailMessage = document.getElementById('email-message');

        if (emailInput) {
            emailInput.addEventListener('blur', function () {
                const email = emailInput.value.trim();
                if (!email) {
                    emailMessage.textContent = '';
                    emailInput.classList.remove('border-red-500');
                    errors.email = false;
                    updateSubmitState();
                    return;
                }

                fetch("{{ route('admin.users.checkEmail') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({ email: email })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.exists) {
                        emailMessage.textContent = "Email already registered!";
                        emailMessage.classList.remove("text-green-600");
                        emailMessage.classList.add("text-red-500");
                        emailInput.classList.add('border-red-500');
                        errors.email = true;
                    } else {
                        emailMessage.textContent = "Available";
                        emailMessage.classList.remove("text-red-500");
                        emailMessage.classList.add("text-green-600");
                        emailInput.classList.remove('border-red-500');
                        errors.email = false;
                    }
                    updateSubmitState();
                })
                .catch(err => console.error(err));
            });
        }

        updateSubmitState(); // init check
    });
    </script>
    @endpush
</x-admin-layout>
