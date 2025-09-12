<x-app-layout>
    <x-slot name="header">
        <h2 class="font-extrabold text-2xl text-gray-900">Add New User</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto bg-white shadow rounded-2xl p-6">
            {{-- Manual Entry Form --}}
            <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-6">
                @csrf

                <div>
                    <label class="block text-sm font-semibold">Name</label>
                    <input type="text" name="name" class="w-full border rounded-lg p-2" required>
                </div>

                <div>
                    <label>Email</label>
                    <input type="email" id="email" name="email" required class="w-full border rounded-lg p-2">
                    <small id="email-message" class="text-red-500 text-sm"></small>
                </div>

                <div>
                    <label class="block text-sm font-semibold">Password</label>
                    <input type="password" name="password" placeholder="Leave blank for default password123"
                           class="w-full border rounded-lg p-2">
                </div>

                {{-- Accounts section (rizqmall + sandbox) --}}
                <div class="space-y-4">
                    {{-- Rizqmall --}}
                    <div>
                        <input type="checkbox" id="rizqmall_active" name="rizqmall_active" value="1"
                               onclick="toggleSection('rizqmallSection')">
                        <label for="rizqmall_active">Activate Rizqmall</label>
                    
                        <div id="rizqmallSection" class="hidden mt-2 space-y-3 border p-3 rounded">
                            <label class="block text-sm font-semibold">Start Date</label>
                            <input type="date" name="rizqmall_start" class="w-full border rounded-lg p-2">
                    
                            <div class="mt-3">
                                <label class="font-semibold">Serial Number</label>
                    
                                <div class="space-y-1">
                                    <label class="flex items-center space-x-2">
                                        <input type="radio" name="rizqmall_serial_mode" value="auto" checked
                                               onclick="toggleManual('rizqmall_serial', false)">
                                        <span>Auto-generate</span>
                                    </label>
                                    <label class="flex items-center space-x-2">
                                        <input type="radio" name="rizqmall_serial_mode" value="manual"
                                               onclick="toggleManual('rizqmall_serial', true)">
                                        <span>Enter Manually</span>
                                    </label>
                                </div>
                    
                                <input type="text" name="rizqmall_serial" id="rizqmall_serial"
                                       class="w-full border rounded-lg p-2 hidden"
                                       placeholder="Enter Rizqmall Serial">
                                <span id="rizqmall_serial_error" class="text-red-500 text-sm"></span>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Sandbox --}}
                    <div>
                        <input type="checkbox" id="sandbox_active" name="sandbox_active" value="1"
                               onclick="toggleSection('sandboxSection')">
                        <label for="sandbox_active">Activate Sandbox</label>
                    
                        <div id="sandboxSection" class="hidden mt-2 space-y-3 border p-3 rounded">
                            <label class="font-semibold">Serial Number</label>
                    
                            <div class="space-y-1">
                                <label class="flex items-center space-x-2">
                                    <input type="radio" name="sandbox_serial_mode" value="auto" checked
                                           onclick="toggleManual('sandbox_serial', false)">
                                    <span>Auto-generate</span>
                                </label>
                                <label class="flex items-center space-x-2">
                                    <input type="radio" name="sandbox_serial_mode" value="manual"
                                           onclick="toggleManual('sandbox_serial', true)">
                                    <span>Enter Manually</span>
                                </label>
                            </div>
                    
                            <input type="text" name="sandbox_serial" id="sandbox_serial"
                                   class="w-full border rounded-lg p-2 hidden"
                                   placeholder="Enter Sandbox Serial">
                            <span id="sandbox_serial_error" class="text-red-500 text-sm"></span>
                        </div>
                    </div>
                </div>

                <button id="submitBtn" type="submit"
                        class="w-full py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-semibold">
                    Create User
                </button>
            </form>

            {{-- Excel Upload --}}
            <div class="mt-10 border-t pt-6">
                <h3 class="font-bold mb-2">Upload via Excel</h3>
                <form method="POST" action="{{ route('admin.users.import') }}" enctype="multipart/form-data">
                    @csrf
                    <input type="file" name="excel" accept=".xlsx,.xls,.csv"
                           class="w-full border rounded-lg p-2 mb-3">
                    <button type="submit"
                            class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg">
                        Upload
                    </button>
                </form>
            </div>
        </div>
    </div>

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
            // Disable if any field has ❌
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
                            errorSpan.textContent = "❌ Serial number already exists!";
                            errorSpan.classList.remove("text-green-600");
                            errorSpan.classList.add("text-red-500");
                            input.classList.add('border-red-500');
                            errors[fieldId.includes('rizqmall') ? 'rizqmall' : 'sandbox'] = true;
                        } else {
                            errorSpan.textContent = "✅ Available";
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
                        emailMessage.textContent = "❌ Email already registered!";
                        emailMessage.classList.remove("text-green-600");
                        emailMessage.classList.add("text-red-500");
                        emailInput.classList.add('border-red-500');
                        errors.email = true;
                    } else {
                        emailMessage.textContent = "✅ Available";
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
</x-app-layout>
