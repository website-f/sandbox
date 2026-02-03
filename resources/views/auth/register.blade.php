<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Sandbox</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .card-shadow {
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }
        .account-card {
            transition: all 0.3s ease;
            cursor: pointer;
        }
        .account-card:hover {
            transform: translateY(-4px);
        }
        .account-card.selected {
            border-color: #667eea;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
        }
        .account-card.selected .check-icon {
            display: flex;
        }
        .account-card.disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        .account-card.disabled:hover {
            transform: none;
        }
        input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.2);
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px -5px rgba(102, 126, 234, 0.5);
        }
        .btn-primary:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }
        @keyframes pulse-glow {
            0%, 100% { box-shadow: 0 0 0 0 rgba(251, 191, 36, 0.7); }
            50% { box-shadow: 0 0 15px 5px rgba(251, 191, 36, 0.4); }
        }
        .animate-pulse-glow {
            animation: pulse-glow 2s ease-in-out infinite;
        }
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        .modal-slide-in {
            animation: slideIn 0.3s ease-out forwards;
        }
        .modal-fade-in {
            animation: fadeIn 0.3s ease-out forwards;
        }
        .tutorial-card {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .tutorial-card.active {
            transform: scale(1.02);
            border-color: #667eea;
            box-shadow: 0 10px 40px -10px rgba(102, 126, 234, 0.5);
        }
        .tutorial-card.active .tutorial-indicator {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .tutorial-highlight {
            position: relative;
        }
        .tutorial-highlight::after {
            content: '';
            position: absolute;
            inset: -4px;
            border: 3px dashed #667eea;
            border-radius: 1rem;
            animation: dash-rotate 20s linear infinite;
        }
        @keyframes dash-rotate {
            to { stroke-dashoffset: 100; }
        }
    </style>
</head>
<body class="min-h-screen gradient-bg flex items-center justify-center p-4 sm:p-6 lg:p-8">
    <div class="w-full max-w-2xl">
        <!-- Logo/Brand -->
        <div class="text-center mb-6 sm:mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 sm:w-20 sm:h-20 bg-white rounded-2xl shadow-lg mb-4">
                <i class="fas fa-cube text-3xl sm:text-4xl text-indigo-600"></i>
            </div>
            <h1 class="text-2xl sm:text-3xl font-bold text-white">Join Sandbox</h1>
            <p class="text-white/80 mt-2 text-sm sm:text-base">Create your account and start your journey</p>
        </div>

        <!-- Registration Card -->
        <div class="bg-white rounded-3xl card-shadow p-6 sm:p-8 lg:p-10">
            <form method="POST" action="{{ route('register') }}" id="registerForm">
                @csrf

                <!-- Step Indicator -->
                <div class="flex items-center justify-center mb-6 sm:mb-8">
                    <div class="flex items-center">
                        <div id="step1Indicator" class="w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-indigo-600 text-white flex items-center justify-center font-bold text-sm sm:text-base">1</div>
                        <div class="w-12 sm:w-20 h-1 bg-gray-200 mx-2" id="stepConnector">
                            <div class="h-full bg-indigo-600 transition-all duration-300" id="stepProgress" style="width: 0%"></div>
                        </div>
                        <div id="step2Indicator" class="w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-gray-200 text-gray-500 flex items-center justify-center font-bold text-sm sm:text-base">2</div>
                    </div>
                </div>

                <!-- Step 1: Personal Info -->
                <div id="step1" class="space-y-4 sm:space-y-5">
                    <h2 class="text-lg sm:text-xl font-bold text-gray-900 text-center mb-4 sm:mb-6">Personal Information</h2>

                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Full Name</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                                <i class="fas fa-user"></i>
                            </span>
                            <input id="name" type="text" name="name" value="{{ old('name') }}" required
                                class="w-full pl-11 pr-4 py-3 sm:py-3.5 border-2 border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 text-sm sm:text-base"
                                placeholder="Enter your full name">
                        </div>
                        @error('name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email Address</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                                <i class="fas fa-envelope"></i>
                            </span>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required
                                class="w-full pl-11 pr-4 py-3 sm:py-3.5 border-2 border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 text-sm sm:text-base"
                                placeholder="Enter your email">
                        </div>
                        @error('email')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Date of Birth -->
                    <div>
                        <label for="dob" class="block text-sm font-semibold text-gray-700 mb-2">Date of Birth</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                                <i class="fas fa-calendar"></i>
                            </span>
                            <input id="dob" type="date" name="dob" value="{{ old('dob') }}" required
                                class="w-full pl-11 pr-4 py-3 sm:py-3.5 border-2 border-gray-200 rounded-xl text-gray-900 text-sm sm:text-base"
                                max="{{ date('Y-m-d', strtotime('-11 years')) }}">
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Required for account type selection</p>
                        @error('dob')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">Password</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                                <i class="fas fa-lock"></i>
                            </span>
                            <input id="password" type="password" name="password" required
                                class="w-full pl-11 pr-12 py-3 sm:py-3.5 border-2 border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 text-sm sm:text-base"
                                placeholder="Create a password">
                            <button type="button" onclick="togglePassword('password')" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-600">
                                <i class="fas fa-eye" id="password-icon"></i>
                            </button>
                        </div>
                        @error('password')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">Confirm Password</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                                <i class="fas fa-lock"></i>
                            </span>
                            <input id="password_confirmation" type="password" name="password_confirmation" required
                                class="w-full pl-11 pr-12 py-3 sm:py-3.5 border-2 border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 text-sm sm:text-base"
                                placeholder="Confirm your password">
                            <button type="button" onclick="togglePassword('password_confirmation')" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-600">
                                <i class="fas fa-eye" id="password_confirmation-icon"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Referral Code (Optional) -->
                    <div>
                        <label for="referral_code" class="block text-sm font-semibold text-gray-700 mb-2">
                            Referral Code <span class="text-gray-400 font-normal">(Optional)</span>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                                <i class="fas fa-gift"></i>
                            </span>
                            <input id="referral_code" type="text" name="referral_code" value="{{ old('referral_code', request('ref')) }}"
                                class="w-full pl-11 pr-4 py-3 sm:py-3.5 border-2 border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 text-sm sm:text-base"
                                placeholder="Enter referral code if you have one">
                        </div>
                    </div>

                    <!-- Next Button -->
                    <button type="button" onclick="goToStep2()" class="w-full btn-primary text-white font-bold py-3 sm:py-4 rounded-xl text-sm sm:text-base mt-4">
                        Continue <i class="fas fa-arrow-right ml-2"></i>
                    </button>
                </div>

                <!-- Step 2: Account Type Selection -->
                <div id="step2" class="hidden space-y-4 sm:space-y-5">
                    <div class="flex items-center justify-center gap-3 mb-2">
                        <h2 class="text-lg sm:text-xl font-bold text-gray-900 text-center">Choose Your Sandbox Type</h2>
                        <button type="button" onclick="openInfoModal()" class="w-8 h-8 sm:w-9 sm:h-9 bg-gradient-to-br from-amber-400 to-orange-500 rounded-full flex items-center justify-center text-white shadow-lg hover:shadow-xl hover:scale-110 transition-all duration-300 animate-pulse-glow">
                            <i class="fas fa-question text-sm sm:text-base"></i>
                        </button>
                    </div>
                    <p class="text-center text-gray-500 text-sm mb-4 sm:mb-6">Select the account type that best suits your needs</p>

                    <input type="hidden" name="sandbox_type" id="sandbox_type" value="usahawan">

                    <div class="grid grid-cols-1 gap-4">
                        <!-- Usahawan -->
                        <div class="account-card selected border-2 border-indigo-500 rounded-2xl p-4 sm:p-5 relative" onclick="selectAccountType('usahawan', this)">
                            <div class="check-icon absolute top-3 right-3 w-6 h-6 sm:w-7 sm:h-7 bg-indigo-600 rounded-full flex items-center justify-center">
                                <i class="fas fa-check text-white text-xs sm:text-sm"></i>
                            </div>
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-briefcase text-white text-lg sm:text-xl"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h3 class="font-bold text-gray-900 text-base sm:text-lg">Sandbox Usahawan</h3>
                                    <p class="text-gray-500 text-xs sm:text-sm mt-1">For entrepreneurs and business owners. Get access to Geran Asas, Tabung Usahawan, and Had Pembiayaan.</p>
                                    <div class="flex flex-wrap gap-2 mt-3">
                                        <span class="px-2 py-1 bg-indigo-100 text-indigo-700 text-xs rounded-lg font-medium">Geran Asas</span>
                                        <span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs rounded-lg font-medium">Tabung Usahawan</span>
                                        <span class="px-2 py-1 bg-green-100 text-green-700 text-xs rounded-lg font-medium">Had Pembiayaan</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Remaja -->
                        <div id="remajaCard" class="account-card border-2 border-gray-200 rounded-2xl p-4 sm:p-5 relative" onclick="selectAccountType('remaja', this)">
                            <div class="check-icon hidden absolute top-3 right-3 w-6 h-6 sm:w-7 sm:h-7 bg-indigo-600 rounded-full items-center justify-center">
                                <i class="fas fa-check text-white text-xs sm:text-sm"></i>
                            </div>
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-br from-pink-500 to-rose-600 rounded-xl flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-graduation-cap text-white text-lg sm:text-xl"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <h3 class="font-bold text-gray-900 text-base sm:text-lg">Sandbox Remaja</h3>
                                        <span id="remajaAgeTag" class="px-2 py-0.5 bg-pink-100 text-pink-700 text-xs rounded-full font-medium">Ages 11-20</span>
                                    </div>
                                    <p class="text-gray-500 text-xs sm:text-sm mt-1">For young entrepreneurs aged 11-20. Access Biasiswa Pemula, Had Biasiswa, and Dana Usahawan Muda.</p>
                                    <div class="flex flex-wrap gap-2 mt-3">
                                        <span class="px-2 py-1 bg-pink-100 text-pink-700 text-xs rounded-lg font-medium">Biasiswa Pemula</span>
                                        <span class="px-2 py-1 bg-rose-100 text-rose-700 text-xs rounded-lg font-medium">Had Biasiswa</span>
                                        <span class="px-2 py-1 bg-orange-100 text-orange-700 text-xs rounded-lg font-medium">Dana Usahawan Muda</span>
                                    </div>
                                    <p id="remajaWarning" class="hidden text-red-500 text-xs mt-2">
                                        <i class="fas fa-exclamation-circle mr-1"></i> You must be between 11 and 20 years old to select this option
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Awam -->
                        <div class="account-card border-2 border-gray-200 rounded-2xl p-4 sm:p-5 relative" onclick="selectAccountType('awam', this)">
                            <div class="check-icon hidden absolute top-3 right-3 w-6 h-6 sm:w-7 sm:h-7 bg-indigo-600 rounded-full items-center justify-center">
                                <i class="fas fa-check text-white text-xs sm:text-sm"></i>
                            </div>
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-users text-white text-lg sm:text-xl"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h3 class="font-bold text-gray-900 text-base sm:text-lg">Sandbox Awam</h3>
                                    <p class="text-gray-500 text-xs sm:text-sm mt-1">For general public. Access Modal Pemula, Had Pembiayaan Hutang, and Khairat Kematian benefits.</p>
                                    <div class="flex flex-wrap gap-2 mt-3">
                                        <span class="px-2 py-1 bg-emerald-100 text-emerald-700 text-xs rounded-lg font-medium">Modal Pemula</span>
                                        <span class="px-2 py-1 bg-teal-100 text-teal-700 text-xs rounded-lg font-medium">Had Pembiayaan Hutang</span>
                                        <span class="px-2 py-1 bg-cyan-100 text-cyan-700 text-xs rounded-lg font-medium">Khairat Kematian</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="flex gap-3 mt-6">
                        <button type="button" onclick="goToStep1()" class="flex-1 border-2 border-gray-200 text-gray-700 font-bold py-3 sm:py-4 rounded-xl text-sm sm:text-base hover:bg-gray-50 transition-colors">
                            <i class="fas fa-arrow-left mr-2"></i> Back
                        </button>
                        <button type="submit" id="submitBtn" class="flex-1 btn-primary text-white font-bold py-3 sm:py-4 rounded-xl text-sm sm:text-base">
                            Create Account <i class="fas fa-check ml-2"></i>
                        </button>
                    </div>
                </div>

                <!-- Login Link -->
                <div class="text-center mt-6 pt-6 border-t border-gray-100">
                    <p class="text-gray-600 text-sm sm:text-base">
                        Already have an account?
                        <a href="{{ route('login') }}" class="text-indigo-600 font-semibold hover:text-indigo-700">Sign in</a>
                    </p>
                </div>
            </form>
        </div>

        <!-- Footer -->
        <div class="text-center mt-6">
            <p class="text-white/60 text-xs sm:text-sm">&copy; {{ date('Y') }} Sandbox. All rights reserved.</p>
        </div>
    </div>

    <!-- Info Modal -->
    <div id="infoModal" class="fixed inset-0 z-50 hidden">
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm modal-fade-in" onclick="closeInfoModal()"></div>

        <!-- Modal Content -->
        <div class="relative min-h-screen flex items-center justify-center p-4">
            <div class="bg-white rounded-3xl card-shadow w-full max-w-3xl max-h-[90vh] overflow-hidden modal-slide-in">
                <!-- Header -->
                <div class="gradient-bg p-5 sm:p-6 text-white relative">
                    <button type="button" onclick="closeInfoModal()" class="absolute top-4 right-4 w-8 h-8 bg-white/20 hover:bg-white/30 rounded-full flex items-center justify-center transition-colors">
                        <i class="fas fa-times text-white"></i>
                    </button>
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                            <i class="fas fa-info-circle text-2xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl sm:text-2xl font-bold" id="modalTitle">Sandbox Types Guide</h3>
                            <p class="text-white/80 text-sm" id="modalSubtitle">Learn about each account type</p>
                        </div>
                    </div>
                    <!-- Language Toggle -->
                    <div class="flex items-center gap-2 mt-4">
                        <span class="text-white/70 text-sm">Language:</span>
                        <div class="flex bg-white/20 rounded-lg p-1">
                            <button type="button" onclick="setLanguage('en')" id="langEn" class="px-3 py-1.5 rounded-md text-sm font-medium transition-colors bg-white text-indigo-600">
                                English
                            </button>
                            <button type="button" onclick="setLanguage('ms')" id="langMs" class="px-3 py-1.5 rounded-md text-sm font-medium transition-colors text-white/80 hover:text-white">
                                Bahasa Melayu
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Body -->
                <div class="p-5 sm:p-6 overflow-y-auto max-h-[60vh]">
                    <!-- Tutorial Dropdown -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-2" id="tutorialLabel">
                            <i class="fas fa-hand-pointer text-indigo-600 mr-2"></i>
                            <span id="selectTypeLabel">Select a type to learn more:</span>
                        </label>
                        <div class="relative">
                            <select id="tutorialSelect" onchange="showTutorialCard(this.value)" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl appearance-none cursor-pointer text-gray-900 font-medium focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all">
                                <option value="usahawan">Sandbox Usahawan</option>
                                <option value="remaja">Sandbox Remaja</option>
                                <option value="awam">Sandbox Awam</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                                <i class="fas fa-chevron-down text-gray-400"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Tutorial Cards -->
                    <div class="space-y-4">
                        <!-- Usahawan Card -->
                        <div id="tutorialUsahawan" class="tutorial-card active bg-gradient-to-br from-indigo-50 to-purple-50 border-2 border-indigo-300 rounded-2xl p-5 sm:p-6">
                            <div class="flex items-start gap-4">
                                <div class="w-14 h-14 sm:w-16 sm:h-16 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg">
                                    <i class="fas fa-briefcase text-white text-xl sm:text-2xl"></i>
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-2">
                                        <h4 class="font-bold text-gray-900 text-lg sm:text-xl">Sandbox Usahawan</h4>
                                        <span class="tutorial-indicator w-3 h-3 rounded-full bg-gradient-to-r from-indigo-500 to-purple-500"></span>
                                    </div>
                                    <div id="usahawanContent">
                                        <p class="text-gray-600 text-sm sm:text-base mb-3" id="usahawanDesc">
                                            <strong>For RizqMall Shop Owners & Vendors</strong><br>
                                            If you plan to sell products on RizqMall marketplace or run your own business, choose this account type.
                                        </p>
                                        <div class="bg-white/80 rounded-xl p-4 mb-3">
                                            <p class="text-sm font-semibold text-gray-700 mb-2" id="usahawanBenefitsTitle">Benefits:</p>
                                            <ul class="text-sm text-gray-600 space-y-1.5" id="usahawanBenefits">
                                                <li><i class="fas fa-check-circle text-green-500 mr-2"></i>Geran Asas - Startup grant (RM600)</li>
                                                <li><i class="fas fa-check-circle text-green-500 mr-2"></i>Tabung Usahawan - Business savings fund</li>
                                                <li><i class="fas fa-check-circle text-green-500 mr-2"></i>Had Pembiayaan - Financing limit access</li>
                                            </ul>
                                        </div>
                                        <div class="flex items-center gap-2 p-3 bg-indigo-100 rounded-xl">
                                            <i class="fas fa-store text-indigo-600 text-lg"></i>
                                            <span class="text-sm text-indigo-700 font-medium" id="usahawanIdeal">Ideal for: Shop owners, vendors, entrepreneurs</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Remaja Card -->
                        <div id="tutorialRemaja" class="tutorial-card hidden bg-gradient-to-br from-pink-50 to-rose-50 border-2 border-gray-200 rounded-2xl p-5 sm:p-6">
                            <div class="flex items-start gap-4">
                                <div class="w-14 h-14 sm:w-16 sm:h-16 bg-gradient-to-br from-pink-500 to-rose-600 rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg">
                                    <i class="fas fa-graduation-cap text-white text-xl sm:text-2xl"></i>
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-2">
                                        <h4 class="font-bold text-gray-900 text-lg sm:text-xl">Sandbox Remaja</h4>
                                        <span class="px-2 py-0.5 bg-pink-100 text-pink-700 text-xs rounded-full font-medium" id="remajaAgeLabel">11-20 years old</span>
                                        <span class="tutorial-indicator w-3 h-3 rounded-full bg-gray-300"></span>
                                    </div>
                                    <div id="remajaContent">
                                        <p class="text-gray-600 text-sm sm:text-base mb-3" id="remajaDesc">
                                            <strong>For Young People (Kids & Teenagers)</strong><br>
                                            Specially designed for young entrepreneurs aged 11-20 years old. Perfect for students who want to learn about business and savings.
                                        </p>
                                        <div class="bg-white/80 rounded-xl p-4 mb-3">
                                            <p class="text-sm font-semibold text-gray-700 mb-2" id="remajaBenefitsTitle">Benefits:</p>
                                            <ul class="text-sm text-gray-600 space-y-1.5" id="remajaBenefits">
                                                <li><i class="fas fa-check-circle text-green-500 mr-2"></i>Biasiswa Pemula - Starter scholarship (RM600)</li>
                                                <li><i class="fas fa-check-circle text-green-500 mr-2"></i>Had Biasiswa - Scholarship fund limit</li>
                                                <li><i class="fas fa-check-circle text-green-500 mr-2"></i>Dana Usahawan Muda - Young entrepreneur fund</li>
                                            </ul>
                                        </div>
                                        <div class="flex items-center gap-2 p-3 bg-pink-100 rounded-xl">
                                            <i class="fas fa-user-graduate text-pink-600 text-lg"></i>
                                            <span class="text-sm text-pink-700 font-medium" id="remajaIdeal">Ideal for: Students, teenagers, young learners</span>
                                        </div>
                                        <div class="mt-3 p-3 bg-amber-50 border border-amber-200 rounded-xl">
                                            <p class="text-xs text-amber-700" id="remajaNote">
                                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                                <strong>Note:</strong> Age requirement is 11-20 years old, verified by your date of birth.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Awam Card -->
                        <div id="tutorialAwam" class="tutorial-card hidden bg-gradient-to-br from-emerald-50 to-teal-50 border-2 border-gray-200 rounded-2xl p-5 sm:p-6">
                            <div class="flex items-start gap-4">
                                <div class="w-14 h-14 sm:w-16 sm:h-16 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg">
                                    <i class="fas fa-users text-white text-xl sm:text-2xl"></i>
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-2">
                                        <h4 class="font-bold text-gray-900 text-lg sm:text-xl">Sandbox Awam</h4>
                                        <span class="tutorial-indicator w-3 h-3 rounded-full bg-gray-300"></span>
                                    </div>
                                    <div id="awamContent">
                                        <p class="text-gray-600 text-sm sm:text-base mb-3" id="awamDesc">
                                            <strong>For General Public - Debt Relief & Benefits</strong><br>
                                            Choose this if you want to access hutang (debt) relief benefits, funeral assistance, and general financial support.
                                        </p>
                                        <div class="bg-white/80 rounded-xl p-4 mb-3">
                                            <p class="text-sm font-semibold text-gray-700 mb-2" id="awamBenefitsTitle">Benefits:</p>
                                            <ul class="text-sm text-gray-600 space-y-1.5" id="awamBenefits">
                                                <li><i class="fas fa-check-circle text-green-500 mr-2"></i>Modal Pemula - Starter capital (RM600)</li>
                                                <li><i class="fas fa-check-circle text-green-500 mr-2"></i>Had Pembiayaan Hutang - Debt financing limit</li>
                                                <li><i class="fas fa-check-circle text-green-500 mr-2"></i>Khairat Kematian - Funeral/death assistance fund</li>
                                            </ul>
                                        </div>
                                        <div class="flex items-center gap-2 p-3 bg-emerald-100 rounded-xl">
                                            <i class="fas fa-hand-holding-heart text-emerald-600 text-lg"></i>
                                            <span class="text-sm text-emerald-700 font-medium" id="awamIdeal">Ideal for: Public seeking hutang relief & welfare benefits</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Summary Table -->
                    <div class="mt-6 bg-gray-50 rounded-2xl p-4 sm:p-5">
                        <h4 class="font-bold text-gray-900 mb-3 flex items-center gap-2" id="summaryTitle">
                            <i class="fas fa-list-check text-indigo-600"></i>
                            Quick Summary
                        </h4>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="text-left border-b border-gray-200">
                                        <th class="pb-2 font-semibold text-gray-700" id="thType">Type</th>
                                        <th class="pb-2 font-semibold text-gray-700" id="thFor">Best For</th>
                                        <th class="pb-2 font-semibold text-gray-700" id="thAge">Age</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <tr>
                                        <td class="py-2.5"><span class="px-2 py-1 bg-indigo-100 text-indigo-700 rounded-lg font-medium text-xs">Usahawan</span></td>
                                        <td class="py-2.5 text-gray-600" id="tdUsahawan">RizqMall vendors & business owners</td>
                                        <td class="py-2.5 text-gray-600" id="tdUsahawanAge">All ages</td>
                                    </tr>
                                    <tr>
                                        <td class="py-2.5"><span class="px-2 py-1 bg-pink-100 text-pink-700 rounded-lg font-medium text-xs">Remaja</span></td>
                                        <td class="py-2.5 text-gray-600" id="tdRemaja">Students & young people</td>
                                        <td class="py-2.5 text-gray-600" id="tdRemajaAge">11-20 years</td>
                                    </tr>
                                    <tr>
                                        <td class="py-2.5"><span class="px-2 py-1 bg-emerald-100 text-emerald-700 rounded-lg font-medium text-xs">Awam</span></td>
                                        <td class="py-2.5 text-gray-600" id="tdAwam">Public seeking hutang relief</td>
                                        <td class="py-2.5 text-gray-600" id="tdAwamAge">All ages</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="p-5 sm:p-6 bg-gray-50 border-t border-gray-100">
                    <button type="button" onclick="closeInfoModal()" class="w-full btn-primary text-white font-bold py-3 rounded-xl">
                        <i class="fas fa-check mr-2"></i>
                        <span id="gotItBtn">Got it!</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let isRemajaEligible = false;
        let currentLang = 'en';

        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const icon = document.getElementById(fieldId + '-icon');
            if (field.type === 'password') {
                field.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                field.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        function calculateAge(dob) {
            const today = new Date();
            const birthDate = new Date(dob);
            let age = today.getFullYear() - birthDate.getFullYear();
            const monthDiff = today.getMonth() - birthDate.getMonth();
            if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
                age--;
            }
            return age;
        }

        function checkRemajaEligibility() {
            const dob = document.getElementById('dob').value;
            const remajaCard = document.getElementById('remajaCard');
            const remajaWarning = document.getElementById('remajaWarning');
            const remajaAgeTag = document.getElementById('remajaAgeTag');

            if (dob) {
                const age = calculateAge(dob);
                isRemajaEligible = age >= 11 && age <= 20;

                if (isRemajaEligible) {
                    remajaCard.classList.remove('disabled');
                    remajaWarning.classList.add('hidden');
                    remajaAgeTag.textContent = `Your age: ${age}`;
                    remajaAgeTag.classList.remove('bg-pink-100', 'text-pink-700');
                    remajaAgeTag.classList.add('bg-green-100', 'text-green-700');
                } else {
                    remajaCard.classList.add('disabled');
                    remajaWarning.classList.remove('hidden');
                    remajaAgeTag.textContent = `Your age: ${age}`;
                    remajaAgeTag.classList.remove('bg-green-100', 'text-green-700');
                    remajaAgeTag.classList.add('bg-red-100', 'text-red-700');

                    // If remaja was selected, switch to usahawan
                    if (document.getElementById('sandbox_type').value === 'remaja') {
                        selectAccountType('usahawan', document.querySelector('.account-card'));
                    }
                }
            }
        }

        function goToStep2() {
            // Validate step 1 fields
            const name = document.getElementById('name').value;
            const email = document.getElementById('email').value;
            const dob = document.getElementById('dob').value;
            const password = document.getElementById('password').value;
            const passwordConfirm = document.getElementById('password_confirmation').value;

            if (!name || !email || !dob || !password || !passwordConfirm) {
                alert('Please fill in all required fields');
                return;
            }

            if (password !== passwordConfirm) {
                alert('Passwords do not match');
                return;
            }

            if (password.length < 8) {
                alert('Password must be at least 8 characters');
                return;
            }

            // Check remaja eligibility based on DOB
            checkRemajaEligibility();

            // Show step 2
            document.getElementById('step1').classList.add('hidden');
            document.getElementById('step2').classList.remove('hidden');
            document.getElementById('step1Indicator').classList.remove('bg-indigo-600', 'text-white');
            document.getElementById('step1Indicator').classList.add('bg-green-500', 'text-white');
            document.getElementById('step1Indicator').innerHTML = '<i class="fas fa-check"></i>';
            document.getElementById('step2Indicator').classList.remove('bg-gray-200', 'text-gray-500');
            document.getElementById('step2Indicator').classList.add('bg-indigo-600', 'text-white');
            document.getElementById('stepProgress').style.width = '100%';
        }

        function goToStep1() {
            document.getElementById('step2').classList.add('hidden');
            document.getElementById('step1').classList.remove('hidden');
            document.getElementById('step1Indicator').classList.add('bg-indigo-600', 'text-white');
            document.getElementById('step1Indicator').classList.remove('bg-green-500');
            document.getElementById('step1Indicator').innerHTML = '1';
            document.getElementById('step2Indicator').classList.add('bg-gray-200', 'text-gray-500');
            document.getElementById('step2Indicator').classList.remove('bg-indigo-600', 'text-white');
            document.getElementById('stepProgress').style.width = '0%';
        }

        function selectAccountType(type, element) {
            // Check if remaja is selected but not eligible
            if (type === 'remaja' && !isRemajaEligible) {
                return;
            }

            // Remove selection from all cards
            document.querySelectorAll('.account-card').forEach(card => {
                card.classList.remove('selected');
                card.classList.remove('border-indigo-500');
                card.classList.add('border-gray-200');
                card.querySelector('.check-icon').classList.add('hidden');
                card.querySelector('.check-icon').classList.remove('flex');
            });

            // Add selection to clicked card
            element.classList.add('selected');
            element.classList.remove('border-gray-200');
            element.classList.add('border-indigo-500');
            element.querySelector('.check-icon').classList.remove('hidden');
            element.querySelector('.check-icon').classList.add('flex');

            // Update hidden input
            document.getElementById('sandbox_type').value = type;
        }

        // Listen to DOB changes
        document.getElementById('dob').addEventListener('change', checkRemajaEligibility);

        // Info Modal Functions
        function openInfoModal() {
            document.getElementById('infoModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            // Reset to first card
            showTutorialCard('usahawan');
            document.getElementById('tutorialSelect').value = 'usahawan';
        }

        function closeInfoModal() {
            document.getElementById('infoModal').classList.add('hidden');
            document.body.style.overflow = '';
        }

        function showTutorialCard(type) {
            // Hide all cards
            document.querySelectorAll('.tutorial-card').forEach(card => {
                card.classList.add('hidden');
                card.classList.remove('active');
                card.classList.remove('border-indigo-300', 'border-pink-300', 'border-emerald-300');
                card.classList.add('border-gray-200');
            });

            // Show selected card with animation
            const cardId = 'tutorial' + type.charAt(0).toUpperCase() + type.slice(1);
            const card = document.getElementById(cardId);
            card.classList.remove('hidden');

            // Trigger reflow for animation
            void card.offsetWidth;

            card.classList.add('active');
            card.classList.remove('border-gray-200');

            // Set appropriate border color
            if (type === 'usahawan') {
                card.classList.add('border-indigo-300');
            } else if (type === 'remaja') {
                card.classList.add('border-pink-300');
            } else {
                card.classList.add('border-emerald-300');
            }
        }

        // Language translations
        const translations = {
            en: {
                modalTitle: 'Sandbox Types Guide',
                modalSubtitle: 'Learn about each account type',
                selectTypeLabel: 'Select a type to learn more:',

                // Usahawan
                usahawanDesc: '<strong>For RizqMall Shop Owners & Vendors</strong><br>If you plan to sell products on RizqMall marketplace or run your own business, choose this account type.',
                usahawanBenefitsTitle: 'Benefits:',
                usahawanBenefits: [
                    'Geran Asas - Startup grant (RM600)',
                    'Tabung Usahawan - Business savings fund',
                    'Had Pembiayaan - Financing limit access'
                ],
                usahawanIdeal: 'Ideal for: Shop owners, vendors, entrepreneurs',

                // Remaja
                remajaAgeLabel: '11-20 years old',
                remajaDesc: '<strong>For Young People (Kids & Teenagers)</strong><br>Specially designed for young entrepreneurs aged 11-20 years old. Perfect for students who want to learn about business and savings.',
                remajaBenefitsTitle: 'Benefits:',
                remajaBenefits: [
                    'Biasiswa Pemula - Starter scholarship (RM600)',
                    'Had Biasiswa - Scholarship fund limit',
                    'Dana Usahawan Muda - Young entrepreneur fund'
                ],
                remajaIdeal: 'Ideal for: Students, teenagers, young learners',
                remajaNote: '<strong>Note:</strong> Age requirement is 11-20 years old, verified by your date of birth.',

                // Awam
                awamDesc: '<strong>For General Public - Debt Relief & Benefits</strong><br>Choose this if you want to access hutang (debt) relief benefits, funeral assistance, and general financial support.',
                awamBenefitsTitle: 'Benefits:',
                awamBenefits: [
                    'Modal Pemula - Starter capital (RM600)',
                    'Had Pembiayaan Hutang - Debt financing limit',
                    'Khairat Kematian - Funeral/death assistance fund'
                ],
                awamIdeal: 'Ideal for: Public seeking hutang relief & welfare benefits',

                // Summary
                summaryTitle: 'Quick Summary',
                thType: 'Type',
                thFor: 'Best For',
                thAge: 'Age',
                tdUsahawan: 'RizqMall vendors & business owners',
                tdUsahawanAge: 'All ages',
                tdRemaja: 'Students & young people',
                tdRemajaAge: '11-20 years',
                tdAwam: 'Public seeking hutang relief',
                tdAwamAge: 'All ages',
                gotItBtn: 'Got it!'
            },
            ms: {
                modalTitle: 'Panduan Jenis Sandbox',
                modalSubtitle: 'Ketahui tentang setiap jenis akaun',
                selectTypeLabel: 'Pilih jenis untuk maklumat lanjut:',

                // Usahawan
                usahawanDesc: '<strong>Untuk Pemilik Kedai & Vendor RizqMall</strong><br>Jika anda merancang untuk menjual produk di pasaran RizqMall atau menjalankan perniagaan sendiri, pilih jenis akaun ini.',
                usahawanBenefitsTitle: 'Manfaat:',
                usahawanBenefits: [
                    'Geran Asas - Geran permulaan (RM600)',
                    'Tabung Usahawan - Tabung simpanan perniagaan',
                    'Had Pembiayaan - Akses had pembiayaan'
                ],
                usahawanIdeal: 'Sesuai untuk: Pemilik kedai, vendor, usahawan',

                // Remaja
                remajaAgeLabel: 'Umur 11-20 tahun',
                remajaDesc: '<strong>Untuk Golongan Muda (Kanak-kanak & Remaja)</strong><br>Direka khas untuk usahawan muda berumur 11-20 tahun. Sesuai untuk pelajar yang ingin belajar tentang perniagaan dan simpanan.',
                remajaBenefitsTitle: 'Manfaat:',
                remajaBenefits: [
                    'Biasiswa Pemula - Biasiswa permulaan (RM600)',
                    'Had Biasiswa - Had tabung biasiswa',
                    'Dana Usahawan Muda - Tabung usahawan muda'
                ],
                remajaIdeal: 'Sesuai untuk: Pelajar, remaja, golongan muda',
                remajaNote: '<strong>Nota:</strong> Syarat umur adalah 11-20 tahun, disahkan melalui tarikh lahir anda.',

                // Awam
                awamDesc: '<strong>Untuk Orang Awam - Pelepasan Hutang & Manfaat</strong><br>Pilih ini jika anda ingin akses kepada manfaat pelepasan hutang, bantuan kematian, dan sokongan kewangan am.',
                awamBenefitsTitle: 'Manfaat:',
                awamBenefits: [
                    'Modal Pemula - Modal permulaan (RM600)',
                    'Had Pembiayaan Hutang - Had pembiayaan hutang',
                    'Khairat Kematian - Tabung bantuan kematian'
                ],
                awamIdeal: 'Sesuai untuk: Orang awam yang inginkan pelepasan hutang & manfaat kebajikan',

                // Summary
                summaryTitle: 'Ringkasan Pantas',
                thType: 'Jenis',
                thFor: 'Sesuai Untuk',
                thAge: 'Umur',
                tdUsahawan: 'Vendor RizqMall & pemilik perniagaan',
                tdUsahawanAge: 'Semua umur',
                tdRemaja: 'Pelajar & golongan muda',
                tdRemajaAge: '11-20 tahun',
                tdAwam: 'Orang awam mencari pelepasan hutang',
                tdAwamAge: 'Semua umur',
                gotItBtn: 'Faham!'
            }
        };

        function setLanguage(lang) {
            currentLang = lang;
            const t = translations[lang];

            // Update language buttons
            document.getElementById('langEn').classList.toggle('bg-white', lang === 'en');
            document.getElementById('langEn').classList.toggle('text-indigo-600', lang === 'en');
            document.getElementById('langEn').classList.toggle('text-white/80', lang !== 'en');
            document.getElementById('langMs').classList.toggle('bg-white', lang === 'ms');
            document.getElementById('langMs').classList.toggle('text-indigo-600', lang === 'ms');
            document.getElementById('langMs').classList.toggle('text-white/80', lang !== 'ms');

            // Update modal header
            document.getElementById('modalTitle').textContent = t.modalTitle;
            document.getElementById('modalSubtitle').textContent = t.modalSubtitle;
            document.getElementById('selectTypeLabel').textContent = t.selectTypeLabel;

            // Update Usahawan content
            document.getElementById('usahawanDesc').innerHTML = t.usahawanDesc;
            document.getElementById('usahawanBenefitsTitle').textContent = t.usahawanBenefitsTitle;
            document.getElementById('usahawanBenefits').innerHTML = t.usahawanBenefits.map(b =>
                `<li><i class="fas fa-check-circle text-green-500 mr-2"></i>${b}</li>`
            ).join('');
            document.getElementById('usahawanIdeal').textContent = t.usahawanIdeal;

            // Update Remaja content
            document.getElementById('remajaAgeLabel').textContent = t.remajaAgeLabel;
            document.getElementById('remajaDesc').innerHTML = t.remajaDesc;
            document.getElementById('remajaBenefitsTitle').textContent = t.remajaBenefitsTitle;
            document.getElementById('remajaBenefits').innerHTML = t.remajaBenefits.map(b =>
                `<li><i class="fas fa-check-circle text-green-500 mr-2"></i>${b}</li>`
            ).join('');
            document.getElementById('remajaIdeal').textContent = t.remajaIdeal;
            document.getElementById('remajaNote').innerHTML = `<i class="fas fa-exclamation-triangle mr-1"></i>${t.remajaNote}`;

            // Update Awam content
            document.getElementById('awamDesc').innerHTML = t.awamDesc;
            document.getElementById('awamBenefitsTitle').textContent = t.awamBenefitsTitle;
            document.getElementById('awamBenefits').innerHTML = t.awamBenefits.map(b =>
                `<li><i class="fas fa-check-circle text-green-500 mr-2"></i>${b}</li>`
            ).join('');
            document.getElementById('awamIdeal').textContent = t.awamIdeal;

            // Update summary table
            document.getElementById('summaryTitle').innerHTML = `<i class="fas fa-list-check text-indigo-600"></i> ${t.summaryTitle}`;
            document.getElementById('thType').textContent = t.thType;
            document.getElementById('thFor').textContent = t.thFor;
            document.getElementById('thAge').textContent = t.thAge;
            document.getElementById('tdUsahawan').textContent = t.tdUsahawan;
            document.getElementById('tdUsahawanAge').textContent = t.tdUsahawanAge;
            document.getElementById('tdRemaja').textContent = t.tdRemaja;
            document.getElementById('tdRemajaAge').textContent = t.tdRemajaAge;
            document.getElementById('tdAwam').textContent = t.tdAwam;
            document.getElementById('tdAwamAge').textContent = t.tdAwamAge;
            document.getElementById('gotItBtn').textContent = t.gotItBtn;
        }

        // Close modal on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && !document.getElementById('infoModal').classList.contains('hidden')) {
                closeInfoModal();
            }
        });
    </script>
</body>
</html>
