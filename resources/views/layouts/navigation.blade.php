<nav x-data="{ open: false }" class="bg-white border-b border-egg-200">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-2 sm:px-4 lg:px-6">
        <div class="flex justify-between h-12">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center gap-3 min-w-0">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 min-w-0">
                        <x-application-logo class="block h-9 w-auto shrink-0" />
                        <span class="sm:hidden font-semibold text-egg-900 text-sm truncate">{{ config('app.name') }}</span>
                        <div class="hidden sm:flex flex-col leading-tight min-w-0">
                            <span class="font-semibold text-egg-900 truncate text-sm lg:text-base">{{ config('app.name') }}</span>
                            <span class="text-xs text-egg-600 truncate max-w-[12rem] lg:max-w-xs">{{ config('app.tagline') }}</span>
                        </div>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
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
                    <x-nav-link :href="route('stock-out.create')" :active="request()->routeIs('stock-out.*')">
                        {{ __('FIFO Keluar') }}
                    </x-nav-link>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-2 py-1 border border-transparent text-xs leading-4 font-medium rounded text-egg-800 bg-white hover:text-egg-900 hover:bg-egg-50 focus:outline-none transition ease-in-out duration-150 sm:text-sm">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
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
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-egg-600 hover:text-egg-800 hover:bg-egg-100 focus:outline-none focus:bg-egg-100 focus:text-egg-800 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
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
            <x-responsive-nav-link :href="route('stock-out.create')" :active="request()->routeIs('stock-out.*')">
                {{ __('FIFO Keluar') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-egg-200">
            <div class="px-4">
                <div class="font-medium text-base text-egg-900">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-egg-700">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
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
