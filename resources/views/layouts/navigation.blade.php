<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    </a>
                </div>

                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                    <x-nav-link :href="route('profile.index')" :active="request()->routeIs('profile.index')">
                        Profile
                    </x-nav-link>
                    <x-nav-link :href="route('wallet.users.index')" :active="request()->routeIs('wallet.users.index')">
                        E-Wallet
                    </x-nav-link>
                    <x-nav-link :href="route('collection.index')" :active="request()->routeIs('collection.index')">
                        Tabung
                    </x-nav-link>
                    @if(auth()->user()->hasRole('Admin'))
                    <x-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.index')">
                        Assign Role
                    </x-nav-link>
                    @endif
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6">
                {{-- THEME TOGGLE BUTTON --}}
                <button type="button" id="theme-toggle" class="relative flex h-[52px] w-[52px] items-center justify-center rounded-[12px] border border-gray-300 hover:bg-gray-100 transition-colors duration-200">
                    <span class="block dark:hidden">
                        <svg class="stroke-gray-800" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M18.3284 14.8687C13.249 14.8687 9.13135 10.751 9.13135 5.67163C9.13135 4.74246 9.26914 3.84548 9.5254 3C5.74897 4.14461 3 7.65276 3 11.803C3 16.8824 7.11765 21 12.197 21C16.3472 21 19.8554 18.251 21 14.4746C20.1545 14.7309 19.2575 14.8687 18.3284 14.8687Z" stroke-width="1.5" stroke-linejoin="round" />
                        </svg>
                    </span>
                    <span class="hidden dark:block">
                        <svg class="stroke-gray-200" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="12" cy="12" r="5" stroke-width="1.5" />
                            <path d="M12 2V4" stroke-width="1.5" stroke-linecap="round" />
                            <path d="M12 20V22" stroke-width="1.5" stroke-linecap="round" />
                            <path d="M20.6602 7L18.9281 8" stroke-width="1.5" stroke-linecap="round" />
                            <path d="M5.07178 16L3.33973 17" stroke-width="1.5" stroke-linecap="round" />
                            <path d="M3.33984 7L5.07189 8" stroke-width="1.5" stroke-linecap="round" />
                            <path d="M18.9282 16L20.6603 17" stroke-width="1.5" stroke-linecap="round" />
                        </svg>
                    </span>
                </button>
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.index')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
        </div>

        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.index')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>
                @if(auth()->user()->hasRole('Admin'))
                <x-responsive-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.index')">
                    Assign Role
                </x-responsive-nav-link>
                @endif
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                        onclick="event.preventDefault(); this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>