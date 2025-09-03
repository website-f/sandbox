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

            {{-- Tabs --}}
            <div x-data="{ tab: 'profile' }" class="bg-white rounded-2xl shadow-lg p-6">
                <div class="mb-6">
                    <nav class="flex flex-wrap gap-x-2 gap-y-2" aria-label="Tabs">
                        <button @click="tab = 'profile'"
                            :class="tab === 'profile' ? 'bg-indigo-600 text-white shadow-md' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-800'"
                            class="whitespace-nowrap rounded-full px-4 py-2 text-sm font-medium transition-colors duration-200 focus:outline-none">
                            Personal Profile
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
                        <button @click="tab = 'nextofkin'"
                            :class="tab === 'nextofkin' ? 'bg-indigo-600 text-white shadow-md' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-800'"
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
                        <form method="POST" action="{{ route('profile.update') }}" class="grid grid-cols-1 md:grid-cols-2 gap-6">
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
                            <div class="col-span-1 md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700">Home Address</label>
                                <textarea name="home_address" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ $profile->home_address ?? '' }}</textarea>
                            </div>
                            <div class="col-span-1 md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700">Profile Photo</label>
                                <input type="file" name="photo_path" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                            </div>
                            <div class="col-span-1 md:col-span-2 flex justify-end">
                                <x-primary-button>Save Profile</x-primary-button>
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

                    {{-- Next of Kin Tab --}}
                    <div x-show="tab === 'nextofkin'">
                        <form method="POST" action="{{ route('profile.nextofkin') }}" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @csrf
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Name</label>
                                <input type="text" name="name" value="{{ $nextOfKin->name ?? '' }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Relationship</label>
                                <input type="text" name="relationship" value="{{ $nextOfKin->relationship ?? '' }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Phone</label>
                                <input type="text" name="phone" value="{{ $nextOfKin->phone ?? '' }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div class="col-span-1 md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700">Address</label>
                                <textarea name="address" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ $nextOfKin->address ?? '' }}</textarea>
                            </div>
                            <div class="col-span-1 md:col-span-2 flex justify-end">
                                <x-primary-button>Save Next of Kin</x-primary-button>
                            </div>
                        </form>
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
</x-app-layout>