<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-3xl text-gray-900 leading-tight">
            My Profile
        </h2>
        <p class="mt-2 text-sm text-gray-500">
            Update your personal, business, and educational information.
        </p>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Success/Error Message --}}
            @if(session('success'))
                <div class="mb-6 p-4 rounded-xl bg-green-50 text-green-700 font-semibold shadow-sm">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-6 p-4 rounded-xl bg-red-50 text-red-700 font-semibold shadow-sm">
                    {{ session('error') }}
                </div>
            @endif
              @if ($errors->any())
                  <div class="mb-6 p-4 rounded-xl bg-red-900 text-red-200">
                      <ul class="list-disc list-inside">
                          @foreach ($errors->all() as $error)
                              <li>{{ $error }}</li>
                          @endforeach
                      </ul>
                  </div>
              @endif

            {{-- Tabs --}}
            <div x-data="{ tab: 'profile' }" class="bg-white rounded-2xl shadow-lg p-6">
                <div class="mb-6">
                    <nav class="flex flex-wrap gap-x-2 gap-y-2" aria-label="Tabs">
                        <button @click="tab = 'profile'"
                            :class="tab === 'profile' ? 'bg-indigo-600 text-white shadow-md' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-800'"
                            class="whitespace-nowrap rounded-full px-4 py-2 text-sm font-medium transition-colors duration-200 focus:outline-none">
                            Personal Profile
                        </button>
                        <button @click="tab = 'bank'"
                            :class="tab === 'bank' ? 'bg-indigo-600 text-white shadow-md' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-800'"
                            class="whitespace-nowrap rounded-full px-4 py-2 text-sm font-medium transition-colors duration-200 focus:outline-none">
                            Bank Details
                        </button>
                        <button @click="tab = 'password'"
                            :class="tab === 'password' ? 'bg-indigo-600 text-white shadow-md' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-800'"
                            class="whitespace-nowrap rounded-full px-4 py-2 text-sm font-medium transition-colors duration-200 focus:outline-none">
                            Password
                        </button>
                        <button @click="tab = 'business'"
                            :class="tab === 'business' ? 'bg-indigo-600 text-white shadow-md' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-800'"
                            class="whitespace-nowrap rounded-full px-4 py-2 text-sm font-medium transition-colors duration-200 focus:outline-none">
                            Business
                        </button>
                        <button @click="tab = 'education'"
                            :class="tab === 'education' ? 'bg-indigo-600 text-white shadow-md' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-800'"
                            class="whitespace-nowrap rounded-full px-4 py-2 text-sm font-medium transition-colors duration-200 focus:outline-none">
                            Education
                        </button>
                        <button @click="tab = 'courses'"
                            :class="tab === 'courses' ? 'bg-indigo-600 text-white shadow-md' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-800'"
                            class="whitespace-nowrap rounded-full px-4 py-2 text-sm font-medium transition-colors duration-200 focus:outline-none">
                            Courses
                        </button>
                        <button @click="tab = 'pewaris'"
                            :class="tab === 'pewaris' ? 'bg-indigo-600 text-white shadow-md' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-800'"
                            class="whitespace-nowrap rounded-full px-4 py-2 text-sm font-medium transition-colors duration-200 focus:outline-none">
                            Next of Kin
                        </button>
                        <button @click="tab = 'affiliation'"
                            :class="tab === 'affiliation' ? 'bg-indigo-600 text-white shadow-md' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-800'"
                            class="whitespace-nowrap rounded-full px-4 py-2 text-sm font-medium transition-colors duration-200 focus:outline-none">
                            Affiliation
                        </button>
                    </nav>
                </div>

                {{-- Tab Content (Forms remain the same) --}}
                <div class="space-y-8">
                    {{-- Profile Tab --}}
                    <div x-show="tab === 'profile'">
    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @csrf
        <div class="col-span-1 md:col-span-2">
            <label class="block text-sm font-medium text-gray-700">Full Name</label>
            <input type="text" name="full_name" value="{{ $profile->full_name ?? '' }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">NRIC</label>
            <input type="text" name="nric" value="{{ $profile->nric ?? '' }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Date of Birth</label>
            <input type="date" name="dob" value="{{ $profile->dob ?? '' }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Phone</label>
            <input type="text" name="phone" value="{{ $profile->phone ?? '' }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Alternative Email</label>
            <input type="email" name="email_alt" value="{{ $profile->email_alt ?? '' }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        </div>

        {{-- Country Dropdown --}}
        <div>
            <label for="country-profile" class="block text-sm font-medium text-gray-700">Country</label>
            <select id="country-profile" name="country" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="">-- Select Country --</option>
            </select>
        </div>

        {{-- State Dropdown --}}
        <div id="state-wrapper-profile" class="{{ ($profile->state ?? '') ? '' : 'hidden' }}">
            <label for="state-profile" class="block text-sm font-medium text-gray-700">State</label>
            <select id="state-profile" name="state" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="">-- Select State --</option>
            </select>
        </div>

        {{-- City Dropdown --}}
        <div id="city-wrapper-profile" class="{{ ($profile->city ?? '') ? '' : 'hidden' }}">
            <label for="city-profile" class="block text-sm font-medium text-gray-700">City</label>
            <select id="city-profile" name="city" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="">-- Select City --</option>
            </select>
        </div>

        <div class="col-span-1 md:col-span-2">
            <label class="block text-sm font-medium text-gray-700">Home Address</label>
            <textarea name="home_address" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ $profile->home_address ?? '' }}</textarea>
        </div>

        {{-- Profile Photo Upload with Preview --}}
        <div class="col-span-1 md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-2">Profile Photo</label>
            
            {{-- Current Photo Display --}}
            @if($profile->photo_path)
                <div id="current-photo" class="mb-4">
                    <div class="flex items-start gap-4">
                        <img src="{{ asset('storage/' . $profile->photo_path) }}" 
                             alt="Current Profile Photo" 
                             class="w-32 h-32 rounded-lg object-cover border border-gray-300 shadow-sm">
                        <div class="flex flex-col gap-2">
                            <p class="text-sm text-gray-600">Current Photo</p>
                            <button type="button" 
                                    onclick="removeCurrentPhoto()"
                                    class="px-3 py-1 bg-red-100 text-red-700 rounded-md text-sm font-medium hover:bg-red-200 transition">
                                Remove Photo
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
                       class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer">
                <p class="mt-1 text-xs text-gray-500">PNG, JPG, GIF up to 2MB</p>
            </div>

            {{-- New Photo Preview --}}
            <div id="photo-preview" class="hidden mt-4">
                <div class="flex items-start gap-4">
                    <img id="preview-image" 
                         src="" 
                         alt="Photo Preview" 
                         class="w-32 h-32 rounded-lg object-cover border border-gray-300 shadow-sm">
                    <div class="flex flex-col gap-2">
                        <p class="text-sm text-gray-600">New Photo Preview</p>
                        <button type="button" 
                                onclick="cancelUpload()"
                                class="px-3 py-1 bg-gray-100 text-gray-700 rounded-md text-sm font-medium hover:bg-gray-200 transition">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>

            {{-- Hidden input for photo removal --}}
            <input type="hidden" id="remove-photo-input" name="remove_photo" value="0">
        </div>

        <div class="col-span-1 md:col-span-2 flex justify-end">
            <x-primary-button>Save Profile</x-primary-button>
        </div>
    </form>
</div>
                    
                    <div x-show="tab === 'bank'">
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
                                <label class="block text-sm font-medium text-gray-700">Bank Name</label>
                                <select name="bank_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Select Bank</option>
                                    @foreach($malaysianBanks as $bankName)
                                        <option value="{{ $bankName }}" @if(($bank->bank_name ?? '') == $bankName) selected @endif>
                                            {{ $bankName }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Account Number</label>
                                <input type="text" name="account_number" value="{{ $bank->account_number ?? '' }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Account Holder</label>
                                <input type="text" name="account_holder" value="{{ $bank->account_holder ?? '' }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div class="col-span-1 md:col-span-2 flex justify-end">
                                <x-primary-button>Save Bank Details</x-primary-button>
                            </div>
                        </form>
                    </div>

                    {{-- Password Tab --}}
                    <div x-show="tab === 'password'">
                        <form method="POST" action="{{ route('profile.password') }}" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @csrf
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Old Password</label>
                                <input type="password" name="old_password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">New Password</label>
                                <input type="password" name="new_password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Confirm New Password</label>
                                <input type="password" name="new_password_confirmation" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div class="col-span-1 md:col-span-2 flex justify-end">
                                <x-primary-button>Update Password</x-primary-button>
                            </div>
                        </form>
                    </div>

                    {{-- Business Tab --}}
                    <div x-show="tab === 'business'">
                        <form method="POST" action="{{ route('profile.business') }}" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @csrf
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Company Name</label>
                                <input type="text" name="company_name" value="{{ $business->company_name ?? '' }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">SSM No</label>
                                <input type="text" name="ssm_no" value="{{ $business->ssm_no ?? '' }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div class="col-span-1 md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700">Business Address</label>
                                <textarea name="business_address" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ $business->business_address ?? '' }}</textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Industry</label>
                                <input type="text" name="industry" value="{{ $business->industry ?? '' }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Main Products/Services</label>
                                <input type="text" name="main_products_services" value="{{ $business->main_products_services ?? '' }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Business Model</label>
                                <input type="text" name="business_model" value="{{ $business->business_model ?? '' }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div class="col-span-1 md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700">Achievements</label>
                                <textarea name="achievements" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ $business->achievements ?? '' }}</textarea>
                            </div>
                            <div class="col-span-1 md:col-span-2 flex justify-end">
                                <x-primary-button>Save Business</x-primary-button>
                            </div>
                        </form>
                    </div>

                    {{-- Education Tab --}}
                    <div x-show="tab === 'education'">
                        <form method="POST" action="{{ route('profile.education') }}" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @csrf
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Primary Education</label>
                                <input type="text" name="primary" value="{{ $education->primary ?? '' }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Secondary Education</label>
                                <input type="text" name="secondary" value="{{ $education->secondary ?? '' }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Higher Education</label>
                                <input type="text" name="higher" value="{{ $education->higher ?? '' }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Skills Training</label>
                                <input type="text" name="skills_training" value="{{ $education->skills_training ?? '' }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div class="col-span-1 md:col-span-2 flex justify-end">
                                <x-primary-button>Save Education</x-primary-button>
                            </div>
                        </form>
                    </div>

                    {{-- Courses Tab --}}
                    <div x-show="tab === 'courses'">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Add New Course Form --}}
                            <div class="col-span-1">
                                <form method="POST" action="{{ route('profile.course') }}" class="space-y-4 p-6 bg-gray-50 rounded-xl shadow-inner">
                                    <h4 class="text-md font-semibold text-gray-800">Add New Course</h4>
                                    @csrf
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Title</label>
                                        <input type="text" name="title" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Provider</label>
                                        <input type="text" name="provider" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Year</label>
                                        <input type="text" name="year" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    </div>
                                    <div class="flex justify-end">
                                        <x-primary-button>Add Course</x-primary-button>
                                    </div>
                                </form>
                            </div>

                            {{-- My Courses List --}}
                            <div class="col-span-1">
                                <h3 class="text-md font-semibold text-gray-800 mb-2">My Courses</h3>
                                <ul class="space-y-4">
                                    @foreach($courses as $c)
                                        <li class="p-4 bg-gray-50 rounded-xl shadow-sm hover:shadow-md transition-shadow duration-200">
                                            <div class="font-bold text-gray-800">{{ $c->title }}</div>
                                            <div class="text-sm text-gray-600">{{ $c->provider }} ({{ $c->year }})</div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div x-show="tab === 'pewaris'">
                        <form method="POST" action="{{ route('profile.pewaris.store') }}" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @csrf
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Name</label>
                                <input type="text" name="name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Relationship</label>
                                <input type="text" name="relationship" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Phone</label>
                                <input type="text" name="phone" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Email</label>
                                <input type="email" name="email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Date of Birth</label>
                                <input type="date" name="dob" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div class="col-span-1 md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700">Address</label>
                                <textarea name="address" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                            </div>
                            <div class="col-span-1 md:col-span-2 flex justify-end">
                                <x-primary-button>Add Pewaris</x-primary-button>
                            </div>
                        </form>
                    
                        <h3 class="text-lg font-bold mt-6 mb-2">My Next of Kin / Pewaris</h3>
                        <ul class="space-y-3">
                            @foreach($pewaris as $nk)
                                <li class="p-4 border rounded-lg flex justify-between items-center">
                                    <div>
                                        <div class="font-semibold">{{ $nk->name }} ({{ $nk->relationship }})</div>
                                        <div class="text-sm text-gray-600">{{ $nk->phone }} - {{ $nk->email }}</div>
                                    </div>
                                    @if($nk->linked_user_id)
                                        <form action="#">
                                            
                                            <x-primary-button>Register Sandbox Remaja</x-primary-button>
                                        </form>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    
                    
                    {{-- Affiliation Tab --}}
                    <div x-show="tab === 'affiliation'">
                        <form method="POST" action="{{ route('profile.affiliation') }}" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @csrf
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Organization</label>
                                <input type="text" name="organization" value="{{ $affiliation->organization ?? '' }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Position</label>
                                <input type="text" name="position" value="{{ $affiliation->position ?? '' }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div class="col-span-1 md:col-span-2 flex justify-end">
                                <x-primary-button>Save Affiliation</x-primary-button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
            document.getElementById('current-photo').innerHTML = '<p class="text-sm text-red-600">Photo will be removed when you save</p>';
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
</x-app-layout>