<x-admin-layout>
    <x-slot name="pageTitle">My Profile</x-slot>
    <x-slot name="breadcrumb">View and manage your profile information</x-slot>

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

    {{-- Profile Header Card --}}
    <div class="bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 rounded-2xl p-6 sm:p-8 mb-6 shadow-xl relative overflow-hidden">
        <div class="absolute inset-0 opacity-10">
            <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                <pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse">
                    <path d="M 10 0 L 0 0 0 10" fill="none" stroke="white" stroke-width="0.5"/>
                </pattern>
                <rect width="100" height="100" fill="url(#grid)" />
            </svg>
        </div>
        <div class="relative flex flex-col sm:flex-row items-center gap-6">
            {{-- Profile Photo --}}
            <div class="flex-shrink-0">
                @if($profile->photo_path)
                    <img src="{{ asset('storage/' . $profile->photo_path) }}"
                         alt="Profile Photo"
                         class="w-28 h-28 sm:w-32 sm:h-32 rounded-2xl object-cover border-4 border-white/30 shadow-lg">
                @else
                    <div class="w-28 h-28 sm:w-32 sm:h-32 rounded-2xl bg-white/20 flex items-center justify-center border-4 border-white/30">
                        <span class="text-4xl sm:text-5xl font-bold text-white">
                            {{ strtoupper(substr($profile->full_name ?? Auth::user()->name, 0, 1)) }}
                        </span>
                    </div>
                @endif
            </div>

            {{-- Profile Info --}}
            <div class="flex-1 text-center sm:text-left">
                <h2 class="text-2xl sm:text-3xl font-bold text-white mb-1">
                    {{ $profile->full_name ?? Auth::user()->name }}
                </h2>
                <p class="text-white/80 mb-2">{{ Auth::user()->email }}</p>
                @if($profile->phone)
                    <p class="text-white/70 text-sm">
                        <i class="fas fa-phone mr-2"></i>{{ $profile->phone }}
                    </p>
                @endif
                <div class="mt-3 flex flex-wrap gap-2 justify-center sm:justify-start">
                    @if($profile->country)
                        <span class="px-3 py-1 bg-white/20 rounded-full text-white text-sm">
                            <i class="fas fa-globe mr-1"></i>{{ $profile->country }}
                        </span>
                    @endif
                    @if($profile->state)
                        <span class="px-3 py-1 bg-white/20 rounded-full text-white text-sm">
                            <i class="fas fa-map-marker-alt mr-1"></i>{{ $profile->state }}
                        </span>
                    @endif
                </div>
            </div>

            {{-- Edit Button --}}
            <a href="{{ route('profile.edit') }}"
               class="px-6 py-3 bg-white text-indigo-600 rounded-xl font-bold shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-300 flex items-center gap-2">
                <i class="fas fa-edit"></i>
                <span>Edit Profile</span>
            </a>
        </div>
    </div>

    {{-- Main Content Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        {{-- Personal Information --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-100 dark:border-gray-700 shadow-sm">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-10 h-10 rounded-xl bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center">
                    <i class="fas fa-user text-indigo-600 dark:text-indigo-400"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Personal Information</h3>
            </div>
            <div class="space-y-4">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Full Name</p>
                    <p class="font-medium text-gray-900 dark:text-white">{{ $profile->full_name ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">NRIC</p>
                    <p class="font-medium text-gray-900 dark:text-white">{{ $profile->nric ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Date of Birth</p>
                    <p class="font-medium text-gray-900 dark:text-white">
                        {{ $profile->dob ? \Carbon\Carbon::parse($profile->dob)->format('d M Y') : '-' }}
                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Phone</p>
                    <p class="font-medium text-gray-900 dark:text-white">{{ $profile->phone ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Alternative Email</p>
                    <p class="font-medium text-gray-900 dark:text-white">{{ $profile->email_alt ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Address</p>
                    <p class="font-medium text-gray-900 dark:text-white">{{ $profile->home_address ?? '-' }}</p>
                </div>
            </div>
        </div>

        {{-- Bank Information --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-100 dark:border-gray-700 shadow-sm">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-10 h-10 rounded-xl bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                    <i class="fas fa-university text-green-600 dark:text-green-400"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Bank Details</h3>
            </div>
            <div class="space-y-4">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Bank Name</p>
                    <p class="font-medium text-gray-900 dark:text-white">{{ $bank->bank_name ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Account Number</p>
                    <p class="font-medium text-gray-900 dark:text-white font-mono">{{ $bank->account_number ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Account Holder</p>
                    <p class="font-medium text-gray-900 dark:text-white">{{ $bank->account_holder ?? '-' }}</p>
                </div>
            </div>
        </div>

        {{-- Business Information --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-100 dark:border-gray-700 shadow-sm">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-10 h-10 rounded-xl bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center">
                    <i class="fas fa-briefcase text-purple-600 dark:text-purple-400"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Business</h3>
            </div>
            <div class="space-y-4">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Company Name</p>
                    <p class="font-medium text-gray-900 dark:text-white">{{ $business->company_name ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">SSM No</p>
                    <p class="font-medium text-gray-900 dark:text-white">{{ $business->ssm_no ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Industry</p>
                    <p class="font-medium text-gray-900 dark:text-white">{{ $business->industry ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Business Address</p>
                    <p class="font-medium text-gray-900 dark:text-white">{{ $business->business_address ?? '-' }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Second Row --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        {{-- Education --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-100 dark:border-gray-700 shadow-sm">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-10 h-10 rounded-xl bg-yellow-100 dark:bg-yellow-900/30 flex items-center justify-center">
                    <i class="fas fa-graduation-cap text-yellow-600 dark:text-yellow-400"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Education</h3>
            </div>
            <div class="space-y-4">
                <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                    <span class="text-gray-600 dark:text-gray-400">Primary</span>
                    <span class="font-medium text-gray-900 dark:text-white">{{ $education->primary ?? '-' }}</span>
                </div>
                <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                    <span class="text-gray-600 dark:text-gray-400">Secondary</span>
                    <span class="font-medium text-gray-900 dark:text-white">{{ $education->secondary ?? '-' }}</span>
                </div>
                <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                    <span class="text-gray-600 dark:text-gray-400">Higher</span>
                    <span class="font-medium text-gray-900 dark:text-white">{{ $education->higher ?? '-' }}</span>
                </div>
                <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                    <span class="text-gray-600 dark:text-gray-400">Skills Training</span>
                    <span class="font-medium text-gray-900 dark:text-white">{{ $education->skills_training ?? '-' }}</span>
                </div>
            </div>
        </div>

        {{-- Affiliation --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-100 dark:border-gray-700 shadow-sm">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-10 h-10 rounded-xl bg-pink-100 dark:bg-pink-900/30 flex items-center justify-center">
                    <i class="fas fa-handshake text-pink-600 dark:text-pink-400"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Affiliation</h3>
            </div>
            <div class="space-y-4">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Organization</p>
                    <p class="font-medium text-gray-900 dark:text-white">{{ $affiliation->organization ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Position</p>
                    <p class="font-medium text-gray-900 dark:text-white">{{ $affiliation->position ?? '-' }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Courses Section --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-100 dark:border-gray-700 shadow-sm mb-6">
        <div class="flex items-center justify-between mb-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                    <i class="fas fa-book text-blue-600 dark:text-blue-400"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Courses & Certifications</h3>
            </div>
            <span class="px-3 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 rounded-full text-sm font-medium">
                {{ $courses->count() }} courses
            </span>
        </div>
        @if($courses->isEmpty())
            <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                <i class="fas fa-book-open text-4xl mb-3 opacity-50"></i>
                <p>No courses added yet</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($courses as $course)
                    <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl border border-gray-200 dark:border-gray-600">
                        <h4 class="font-semibold text-gray-900 dark:text-white mb-1">{{ $course->title }}</h4>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            <i class="fas fa-building mr-1"></i> {{ $course->provider }}
                        </p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            <i class="fas fa-calendar mr-1"></i> {{ $course->year }}
                        </p>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Next of Kin / Pewaris Section --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-100 dark:border-gray-700 shadow-sm mb-6">
        <div class="flex items-center justify-between mb-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center">
                    <i class="fas fa-users text-orange-600 dark:text-orange-400"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Next of Kin / Pewaris</h3>
            </div>
            <span class="px-3 py-1 bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-400 rounded-full text-sm font-medium">
                {{ $pewaris->count() }} registered
            </span>
        </div>
        @if($pewaris->isEmpty())
            <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                <i class="fas fa-users text-4xl mb-3 opacity-50"></i>
                <p>No next of kin added yet</p>
            </div>
        @else
            <div class="space-y-4">
                @foreach($pewaris as $nk)
                    <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl border border-gray-200 dark:border-gray-600">
                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-3">
                            <div>
                                <h4 class="font-semibold text-gray-900 dark:text-white">
                                    {{ $nk->name }}
                                    <span class="text-sm font-normal text-gray-500 dark:text-gray-400">({{ $nk->relationship }})</span>
                                </h4>
                                <div class="flex flex-wrap gap-4 mt-2 text-sm text-gray-600 dark:text-gray-400">
                                    @if($nk->phone)
                                        <span><i class="fas fa-phone mr-1"></i>{{ $nk->phone }}</span>
                                    @endif
                                    @if($nk->email)
                                        <span><i class="fas fa-envelope mr-1"></i>{{ $nk->email }}</span>
                                    @endif
                                    @if($nk->dob)
                                        <span><i class="fas fa-birthday-cake mr-1"></i>{{ $nk->dob->format('d M Y') }} ({{ $nk->age }} years)</span>
                                    @endif
                                </div>
                            </div>
                            @if($nk->dob && $nk->isEligibleForRemaja())
                                <span class="px-3 py-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 text-xs font-semibold rounded-lg whitespace-nowrap">
                                    <i class="fas fa-check-circle mr-1"></i> Eligible for Sandbox Remaja
                                </span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Account & Subscription History Section --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-100 dark:border-gray-700 shadow-sm">
        <div class="flex items-center gap-3 mb-5">
            <div class="w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center">
                <i class="fas fa-file-invoice text-emerald-600 dark:text-emerald-400"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Account & Subscription History</h3>
        </div>

        {{-- Account Status --}}
        <div class="mb-6">
            <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 uppercase tracking-wider">My Accounts</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($accounts as $account)
                    <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl border border-gray-200 dark:border-gray-600">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-3">
                                <img src="{{ $account->type === 'rizqmall' ? asset('rizqmall.jpeg') : asset('sandboxlogo.png') }}"
                                     alt="{{ ucfirst($account->type) }}"
                                     class="w-10 h-10 rounded-lg object-cover">
                                <div>
                                    <h5 class="font-semibold text-gray-900 dark:text-white">
                                        @if($account->type === 'sandbox')
                                            Sandbox {{ ucfirst($account->subtype ?? 'Usahawan') }}
                                        @else
                                            {{ ucfirst($account->type) }}
                                        @endif
                                    </h5>
                                    @if($account->serial_number)
                                        <p class="text-xs font-mono text-indigo-600 dark:text-indigo-400">{{ $account->serial_number }}</p>
                                    @endif
                                </div>
                            </div>
                            <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $account->active ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400' : 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400' }}">
                                {{ $account->active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                        @if($account->expires_at)
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                @if(\Carbon\Carbon::parse($account->expires_at)->isPast())
                                    <i class="fas fa-exclamation-triangle text-orange-500 mr-1"></i>
                                    Expired on {{ \Carbon\Carbon::parse($account->expires_at)->format('d M Y') }}
                                @else
                                    <i class="fas fa-calendar-check text-green-500 mr-1"></i>
                                    Valid until {{ \Carbon\Carbon::parse($account->expires_at)->format('d M Y') }}
                                @endif
                            </p>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Subscription History --}}
        <div>
            <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 uppercase tracking-wider">Subscription History</h4>
            @if($subscriptions->isEmpty())
                <div class="text-center py-8 text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                    <i class="fas fa-receipt text-4xl mb-3 opacity-50"></i>
                    <p>No subscription history yet</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="text-left text-sm font-semibold text-gray-500 dark:text-gray-400 border-b border-gray-200 dark:border-gray-700">
                                <th class="pb-3 px-4">Plan</th>
                                <th class="pb-3 px-4">Amount</th>
                                <th class="pb-3 px-4">Status</th>
                                <th class="pb-3 px-4">Progress</th>
                                <th class="pb-3 px-4">Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach($subscriptions as $sub)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                    <td class="py-3 px-4">
                                        <div class="flex items-center gap-2">
                                            <img src="{{ $sub->plan === 'rizqmall' ? asset('rizqmall.jpeg') : asset('sandboxlogo.png') }}"
                                                 alt="{{ $sub->plan }}"
                                                 class="w-8 h-8 rounded-lg object-cover">
                                            <span class="font-medium text-gray-900 dark:text-white">{{ ucfirst($sub->plan) }}</span>
                                        </div>
                                    </td>
                                    <td class="py-3 px-4 font-medium text-gray-900 dark:text-white">
                                        RM {{ number_format($sub->amount / 100, 2) }}
                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full
                                            @if($sub->status === 'completed' || $sub->status === 'active') bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400
                                            @elseif($sub->status === 'pending') bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400
                                            @else bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300
                                            @endif">
                                            {{ ucfirst($sub->status) }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-sm text-gray-600 dark:text-gray-400">
                                        @if($sub->installments_total > 1)
                                            {{ $sub->installments_paid }}/{{ $sub->installments_total }} installments
                                        @else
                                            Full payment
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-sm text-gray-600 dark:text-gray-400">
                                        {{ $sub->created_at->format('d M Y') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

</x-admin-layout>
