<aside class="app-sidebar sticky" id="sidebar">

    <!-- Start::main-sidebar-header -->
    <div class="main-sidebar-header">
        <a href="{{ route('dashboard') }}" class="header-logo">
            <img src="{{ asset('admin/assets/images/brand-logos/logo.png') }}" alt="logo" class="desktop-logo">
            <img src="{{ asset('admin/assets/images/brand-logos/toggle-dark.png') }}" alt="logo" class="toggle-dark">
            <img src="{{ asset('admin/assets/images/brand-logos/desktop-dark.png') }}" alt="logo"
                class="desktop-dark">
            <img src="{{ asset('admin/assets/images/brand-logos/logo-icon.png') }}" alt="logo" class="toggle-logo">
            <img src="{{ asset('admin/assets/images/brand-logos/toggle-white.png') }}" alt="logo"
                class="toggle-white">
            <img src="{{ asset('admin/assets/images/brand-logos/desktop-white.png') }}" alt="logo"
                class="desktop-white">
        </a>
    </div>
    <!-- End::main-sidebar-header -->

    <!-- Start::main-sidebar -->
    <div class="main-sidebar" id="sidebar-scroll">

        <!-- Start::nav -->
        <nav class="main-menu-container nav nav-pills flex-column sub-open">
            <div class="slide-left" id="slide-left">
                <svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191" width="24" height="24"
                    viewBox="0 0 24 24">
                    <path d="M13.293 6.293 7.586 12l5.707 5.707 1.414-1.414L10.414 12l4.293-4.293z"></path>
                </svg>
            </div>
            <ul class="main-menu">
                <!-- Start::slide -->
                <li class="slide">
                    <a href="{{ route('dashboard') }}"
                        class="side-menu__item {{ request()->routeIs('dashboard') ? 'active' : '' }} ">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 side-menu__icon" fill="none"
                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                        </svg>
                        <span class="side-menu__label">Dashboard</span>
                    </a>
                </li>
                <!-- End::slide -->

                <!-- Start::slide Leads Manager -->
                @if (canAccess('leads.allow'))
                    <li class="slide">
                        <a href="{{ route('lead.index') }}"
                            class="side-menu__item {{ request()->routeIs('lead.*') ? 'active' : '' }}">

                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 side-menu__icon" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path
                                    d="M22 16.92V21a2 2 0 0 1-2.18 2A19.8 19.8 0 0 1 3 5.18 2 2 0 0 1 5 3h4.09a2 2 0 0 1 2 1.72c.12.81.3 1.6.57 2.36a2 2 0 0 1-.45 2.11L10 10a16 16 0 0 0 6 6l.81-1.21a2 2 0 0 1 2.11-.45c.76.27 1.55.45 2.36.57A2 2 0 0 1 22 16.92z" />
                            </svg>

                            <span class="side-menu__label">Leads</span>
                        </a>
                    </li>
                @endif
                <!-- End::slide -->

                <!-- Start::slide client Maanger-->
                @if (canAccess('client.allow'))
                    <li class="slide">
                        <a href="{{ route('clients.index') }}"
                            class="side-menu__item {{ request()->routeIs('clients.*') ? 'active' : '' }} ">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 side-menu__icon" fill="none"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15 19.125a3.375 3.375 0 1 0-6 0M3 20.25a9 9 0 0 1 18 0M12 9a3 3 0 1 0 0-6 3 3 0 0 0 0 6z" />
                            </svg>
                            <span class="side-menu__label">client</span>
                        </a>
                    </li>
                @endif
                <!-- End::slide -->

                <!-- Start::slide Leads Manager -->
                @if (canAccess('consulting.allow'))
                    <li class="slide">
                        <a href="{{ route('consulting.index') }}"
                            class="side-menu__item {{ request()->routeIs('consulting.*') ? 'active' : '' }}">

                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 side-menu__icon" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path d="M21 15a4 4 0 0 1-4 4H7l-4 4V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4z" />
                                <path d="M8 9h8" />
                                <path d="M8 13h6" />
                            </svg>

                            <span class="side-menu__label">Consulting</span>
                        </a>
                    </li>
                @endif
                <!-- End::slide -->

                <!-- Start::slide Leads Manager -->
                @if (canAccess('client-objective.allow'))
                    <li class="slide">
                        <a href="{{ route('client-objective-manager.index') }}"
                            class="side-menu__item {{ request()->routeIs('client-objective-manager.*') ? 'active' : '' }}">

                            <svg xmlns="http://www.w3.org/2000/svg"class="w-6 h-6 side-menu__icon" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10" />
                                <circle cx="12" cy="12" r="6" />
                                <circle cx="12" cy="12" r="2" />
                            </svg>

                            <span class="side-menu__label">Client Objective</span>
                        </a>
                    </li>
                @endif
                <!-- End::slide -->

                <!-- Start::slide task Manager -->
                @if (canAccess('task.allow'))
                    <li class="slide">
                        <a href="{{ route('task.index') }}"
                            class="side-menu__item {{ request()->routeIs('task.*') ? 'active' : '' }}">

                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 side-menu__icon" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2">
                                </rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                                <circle cx="12" cy="15" r="3"></circle>
                                <line x1="12" y1="15" x2="12" y2="13"></line>
                                <line x1="12" y1="15" x2="14" y2="15"></line>
                            </svg>

                            <span class="side-menu__label">Meetings</span>
                        </a>
                    </li>
                @endif
                <!-- End::slide -->

                <!-- Start::slide task Manager -->
                @if (canAccess('user-task.allow'))
                    <li class="slide">
                        <a href="{{ route('user-task.index') }}"
                            class="side-menu__item {{ request()->routeIs('user-task.*') ? 'active' : '' }}">

                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 side-menu__icon" fill="none"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M4.5 6.75h15M4.5 12h15M4.5 17.25h15M5.25 6.75l1.5 1.5L9 5.25M5.25 12l1.5 1.5L9 10.5M5.25 17.25l1.5 1.5L9 15.75" />
                            </svg>
                            <span class="side-menu__label">Tasks</span>
                        </a>
                    </li>
                @endif
                <!-- End::slide -->

                <!-- Start::slide Staff Maanger-->
                {{-- @if (canAccess('user.allow'))
                    <li class="slide">
                        <a href="{{ route('user-manager.index') }}"
                            class="side-menu__item {{ request()->routeIs('user-manager.*') ? 'active' : '' }} ">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 side-menu__icon"
                                viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 12a5 5 0 1 0-5-5 5 5 0 0 0 5 5z" />
                                <path d="M4 20a8 8 0 0 1 16 0z" />
                            </svg>

                            <span class="side-menu__label">User Manager</span>
                        </a>
                    </li>
                @endif --}}
                <!-- End::slide -->

                <!-- Start::slide Staff Maanger-->
                @if (canAccess('objective.allow') ||
                        canAccess('expertise.allow') ||
                        canAccess('focus-area.allow') ||
                        canAccess('status-manager.allow'))
                    <li
                        class="slide has-sub
                        {{ (request()->routeIs('objective-manager.*') || request()->routeIs('expertise-manager.*') || request()->routeIs('focus-area-manager.*') ? 'open' : '' || request()->routeIs('status-manager.*')) ? 'open' : '' }}
                        ">
                        <a href="javascript:void(0);"
                            class="side-menu__item {{ (request()->routeIs('objective-manager.*') || request()->routeIs('expertise-manager.*') || request()->routeIs('focus-area-manager.*') ? 'active' : '' || request()->routeIs('status-manager.*')) ? 'active' : '' }}">
                            <i class="ri-arrow-right-s-line side-menu__angle"></i>
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 side-menu__icon" fill="none"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 6h18M3 12h18M3 18h18" />
                            </svg>
                            <span class="side-menu__label">Masters</span>
                        </a>
                        <ul class="slide-menu child1">
                            <li class="slide side-menu__label1">
                                <a href="javascript:void(0)">Masters</a>
                            </li>
                            @if (canAccess('objective.allow'))
                                <li class="slide">
                                    <a href="{{ route('objective-manager.index') }}"
                                        class="side-menu__item {{ request()->routeIs('objective-manager.*') ? 'active' : '' }}">Objective
                                        Manager</a>
                                </li>
                            @endif
                            @if (canAccess('expertise.allow'))
                                <li class="slide">
                                    <a href="{{ route('expertise-manager.index') }}"
                                        class="side-menu__item {{ request()->routeIs('expertise-manager.*') ? 'active' : '' }}">Expertise
                                        Manager</a>
                                </li>
                            @endif
                            @if (canAccess('focus-area.allow'))
                                <li class="slide">
                                    <a href="{{ route('focus-area-manager.index') }}"
                                        class="side-menu__item {{ request()->routeIs('focus-area-manager.*') ? 'active' : '' }}">Focus
                                        Area
                                        Manager</a>
                                </li>
                            @endif
                            @if (canAccess('status-manager.allow'))
                                <li class="slide">
                                    <a href="{{ route('status-manager.index') }}"
                                        class="side-menu__item {{ request()->routeIs('status-manager.*') ? 'active' : '' }}">Status
                                        Manager</a>
                                </li>
                            @endif
                        </ul>
                    </li>
                @endif
                <!-- End::slide -->

                @php
                    $reportRoutes = [
                        'reports.clients',
                        'reports.objectives',
                        'reports.consultings',
                        'reports.leads',
                    ];

                    $isReportActive = request()->routeIs($reportRoutes);
                @endphp

                @if (canAccess('reports.allow'))
                    <li class="slide has-sub {{ $isReportActive ? 'open' : '' }}">
                        <a href="javascript:void(0);" class="side-menu__item {{ $isReportActive ? 'active' : '' }}">
                            <i class="ri-arrow-right-s-line side-menu__angle"></i>
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 side-menu__icon" fill="none"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 6h18M3 12h18M3 18h18" />
                            </svg>
                            <span class="side-menu__label">Reports</span>
                        </a>
                        <ul class="slide-menu child1">
                            <li class="slide side-menu__label1">
                                <a href="javascript:void(0)">Reports</a>
                            </li>

                            {{-- Client Report --}}
                            <li class="slide {{ request()->routeIs('reports.clients') ? 'active' : '' }}">
                                <a href="{{ route('reports.clients') }}" class="side-menu__item">
                                    Clients Report
                                </a>
                            </li>

                            {{-- Objective Report --}}
                            <li class="slide {{ request()->routeIs('reports.objectives') ? 'active' : '' }}">
                                <a href="{{ route('reports.objectives') }}" class="side-menu__item">
                                    Objectives Report
                                </a>
                            </li>

                            {{-- Consulting Report --}}
                            <li class="slide {{ request()->routeIs('reports.consultings') ? 'active' : '' }}">
                                <a href="{{ route('reports.consultings') }}" class="side-menu__item">
                                    Consultings Report
                                </a>
                            </li>

                            {{-- Lead Report --}}
                            <li class="slide {{ request()->routeIs('reports.leads') ? 'active' : '' }}">
                                <a href="{{ route('reports.leads') }}" class="side-menu__item">
                                    Leads Report
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif

            </ul>

        </nav>
        <!-- End::nav -->

    </div>

</aside>
