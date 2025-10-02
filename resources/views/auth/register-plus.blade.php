<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts - Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .gradient-button {
            background-image: linear-gradient(to right, #4F46E5, #8B5CF6);
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }
        .gradient-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(139, 92, 246, 0.4);
        }
        input:focus {
            outline: none;
            box-shadow: 0 0 0 2px rgba(139, 92, 246, 0.5);
            border-color: #8B5CF6;
        }
    </style>
</head>
<body class="bg-gray-950 text-gray-200 antialiased flex items-center justify-center min-h-screen p-4">

    <div class="w-full max-w-lg mx-auto bg-gray-900 rounded-2xl shadow-2xl overflow-hidden p-8 sm:p-12 border border-gray-800">
        <!-- Logo -->
        <div class="flex flex-col items-center justify-center mb-8">
            <div class="flex flex-row items-center justify-center gap-4">
            <img src="{{ asset('sandboxlogo.png') }}" alt="Logo" class="w-24 h-24 rounded">
            <img src="{{ asset('rizq.jpeg') }}" alt="Logo" class="w-24 h-24 rounded">
            </div>
           
            <h2 class="mt-4 text-3xl font-bold tracking-tight text-white">Join the future.</h2>
            <p class="text-sm text-gray-400 mt-2">Create your account to get started.</p>
        </div>

        <!-- Display all errors at the top -->
        @if ($errors->any())
            <div class="mb-6 p-4 rounded-xl bg-red-900 text-red-200">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Registration Form -->
        <form method="POST" action="{{ route('register.store') }}">
            @csrf

            <!-- Name -->
            <div class="mb-5">
                <label for="name" class="block text-sm font-medium text-gray-400 mb-1.5">Name</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus
                       class="block w-full px-4 py-3 bg-gray-800 text-gray-200 border border-gray-700 rounded-xl transition duration-300 placeholder-gray-500">
                @error('name')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email -->
            <div class="mb-5">
                <label for="email" class="block text-sm font-medium text-gray-400 mb-1.5">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required
                       class="block w-full px-4 py-3 bg-gray-800 text-gray-200 border border-gray-700 rounded-xl transition duration-300 placeholder-gray-500">
                @error('email')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Phone -->
            <div class="mb-5">
                <label for="phone" class="block text-sm font-medium text-gray-400 mb-1.5">Phone</label>
                <input type="text" id="phone" name="phone" value="{{ old('phone') }}"
                       class="block w-full px-4 py-3 bg-gray-800 text-gray-200 border border-gray-700 rounded-xl">
                @error('phone')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Country -->
            <div class="mb-5">
                <label for="country" class="block text-sm font-medium text-gray-400 mb-1.5">Country</label>
                <select id="country" name="country"
                        class="block w-full px-4 py-3 bg-gray-800 text-gray-200 border border-gray-700 rounded-xl">
                    <option value="">-- Select Country --</option>
                </select>
            </div>
            
            <!-- State -->
            <div id="state-wrapper" class="mb-5 hidden">
                <label for="state" class="block text-sm font-medium text-gray-400 mb-1.5">State</label>
                <select id="state" name="state"
                        class="block w-full px-4 py-3 bg-gray-800 text-gray-200 border border-gray-700 rounded-xl">
                    <option value="">-- Select State --</option>
                </select>
            </div>
            
            <!-- City -->
            <div id="city-wrapper" class="mb-5 hidden">
                <label for="city" class="block text-sm font-medium text-gray-400 mb-1.5">City</label>
                <select id="city" name="city"
                        class="block w-full px-4 py-3 bg-gray-800 text-gray-200 border border-gray-700 rounded-xl">
                    <option value="">-- Select City --</option>
                </select>
            </div>


            <!-- Password -->
            <div class="mb-5 relative">
                <label for="password" class="block text-sm font-medium text-gray-400 mb-1.5">Password</label>
                <input type="password" id="password" name="password" required
                       class="block w-full px-4 py-3 bg-gray-800 text-gray-200 border border-gray-700 rounded-xl transition duration-300 placeholder-gray-500 pr-10">
                <button type="button" class="absolute inset-y-0 right-0 top-6 flex items-center pr-3" onclick="togglePasswordVisibility('password')">
                    <svg id="password-hide-icon" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.418 0-8-2.686-8-6s3.582-6 8-6 8 2.686 8 6a10.05 10.05 0 01-1.875 5.825m1.34-1.34L20.8 19.2a.75.75 0 01-1.06 1.06l-2.03-2.03m-2.14-2.14a3 3 0 00-4.242 0M12 15a3 3 0 100-6 3 3 0 000 6z" />
                    </svg>
                    <svg id="password-show-icon" class="h-5 w-5 text-gray-400 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                </button>
                @error('password')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Confirm Password -->
            <div class="mb-5 relative">
                <label for="password_confirmation" class="block text-sm font-medium text-gray-400 mb-1.5">Confirm Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required
                       class="block w-full px-4 py-3 bg-gray-800 text-gray-200 border border-gray-700 rounded-xl transition duration-300 placeholder-gray-500 pr-10">
                <button type="button" class="absolute inset-y-0 right-0 top-6 flex items-center pr-3" onclick="togglePasswordVisibility('password_confirmation')">
                    <svg id="password_confirmation-hide-icon" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.418 0-8-2.686-8-6s3.582-6 8-6 8 2.686 8 6a10.05 10.05 0 01-1.875 5.825m1.34-1.34L20.8 19.2a.75.75 0 01-1.06 1.06l-2.03-2.03m-2.14-2.14a3 3 0 00-4.242 0M12 15a3 3 0 100-6 3 3 0 000 6z" />
                    </svg>
                    <svg id="password_confirmation-show-icon" class="h-5 w-5 text-gray-400 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                </button>
                @error('password_confirmation')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Hidden referral code -->
            <input type="hidden" name="ref" value="{{ request('ref') }}">

            <!-- Submit -->
            <div>
                <button type="submit" class="w-full py-3.5 px-6 rounded-xl font-semibold text-white gradient-button shadow-lg">
                    Register
                </button>
            </div>
        </form>

        <!-- Login Link -->
        <p class="mt-8 text-center text-sm text-gray-500">
            Already have an account?
            <a href="{{ route('login') }}" class="font-medium text-indigo-400 hover:text-indigo-300 transition-colors duration-200">
                Log In
            </a>
        </p>
    </div>

    <script>
        function togglePasswordVisibility(fieldId) {
            const field = document.getElementById(fieldId);
            const showIcon = document.getElementById(fieldId + '-show-icon');
            const hideIcon = document.getElementById(fieldId + '-hide-icon');
            if (field.type === 'password') {
                field.type = 'text';
                showIcon.classList.remove('hidden');
                hideIcon.classList.add('hidden');
            } else {
                field.type = 'password';
                showIcon.classList.add('hidden');
                hideIcon.classList.remove('hidden');
            }
        }
    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    let data = {};

    $.getJSON("{{ asset('select.json') }}", function(response) {
        data = response;
        $.each(data, function(country) {
            $("#country").append(new Option(country, country));
        });
    });

    $("#country").on("change", function() {
        let country = $(this).val();
        let states = data[country] || {};

        $("#state").empty().append(new Option("-- Select State --", ""));
        $("#city").empty().append(new Option("-- Select City --", ""));
        $("#city-wrapper").addClass("hidden");

        if (country === "Malaysia") {
            $("#state-wrapper").removeClass("hidden");
            $.each(states, function(state) {
                $("#state").append(new Option(state, state));
            });
        } else {
            $("#state-wrapper").addClass("hidden");
            $("#city-wrapper").addClass("hidden");
        }
    });

    $("#state").on("change", function() {
        let country = $("#country").val();
        let state = $(this).val();
        let cities = data[country][state] || [];

        $("#city").empty().append(new Option("-- Select City --", ""));

        if (cities.length > 0) {
            $("#city-wrapper").removeClass("hidden");
            $.each(cities, function(i, city) {
                $("#city").append(new Option(city, city));
            });
        } else {
            $("#city-wrapper").addClass("hidden");
        }
    });
</script>

</body>
</html>
