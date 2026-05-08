<nav x-data="{ open: false }" class="fixed top-0 left-0 right-0 z-50 w-full bg-gradient-to-r from-egg-700 via-egg-600 to-egg-500 shadow-lg border-b border-egg-800/40">
    <!-- Primary Navigation Menu -->
    <div class="w-full max-w-[1920px] mx-auto px-4 sm:px-8 lg:px-12">
        <div class="flex justify-between items-center min-h-[4.25rem] sm:min-h-[4.5rem]">
            <div class="flex items-center gap-4 lg:gap-8 min-w-0">
                <!-- Logo -->
                <div class="shrink-0 flex items-center gap-3 min-w-0">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 min-w-0 group">
                        <x-application-logo class="block h-11 w-auto shrink-0 rounded-md ring-2 ring-white/25 shadow-md" />
                        <span class="sm:hidden font-semibold text-white text-base truncate drop-shadow-sm">{{ config('app.name') }}</span>
                        <div class="hidden sm:flex flex-col leading-tight min-w-0">
                            <span class="font-semibold text-white truncate text-base lg:text-lg drop-shadow-sm">{{ config('app.name') }}</span>
                            <span class="text-sm text-egg-100/95 truncate max-w-[14rem] lg:max-w-md">{{ config('app.tagline') }}</span>
                        </div>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden flex-wrap gap-x-1 lg:gap-x-2 xl:gap-x-3 sm:-my-px sm:ms-4 lg:ms-8 sm:flex items-end">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                    <x-nav-link :href="route('item-barcodes.index')" :active="request()->routeIs('item-barcodes.*')">
                        {{ __('Barcode Barang') }}
                    </x-nav-link>
                    <x-nav-link :href="route('company-barcodes.index')" :active="request()->routeIs('company-barcodes.*')">
                        {{ __('Barcode Perusahaan') }}
                    </x-nav-link>
                    <x-nav-link :href="route('scan.index')" :active="request()->routeIs('scan.*')">
                        {{ __('Scan') }}
                    </x-nav-link>
                    <x-nav-link :href="route('employees.index')" :active="request()->routeIs('employees.*')">
                        {{ __('Karyawan') }}
                    </x-nav-link>
                    <!-- <x-nav-link :href="route('stock-out.create')" :active="request()->routeIs('stock-out.*')">
                        {{ __('FIFO Keluar') }}
                    </x-nav-link> -->
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-4 shrink-0">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-4 py-2.5 border border-white/25 text-base leading-5 font-medium rounded-lg text-white bg-white/10 hover:bg-white/20 focus:outline-none transition ease-in-out duration-150 shadow-sm">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-2">
                                <svg class="fill-current h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden shrink-0">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-3 rounded-lg text-white hover:bg-white/15 focus:outline-none focus:bg-white/15 transition duration-150 ease-in-out">
                    <svg class="h-7 w-7" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden border-t border-white/15 bg-egg-900/35 backdrop-blur-sm">
        <div class="pt-2 pb-3 space-y-0.5 px-2">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('item-barcodes.index')" :active="request()->routeIs('item-barcodes.*')">
                {{ __('Barcode Barang') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('company-barcodes.index')" :active="request()->routeIs('company-barcodes.*')">
                {{ __('Barcode Perusahaan') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('scan.index')" :active="request()->routeIs('scan.*')">
                {{ __('Scan') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('employees.index')" :active="request()->routeIs('employees.*')">
                {{ __('Karyawan') }}
            </x-responsive-nav-link>
            <!-- <x-responsive-nav-link :href="route('stock-out.create')" :active="request()->routeIs('stock-out.*')">
                {{ __('FIFO Keluar') }}
            </x-responsive-nav-link> -->
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-3 border-t border-white/15 px-4">
            <div class="px-1">
                <div class="font-semibold text-lg text-white">{{ Auth::user()->name }}</div>
                <div class="font-medium text-base text-egg-100">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-0.5">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
