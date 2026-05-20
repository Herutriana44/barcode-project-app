<nav x-data="{ open: false }" class="sticky-navbar sticky top-0 z-[9999] bg-gradient-to-r from-egg-700 via-egg-600 to-egg-500 shadow-lg border-b border-egg-800/40">
    <div class="w-full max-w-[1920px] mx-auto px-4 sm:px-8 lg:px-12">
        <div class="flex justify-between items-center min-h-[4.25rem] sm:min-h-[4.5rem]">
            <div class="flex items-center gap-4 lg:gap-8 min-w-0">
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

                <div class="hidden flex-wrap gap-x-0.5 lg:gap-x-1 sm:-my-px sm:ms-2 lg:ms-4 sm:flex items-center h-[4.5rem]">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    <x-dropdown align="left" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 border-b-2 {{ request()->routeIs('item-barcodes.*') || request()->routeIs('company-barcodes.*') ? 'border-white text-white' : 'border-transparent text-white/85 hover:text-white hover:border-white/40' }} text-sm lg:text-base font-medium whitespace-nowrap transition duration-150 ease-in-out">
                                <div>{{ __('Data Barcode') }}</div>
                                <div class="ms-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            <x-dropdown-link :href="route('item-barcodes.index')" :active="request()->routeIs('item-barcodes.*')">
                                {{ __('Barcode Barang') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('company-barcodes.index')" :active="request()->routeIs('company-barcodes.*')">
                                {{ __('Barcode Perusahaan') }}
                            </x-dropdown-link>
                        </x-slot>
                    </x-dropdown>

                    @if(auth()->user()->role !== 'admin')
                    <x-dropdown align="left" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 border-b-2 {{ request()->routeIs('scan.*') || request()->routeIs('scan-employee.*') ? 'border-white text-white' : 'border-transparent text-white/85 hover:text-white hover:border-white/40' }} text-sm lg:text-base font-medium whitespace-nowrap transition duration-150 ease-in-out">
                                <div>{{ __('Fitur Scan') }}</div>
                                <div class="ms-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            @if(auth()->user()->role !== 'admin')
                            <x-dropdown-link :href="route('scan.index')" :active="request()->routeIs('scan.*') && !request()->routeIs('scan-employee.*')">
                                {{ __('Scan') }}
                            </x-dropdown-link>
                            @endif
                            @if(auth()->user()->role !== 'admin')
                            <x-dropdown-link :href="route('scan-employee.index')" :active="request()->routeIs('scan-employee.*')">
                                {{ __('Scan Karyawan') }}
                            </x-dropdown-link>
                            @endif
                        </x-slot>
                    </x-dropdown>
                    @endif

                    @if(auth()->user()->role === 'admin')
                    <x-nav-link :href="route('scan.index')" :active="request()->routeIs('scan.*') && !request()->routeIs('scan-employee.*')">
                        {{ __('Scan') }}
                    </x-nav-link>
                    <x-nav-link :href="route('employees.index')" :active="request()->routeIs('employees.*')">
                        {{ __('Karyawan') }}
                    </x-nav-link>

                        <x-nav-link :href="route('activity-logs.index')" :active="request()->routeIs('activity-logs.*')">
                            {{ __('Log Aktivitas') }}
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-4 gap-3 shrink-0">
                {{-- Badge karyawan aktif --}}
                @if(auth()->user()->role != 'admin')
                @if(session('active_employee_id'))
                    <div class="flex items-center gap-2 px-4 py-2 rounded-lg bg-green-500/20 border border-green-400/40 text-white text-base font-medium">
                        <span class="w-2 h-2 rounded-full bg-green-400 shrink-0 animate-pulse"></span>
                        <span class="truncate max-w-[10rem]">{{ session('active_employee_name') }}</span>
                        <form method="POST" action="{{ route('scan-employee.destroy') }}" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" title="Akhiri sesi karyawan"
                                    class="ml-1 text-green-200 hover:text-white transition text-lg leading-none"
                                    onclick="return confirm('Akhiri sesi karyawan?')">✕</button>
                        </form>
                    </div>
                @else
                    <a href="{{ route('scan-employee.index') }}"
                       class="flex items-center gap-2 px-4 py-2 rounded-lg bg-amber-500/20 border border-amber-400/40 text-amber-100 hover:bg-amber-500/30 text-base font-medium transition">
                        <span class="w-2 h-2 rounded-full bg-amber-400 shrink-0"></span>
                        Scan Karyawan
                    </a>
                @endif
                @endif

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-4 py-2 border border-white/25 text-base font-medium rounded-lg text-white bg-white/10 hover:bg-white/20 focus:outline-none transition ease-in-out duration-150 shadow-sm">
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
            <x-responsive-nav-link :href="route('scan.index')" :active="request()->routeIs('scan.*') && !request()->routeIs('scan-employee.*')">
                {{ __('Scan') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('scan-employee.index')" :active="request()->routeIs('scan-employee.*')">
                {{ __('Scan Karyawan') }}
                @if(session('active_employee_id'))
                    <span class="ml-2 inline-flex items-center gap-1 text-xs text-green-300">
                        <span class="w-1.5 h-1.5 rounded-full bg-green-400"></span>{{ session('active_employee_name') }}
                    </span>
                @endif
            </x-responsive-nav-link>
            @if(auth()->user()->role === 'admin')
            <x-responsive-nav-link :href="route('employees.index')" :active="request()->routeIs('employees.*')">
                                {{ __('Karyawan') }}
                                            </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('activity-logs.index')" :active="request()->routeIs('activity-logs.*')">
                    {{ __('Log Aktivitas') }}
                </x-responsive-nav-link>
            @endif
        </div>

        <div class="pt-4 pb-3 border-t border-white/15 px-4">
            <div class="px-1">
                <div class="font-semibold text-lg text-white">{{ Auth::user()->name }}</div>
                <div class="font-medium text-base text-egg-100">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-0.5">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

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