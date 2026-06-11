<nav x-data="{ open: false }" class="bg-indigo-900 border-b border-indigo-800 text-white shadow-lg">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo / UMS Title -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center space-x-2">
                        <svg class="h-8 w-8 text-indigo-400 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                            <path d="M12 3L1 9l11 6 9-4.91V17h2V9L12 3z"/>
                            <path d="M4.19 14.19L12 18.5l7.81-4.31v3.13L12 21.64l-7.81-4.32v-3.13z"/>
                        </svg>
                        <span class="font-bold text-lg tracking-wider text-white">Apex UMS</span>
                    </a>
                </div>

                <!-- Navigation Links (Desktop) -->
                <div class="hidden space-x-4 sm:-my-px sm:ms-8 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="text-indigo-100 hover:text-white border-transparent hover:border-indigo-300">
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    @if (Auth::user()->hasRole(['super_admin', 'registrar']))
                        <x-nav-link :href="route('students.index')" :active="request()->routeIs('students.*')" class="text-indigo-100 hover:text-white">
                            {{ __('Students') }}
                        </x-nav-link>

                        <x-nav-link :href="route('lecturers.index')" :active="request()->routeIs('lecturers.*')" class="text-indigo-100 hover:text-white">
                            {{ __('Lecturers') }}
                        </x-nav-link>

                        <x-nav-link :href="route('attendance.index')" :active="request()->routeIs('attendance.*') && !request()->routeIs('*face-register*')" class="text-indigo-100 hover:text-white">
                            {{ __('Attendance') }}
                        </x-nav-link>

                        <x-nav-link :href="route('grades.index')" :active="request()->routeIs('grades.*')" class="text-indigo-100 hover:text-white">
                            {{ __('Grades') }}
                        </x-nav-link>

                        <x-nav-link :href="route('finance.index')" :active="request()->routeIs('finance.*')" class="text-indigo-100 hover:text-white">
                            {{ __('Finance') }}
                        </x-nav-link>

                        <!-- HR Dropdown inside Nav -->
                        <div class="inline-flex items-center px-1 pt-1 text-sm font-medium leading-5 transition duration-150 ease-in-out">
                            <x-dropdown align="left" width="48">
                                <x-slot name="trigger">
                                    <button class="inline-flex items-center text-indigo-100 hover:text-white focus:outline-none transition ease-in-out duration-150">
                                        <div>HR</div>
                                        <div class="ms-1">
                                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    </button>
                                </x-slot>
                                <x-slot name="content">
                                    <x-dropdown-link :href="route('hr.payroll.index')">Payrolls</x-dropdown-link>
                                    <x-dropdown-link :href="route('hr.leaves.index')">Leave Requests</x-dropdown-link>
                                </x-slot>
                            </x-dropdown>
                        </div>

                        <!-- Library Dropdown inside Nav -->
                        <div class="inline-flex items-center px-1 pt-1 text-sm font-medium leading-5 transition duration-150 ease-in-out">
                            <x-dropdown align="left" width="48">
                                <x-slot name="trigger">
                                    <button class="inline-flex items-center text-indigo-100 hover:text-white focus:outline-none transition ease-in-out duration-150">
                                        <div>Library</div>
                                        <div class="ms-1">
                                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    </button>
                                </x-slot>
                                <x-slot name="content">
                                    <x-dropdown-link :href="route('library.books.index')">Books</x-dropdown-link>
                                    <x-dropdown-link :href="route('library.borrows.index')">Borrow Records</x-dropdown-link>
                                </x-slot>
                            </x-dropdown>
                        </div>

                        <x-nav-link :href="route('reports.index')" :active="request()->routeIs('reports.*')" class="text-indigo-100 hover:text-white">
                            {{ __('Reports') }}
                        </x-nav-link>
                    @endif

                    @if (Auth::user()->hasRole('lecturer'))
                        <x-nav-link :href="route('attendance.index')" :active="request()->routeIs('attendance.*')" class="text-indigo-100 hover:text-white">
                            {{ __('Class Attendance') }}
                        </x-nav-link>

                        <x-nav-link :href="route('grades.index')" :active="request()->routeIs('grades.*')" class="text-indigo-100 hover:text-white">
                            {{ __('Student Grades') }}
                        </x-nav-link>

                        <x-nav-link :href="route('hr.leaves.create')" :active="request()->routeIs('hr.leaves.create')" class="text-indigo-100 hover:text-white">
                            {{ __('Apply Leave') }}
                        </x-nav-link>
                    @endif

                    @if (Auth::user()->hasRole('student'))
                        @if (Auth::user()->student)
                            <x-nav-link :href="route('grades.transcript', Auth::user()->student->id)" :active="request()->routeIs('grades.transcript')" class="text-indigo-100 hover:text-white">
                                {{ __('Transcript') }}
                            </x-nav-link>
                        @endif

                        <x-nav-link :href="route('attendance.face-register')" :active="request()->routeIs('*face-register*')" class="text-indigo-100 hover:text-white">
                            {{ __('Register Face') }}
                        </x-nav-link>

                        <x-nav-link :href="route('library.books.index')" :active="request()->routeIs('library.*')" class="text-indigo-100 hover:text-white">
                            {{ __('Library Catalog') }}
                        </x-nav-link>
                    @endif

                    @if (Auth::user()->hasRole('staff'))
                        <x-nav-link :href="route('hr.leaves.create')" :active="request()->routeIs('hr.leaves.create')" class="text-indigo-100 hover:text-white">
                            {{ __('Apply Leave') }}
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <!-- Settings Dropdown (Desktop) -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-indigo-200 bg-indigo-800 hover:text-white hover:bg-indigo-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }} ({{ strtoupper(str_replace('_', ' ', Auth::user()->roles->first()?->slug ?? 'Guest')) }})</div>
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile Settings') }}
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

            <!-- Hamburger (Mobile) -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-indigo-300 hover:text-white hover:bg-indigo-800 focus:outline-none focus:bg-indigo-800 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu (Mobile) -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-indigo-950 px-2 pt-2 pb-3 space-y-1">
        <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="text-white hover:bg-indigo-800">
            {{ __('Dashboard') }}
        </x-responsive-nav-link>

        @if (Auth::user()->hasRole(['super_admin', 'registrar']))
            <x-responsive-nav-link :href="route('students.index')" :active="request()->routeIs('students.*')" class="text-white hover:bg-indigo-800">
                {{ __('Students') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('lecturers.index')" :active="request()->routeIs('lecturers.*')" class="text-white hover:bg-indigo-800">
                {{ __('Lecturers') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('attendance.index')" :active="request()->routeIs('attendance.*')" class="text-white hover:bg-indigo-800">
                {{ __('Attendance') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('grades.index')" :active="request()->routeIs('grades.*')" class="text-white hover:bg-indigo-800">
                {{ __('Grades') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('finance.index')" :active="request()->routeIs('finance.*')" class="text-white hover:bg-indigo-800">
                {{ __('Finance') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('hr.payroll.index')" :active="request()->routeIs('hr.payroll.*')" class="text-white hover:bg-indigo-800">
                {{ __('Payrolls') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('hr.leaves.index')" :active="request()->routeIs('hr.leaves.*')" class="text-white hover:bg-indigo-800">
                {{ __('Leaves') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('reports.index')" :active="request()->routeIs('reports.*')" class="text-white hover:bg-indigo-800">
                {{ __('Reports') }}
            </x-responsive-nav-link>
        @endif

        <!-- Settings Options (Mobile) -->
        <div class="pt-4 pb-1 border-t border-indigo-800">
            <div class="px-4">
                <div class="font-medium text-base text-white">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-indigo-300">{{ Auth::user()->email }}</div>
            </div>
            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')" class="text-indigo-200">
                    {{ __('Profile Settings') }}
                </x-responsive-nav-link>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();" class="text-indigo-200">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
