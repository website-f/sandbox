<x-admin-layout>
    <x-slot name="pageTitle">Edit Profile</x-slot>
    <x-slot name="breadcrumb">Update your personal, business, and educational information</x-slot>

    {{-- Success/Error Message --}}
    @if(session('success'))
        <div class="mb-6 p-4 rounded-xl bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 font-semibold flex items-center">
            <i class="fas fa-check-circle mr-3 text-green-500"></i>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-6 p-4 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 font-semibold flex items-center">
            <i class="fas fa-exclamation-circle mr-3 text-red-500"></i>
            {{ session('error') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="mb-6 p-4 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800">
            <ul class="list-disc list-inside text-red-700 dark:text-red-400">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Back to Profile --}}
    <div class="mb-6">
        <a href="{{ route('profile.index') }}" class="inline-flex items-center gap-2 text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 font-medium transition-colors">
            <i class="fas fa-arrow-left"></i>
            <span>Back to Profile</span>
        </a>
    </div>

    {{-- Tabs Container --}}
    <div x-data="{ tab: '{{ request('tab', 'profile') }}' }" class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 p-4 sm:p-6">
        {{-- Tab Navigation --}}
        <div class="mb-4 sm:mb-6 border-b border-gray-200 dark:border-gray-700 pb-4 -mx-4 sm:mx-0 px-4 sm:px-0">
            <nav class="flex overflow-x-auto gap-2 pb-2 sm:pb-0 sm:flex-wrap scrollbar-hide" aria-label="Tabs" style="-webkit-overflow-scrolling: touch;">
                <button @click="tab = 'profile'"
                    :class="tab === 'profile' ? 'bg-indigo-600 text-white shadow-md' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-800 dark:hover:text-white'"
                    class="whitespace-nowrap rounded-xl px-3 sm:px-4 py-2 sm:py-2.5 text-xs sm:text-sm font-medium transition-all duration-200 focus:outline-none flex-shrink-0">
                    <i class="fas fa-user sm:mr-2"></i><span class="hidden sm:inline"> Personal Profile</span>
                </button>
                <button @click="tab = 'bank'"
                    :class="tab === 'bank' ? 'bg-indigo-600 text-white shadow-md' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-800 dark:hover:text-white'"
                    class="whitespace-nowrap rounded-xl px-3 sm:px-4 py-2 sm:py-2.5 text-xs sm:text-sm font-medium transition-all duration-200 focus:outline-none flex-shrink-0">
                    <i class="fas fa-university sm:mr-2"></i><span class="hidden sm:inline"> Bank</span>
                </button>
                <button @click="tab = 'password'"
                    :class="tab === 'password' ? 'bg-indigo-600 text-white shadow-md' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-800 dark:hover:text-white'"
                    class="whitespace-nowrap rounded-xl px-3 sm:px-4 py-2 sm:py-2.5 text-xs sm:text-sm font-medium transition-all duration-200 focus:outline-none flex-shrink-0">
                    <i class="fas fa-lock sm:mr-2"></i><span class="hidden sm:inline"> Password</span>
                </button>
                <button @click="tab = 'business'"
                    :class="tab === 'business' ? 'bg-indigo-600 text-white shadow-md' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-800 dark:hover:text-white'"
                    class="whitespace-nowrap rounded-xl px-3 sm:px-4 py-2 sm:py-2.5 text-xs sm:text-sm font-medium transition-all duration-200 focus:outline-none flex-shrink-0">
                    <i class="fas fa-briefcase sm:mr-2"></i><span class="hidden sm:inline"> Business</span>
                </button>
                <button @click="tab = 'education'"
                    :class="tab === 'education' ? 'bg-indigo-600 text-white shadow-md' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-800 dark:hover:text-white'"
                    class="whitespace-nowrap rounded-xl px-3 sm:px-4 py-2 sm:py-2.5 text-xs sm:text-sm font-medium transition-all duration-200 focus:outline-none flex-shrink-0">
                    <i class="fas fa-graduation-cap sm:mr-2"></i><span class="hidden sm:inline"> Education</span>
                </button>
                <button @click="tab = 'courses'"
                    :class="tab === 'courses' ? 'bg-indigo-600 text-white shadow-md' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-800 dark:hover:text-white'"
                    class="whitespace-nowrap rounded-xl px-3 sm:px-4 py-2 sm:py-2.5 text-xs sm:text-sm font-medium transition-all duration-200 focus:outline-none flex-shrink-0">
                    <i class="fas fa-book sm:mr-2"></i><span class="hidden sm:inline"> Courses</span>
                </button>
                <button @click="tab = 'pewaris'"
                    :class="tab === 'pewaris' ? 'bg-indigo-600 text-white shadow-md' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-800 dark:hover:text-white'"
                    class="whitespace-nowrap rounded-xl px-3 sm:px-4 py-2 sm:py-2.5 text-xs sm:text-sm font-medium transition-all duration-200 focus:outline-none flex-shrink-0">
                    <i class="fas fa-users sm:mr-2"></i><span class="hidden sm:inline"> Next of Kin</span>
                </button>
                <button @click="tab = 'affiliation'"
                    :class="tab === 'affiliation' ? 'bg-indigo-600 text-white shadow-md' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-800 dark:hover:text-white'"
                    class="whitespace-nowrap rounded-xl px-3 sm:px-4 py-2 sm:py-2.5 text-xs sm:text-sm font-medium transition-all duration-200 focus:outline-none flex-shrink-0">
                    <i class="fas fa-handshake sm:mr-2"></i><span class="hidden sm:inline"> Affiliation</span>
                </button>
            </nav>
        </div>

        {{-- Tab Content --}}
        <div class="space-y-8">
            {{-- Profile Tab --}}
            <div x-show="tab === 'profile'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @csrf
                    <div class="col-span-1 md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Full Name</label>
                        <input type="text" name="full_name" value="{{ $profile->full_name ?? '' }}"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">NRIC</label>
                        <input type="text" name="nric" value="{{ $profile->nric ?? '' }}"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date of Birth</label>
                        <input type="date" name="dob" value="{{ $profile->dob ?? '' }}"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Phone</label>
                        <input type="text" name="phone" value="{{ $profile->phone ?? '' }}"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Alternative Email</label>
                        <input type="email" name="email_alt" value="{{ $profile->email_alt ?? '' }}"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                    </div>

                    {{-- Country Dropdown --}}
                    <div>
                        <label for="country-profile" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Country</label>
                        <select id="country-profile" name="country"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                            <option value="">-- Select Country --</option>
                        </select>
                    </div>

                    {{-- State Dropdown --}}
                    <div id="state-wrapper-profile" class="{{ ($profile->state ?? '') ? '' : 'hidden' }}">
                        <label for="state-profile" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">State</label>
                        <select id="state-profile" name="state"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                            <option value="">-- Select State --</option>
                        </select>
                    </div>

                    {{-- City Dropdown --}}
                    <div id="city-wrapper-profile" class="{{ ($profile->city ?? '') ? '' : 'hidden' }}">
                        <label for="city-profile" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">City</label>
                        <select id="city-profile" name="city"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                            <option value="">-- Select City --</option>
                        </select>
                    </div>

                    <div class="col-span-1 md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Home Address</label>
                        <textarea name="home_address" rows="3"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">{{ $profile->home_address ?? '' }}</textarea>
                    </div>

                    {{-- Profile Photo Upload with Preview --}}
                    <div class="col-span-1 md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Profile Photo</label>

                        {{-- Current Photo Display --}}
                        @if($profile->photo_path)
                            <div id="current-photo" class="mb-4">
                                <div class="flex items-start gap-4">
                                    <img src="{{ asset('storage/' . $profile->photo_path) }}"
                                         alt="Current Profile Photo"
                                         class="w-32 h-32 rounded-xl object-cover border-2 border-gray-200 dark:border-gray-600 shadow-md">
                                    <div class="flex flex-col gap-2">
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Current Photo</p>
                                        <button type="button"
                                                onclick="removeCurrentPhoto()"
                                                class="px-4 py-2 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 rounded-lg text-sm font-medium hover:bg-red-200 dark:hover:bg-red-900/50 transition-colors">
                                            <i class="fas fa-trash-alt mr-2"></i> Remove Photo
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Photo Upload Input --}}
                        <div id="upload-section">
                            <input type="file"
                                   id="photo-input"
                                   name="photo_path"
                                   accept="image/*"
                                   onchange="previewPhoto(event)"
                                   class="w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 dark:file:bg-indigo-900/30 file:text-indigo-700 dark:file:text-indigo-400 hover:file:bg-indigo-100 dark:hover:file:bg-indigo-900/50 cursor-pointer">
                            <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">PNG, JPG, GIF up to 2MB</p>
                        </div>

                        {{-- New Photo Preview --}}
                        <div id="photo-preview" class="hidden mt-4">
                            <div class="flex items-start gap-4">
                                <img id="preview-image"
                                     src=""
                                     alt="Photo Preview"
                                     class="w-32 h-32 rounded-xl object-cover border-2 border-gray-200 dark:border-gray-600 shadow-md">
                                <div class="flex flex-col gap-2">
                                    <p class="text-sm text-gray-600 dark:text-gray-400">New Photo Preview</p>
                                    <button type="button"
                                            onclick="cancelUpload()"
                                            class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg text-sm font-medium hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                                        <i class="fas fa-times mr-2"></i> Cancel
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- Hidden input for photo removal --}}
                        <input type="hidden" id="remove-photo-input" name="remove_photo" value="0">
                    </div>

                    <div class="col-span-1 md:col-span-2 flex justify-end">
                        <button type="submit" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-semibold shadow-lg transition-colors">
                            <i class="fas fa-save mr-2"></i> Save Profile
                        </button>
                    </div>
                </form>
            </div>

            {{-- Bank Tab --}}
            <div x-show="tab === 'bank'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                @php
                    $malaysianBanks = [
                        'Affin Bank', 'Agrobank', 'Alliance Bank', 'AmBank', 'Bank Islam',
                        'Bank Muamalat', 'Bank Rakyat', 'CIMB Bank', 'Hong Leong Bank',
                        'HSBC Bank', 'Maybank', 'Public Bank', 'RHB Bank', 'Standard Chartered',
                        'UOB Bank', 'OCBC Bank'
                    ];
                @endphp
                <form method="POST" action="{{ route('profile.bank') }}" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Bank Name</label>
                        <select name="bank_name"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                            <option value="">Select Bank</option>
                            @foreach($malaysianBanks as $bankName)
                                <option value="{{ $bankName }}" @if(($bank->bank_name ?? '') == $bankName) selected @endif>
                                    {{ $bankName }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Account Number</label>
                        <input type="text" name="account_number" value="{{ $bank->account_number ?? '' }}"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Account Holder</label>
                        <input type="text" name="account_holder" value="{{ $bank->account_holder ?? '' }}"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                    </div>
                    <div class="col-span-1 md:col-span-2 flex justify-end">
                        <button type="submit" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-semibold shadow-lg transition-colors">
                            <i class="fas fa-save mr-2"></i> Save Bank Details
                        </button>
                    </div>
                </form>
            </div>

            {{-- Password Tab --}}
            <div x-show="tab === 'password'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                <form method="POST" action="{{ route('profile.password') }}" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Old Password</label>
                        <input type="password" name="old_password"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">New Password</label>
                        <input type="password" name="new_password"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Confirm New Password</label>
                        <input type="password" name="new_password_confirmation"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                    </div>
                    <div class="col-span-1 md:col-span-2 flex justify-end">
                        <button type="submit" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-semibold shadow-lg transition-colors">
                            <i class="fas fa-key mr-2"></i> Update Password
                        </button>
                    </div>
                </form>
            </div>

            {{-- Business Tab --}}
            <div x-show="tab === 'business'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                <form method="POST" action="{{ route('profile.business') }}" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Company Name</label>
                        <input type="text" name="company_name" value="{{ $business->company_name ?? '' }}"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">SSM No</label>
                        <input type="text" name="ssm_no" value="{{ $business->ssm_no ?? '' }}"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                    </div>
                    <div class="col-span-1 md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Business Address</label>
                        <textarea name="business_address" rows="3"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">{{ $business->business_address ?? '' }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Industry</label>
                        <input type="text" name="industry" value="{{ $business->industry ?? '' }}"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Main Products/Services</label>
                        <input type="text" name="main_products_services" value="{{ $business->main_products_services ?? '' }}"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Business Model</label>
                        <input type="text" name="business_model" value="{{ $business->business_model ?? '' }}"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                    </div>
                    <div class="col-span-1 md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Achievements</label>
                        <textarea name="achievements" rows="3"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">{{ $business->achievements ?? '' }}</textarea>
                    </div>
                    <div class="col-span-1 md:col-span-2 flex justify-end">
                        <button type="submit" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-semibold shadow-lg transition-colors">
                            <i class="fas fa-save mr-2"></i> Save Business
                        </button>
                    </div>
                </form>
            </div>

            {{-- Education Tab --}}
            <div x-show="tab === 'education'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                <form method="POST" action="{{ route('profile.education') }}" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Primary Education</label>
                        <input type="text" name="primary" value="{{ $education->primary ?? '' }}"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Secondary Education</label>
                        <input type="text" name="secondary" value="{{ $education->secondary ?? '' }}"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Higher Education</label>
                        <input type="text" name="higher" value="{{ $education->higher ?? '' }}"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Skills Training</label>
                        <input type="text" name="skills_training" value="{{ $education->skills_training ?? '' }}"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                    </div>
                    <div class="col-span-1 md:col-span-2 flex justify-end">
                        <button type="submit" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-semibold shadow-lg transition-colors">
                            <i class="fas fa-save mr-2"></i> Save Education
                        </button>
                    </div>
                </form>
            </div>

            {{-- Courses Tab --}}
            <div x-show="tab === 'courses'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    {{-- Add New Course Form --}}
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-2xl p-6 border border-gray-200 dark:border-gray-600">
                        <h4 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">
                            <i class="fas fa-plus-circle text-indigo-600 dark:text-indigo-400 mr-2"></i> Add New Course
                        </h4>
                        <form method="POST" action="{{ route('profile.course') }}" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Title</label>
                                <input type="text" name="title"
                                    class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Provider</label>
                                <input type="text" name="provider"
                                    class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Year</label>
                                <input type="text" name="year"
                                    class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                            </div>
                            <div class="flex justify-end">
                                <button type="submit" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-semibold shadow-lg transition-colors">
                                    <i class="fas fa-plus mr-2"></i> Add Course
                                </button>
                            </div>
                        </form>
                    </div>

                    {{-- My Courses List --}}
                    <div>
                        <h4 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">
                            <i class="fas fa-list text-indigo-600 dark:text-indigo-400 mr-2"></i> My Courses
                        </h4>
                        <div class="space-y-3">
                            @forelse($courses as $c)
                                <div class="p-4 bg-white dark:bg-gray-700 rounded-xl border border-gray-200 dark:border-gray-600 hover:shadow-md transition-shadow">
                                    <div class="font-semibold text-gray-800 dark:text-white">{{ $c->title }}</div>
                                    <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                        <i class="fas fa-building mr-1"></i> {{ $c->provider }}
                                        <span class="mx-2">|</span>
                                        <i class="fas fa-calendar mr-1"></i> {{ $c->year }}
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                                    <i class="fas fa-book-open text-4xl mb-3 opacity-50"></i>
                                    <p>No courses added yet</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            {{-- Pewaris Tab --}}
            <div x-show="tab === 'pewaris'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                <form method="POST" action="{{ route('profile.pewaris.store') }}" class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Name</label>
                        <input type="text" name="name"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Relationship</label>
                        <input type="text" name="relationship"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Phone</label>
                        <input type="text" name="phone"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email</label>
                        <input type="email" name="email"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date of Birth</label>
                        <input type="date" name="dob"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                    </div>
                    <div class="col-span-1 md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Address</label>
                        <textarea name="address" rows="3"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"></textarea>
                    </div>
                    <div class="col-span-1 md:col-span-2 flex justify-end">
                        <button type="submit" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-semibold shadow-lg transition-colors">
                            <i class="fas fa-user-plus mr-2"></i> Add Next of Kin
                        </button>
                    </div>
                </form>

                <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4">
                    <i class="fas fa-users text-indigo-600 dark:text-indigo-400 mr-2"></i> My Next of Kin / Pewaris
                </h3>
                <div class="space-y-3">
                    @forelse($pewaris as $nk)
                        <div class="p-4 bg-white dark:bg-gray-700 rounded-xl border border-gray-200 dark:border-gray-600 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                            <div>
                                <div class="font-semibold text-gray-800 dark:text-white">{{ $nk->name }} <span class="text-sm font-normal text-gray-500 dark:text-gray-400">({{ $nk->relationship }})</span></div>
                                <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                    <i class="fas fa-phone mr-1"></i> {{ $nk->phone }}
                                    <span class="mx-2">|</span>
                                    <i class="fas fa-envelope mr-1"></i> {{ $nk->email }}
                                    @if($nk->dob)
                                        <span class="mx-2">|</span>
                                        <i class="fas fa-birthday-cake mr-1"></i> {{ $nk->dob->format('d M Y') }} ({{ $nk->age }} years)
                                    @endif
                                </div>
                                @if($nk->dob && $nk->isEligibleForRemaja())
                                    <div class="mt-2">
                                        <span class="px-2 py-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 text-xs font-semibold rounded-lg">
                                            <i class="fas fa-check-circle mr-1"></i> Eligible for Sandbox Remaja
                                        </span>
                                    </div>
                                @endif
                            </div>
                            <div class="flex items-center gap-2">
                                @if($nk->linked_user_id && $nk->isEligibleForRemaja())
                                    <form action="#">
                                        <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-xl text-sm font-semibold transition-colors">
                                            <i class="fas fa-user-plus mr-2"></i> Register Sandbox Remaja
                                        </button>
                                    </form>
                                @endif
                                <form action="{{ route('profile.pewaris.destroy', $nk->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this next of kin? This action cannot be undone.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-4 py-2 bg-red-100 dark:bg-red-900/30 hover:bg-red-200 dark:hover:bg-red-900/50 text-red-700 dark:text-red-400 rounded-xl text-sm font-semibold transition-colors">
                                        <i class="fas fa-trash-alt mr-2"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                            <i class="fas fa-users text-4xl mb-3 opacity-50"></i>
                            <p>No next of kin added yet</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Affiliation Tab --}}
            <div x-show="tab === 'affiliation'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                <form method="POST" action="{{ route('profile.affiliation') }}" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Organization</label>
                        <input type="text" name="organization" value="{{ $affiliation->organization ?? '' }}"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Position</label>
                        <input type="text" name="position" value="{{ $affiliation->position ?? '' }}"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                    </div>
                    <div class="col-span-1 md:col-span-2 flex justify-end">
                        <button type="submit" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-semibold shadow-lg transition-colors">
                            <i class="fas fa-save mr-2"></i> Save Affiliation
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    // Preview uploaded photo
    function previewPhoto(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('preview-image').src = e.target.result;
                document.getElementById('photo-preview').classList.remove('hidden');
            }
            reader.readAsDataURL(file);
        }
    }

    // Cancel new photo upload
    function cancelUpload() {
        document.getElementById('photo-input').value = '';
        document.getElementById('photo-preview').classList.add('hidden');
    }

    // Remove current photo
    function removeCurrentPhoto() {
        if (confirm('Are you sure you want to remove your profile photo?')) {
            document.getElementById('remove-photo-input').value = '1';
            document.getElementById('current-photo').style.opacity = '0.5';
            document.getElementById('current-photo').innerHTML = '<p class="text-sm text-red-600 dark:text-red-400"><i class="fas fa-info-circle mr-2"></i>Photo will be removed when you save</p>';
        }
    }
    </script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        let profileData = {};
        const savedCountry = "{{ $profile->country ?? '' }}";
        const savedState = "{{ $profile->state ?? '' }}";
        const savedCity = "{{ $profile->city ?? '' }}";

        $.getJSON("{{ asset('select.json') }}", function(response) {
            profileData = response;

            // Populate countries
            $.each(profileData, function(country) {
                $("#country-profile").append(new Option(country, country));
            });

            // If there's a saved country, select it and load states
            if (savedCountry) {
                $("#country-profile").val(savedCountry);
                loadStates(savedCountry);
            }
        });

        function loadStates(country) {
            let states = profileData[country] || {};

            $("#state-profile").empty().append(new Option("-- Select State --", ""));
            $("#city-profile").empty().append(new Option("-- Select City --", ""));
            $("#city-wrapper-profile").addClass("hidden");

            if (country === "Malaysia") {
                $("#state-wrapper-profile").removeClass("hidden");
                $.each(states, function(state) {
                    $("#state-profile").append(new Option(state, state));
                });

                // If there's a saved state, select it and load cities
                if (savedState) {
                    $("#state-profile").val(savedState);
                    loadCities(country, savedState);
                }
            } else {
                $("#state-wrapper-profile").addClass("hidden");
                $("#city-wrapper-profile").addClass("hidden");
            }
        }

        function loadCities(country, state) {
            let cities = profileData[country][state] || [];

            $("#city-profile").empty().append(new Option("-- Select City --", ""));

            if (cities.length > 0) {
                $("#city-wrapper-profile").removeClass("hidden");
                $.each(cities, function(i, city) {
                    $("#city-profile").append(new Option(city, city));
                });

                // If there's a saved city, select it
                if (savedCity) {
                    $("#city-profile").val(savedCity);
                }
            } else {
                $("#city-wrapper-profile").addClass("hidden");
            }
        }

        // Handle country change
        $("#country-profile").on("change", function() {
            let country = $(this).val();
            let states = profileData[country] || {};

            $("#state-profile").empty().append(new Option("-- Select State --", ""));
            $("#city-profile").empty().append(new Option("-- Select City --", ""));
            $("#city-wrapper-profile").addClass("hidden");

            if (country === "Malaysia") {
                $("#state-wrapper-profile").removeClass("hidden");
                $.each(states, function(state) {
                    $("#state-profile").append(new Option(state, state));
                });
            } else {
                $("#state-wrapper-profile").addClass("hidden");
                $("#city-wrapper-profile").addClass("hidden");
            }
        });

        // Handle state change
        $("#state-profile").on("change", function() {
            let country = $("#country-profile").val();
            let state = $(this).val();
            let cities = profileData[country][state] || [];

            $("#city-profile").empty().append(new Option("-- Select City --", ""));

            if (cities.length > 0) {
                $("#city-wrapper-profile").removeClass("hidden");
                $.each(cities, function(i, city) {
                    $("#city-profile").append(new Option(city, city));
                });
            } else {
                $("#city-wrapper-profile").addClass("hidden");
            }
        });
    </script>
    @endpush
</x-admin-layout>
