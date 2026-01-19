<aside class="app-sidebar sticky" id="sidebar">

    <!-- Start::main-sidebar-header -->
    <div class="main-sidebar-header">
        <a href="index.html" class="header-logo">
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
                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                        </svg>
                        <span class="side-menu__label">Dashboard</span>
                    </a>
                </li>
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
                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
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

                <!-- Start::slide Staff Maanger-->
                @if (canAccess('user.allow'))
                    <li class="slide">
                        <a href="{{ route('user-manager.index') }}"
                            class="side-menu__item {{ request()->routeIs('user-manager.*') ? 'active' : '' }} ">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 side-menu__icon" viewBox="0 0 24 24"
                                fill="currentColor">
                                <path d="M12 12a5 5 0 1 0-5-5 5 5 0 0 0 5 5z" />
                                <path d="M4 20a8 8 0 0 1 16 0z" />
                            </svg>

                            <span class="side-menu__label">User Manager</span>
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
                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15 19.125a3.375 3.375 0 1 0-6 0M3 20.25a9 9 0 0 1 18 0M12 9a3 3 0 1 0 0-6 3 3 0 0 0 0 6z" />
                            </svg>
                            <span class="side-menu__label">client</span>
                        </a>
                    </li>
                @endif
                <!-- End::slide -->

                <!-- Start::slide Leads Manager -->
                @if (canAccess('leads.allow'))
                    <li class="slide">
                        <a href="{{ route('lead.index') }}"
                            class="side-menu__item {{ request()->routeIs('lead.*') ? 'active' : '' }}">

                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 side-menu__icon"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                                stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="7" cy="7" r="3" />
                                <path d="M2.5 18c0-2.8 2.2-5 4.5-5s4.5 2.2 4.5 5" />
                                <rect x="13" y="4" width="8" height="14" rx="2" />
                                <path d="M15.5 8h3" />
                                <path d="M15.5 11h3" />
                                <path d="M15.5 14h3" />
                            </svg>
                            <span class="side-menu__label">Leads Manager</span>
                        </a>
                    </li>
                @endif
                <!-- End::slide -->

                {{-- @if (canAccess('reports.allow'))
                    <li
                        class="slide has-sub {{ request()->routeIs('admin.reports.marketplace-demand') || request()->routeIs('admin.reports.service-demand') || request()->routeIs('admin.reports.vendor-performance') || request()->routeIs('admin.reports.client-engagement') || request()->routeIs('admin.reports.service-click-detail') ? 'open' : '' }} ">
                        <a href="javascript:void(0);"
                            class="side-menu__item {{ request()->routeIs('admin.reports.marketplace-demand') || request()->routeIs('admin.reports.service-demand') || request()->routeIs('admin.reports.vendor-performance') || request()->routeIs('admin.reports.client-engagement') || request()->routeIs('admin.reports.service-click-detail') ? 'active' : '' }}">
                            <i class="ri-arrow-right-s-line side-menu__angle"></i>
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 side-menu__icon" fill="none"
                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 6h18M3 12h18M3 18h18" />
                            </svg>
                            <span class="side-menu__label">Reports</span>
                        </a>
                        <ul class="slide-menu child1">
                            <li class="slide side-menu__label1">
                                <a href="javascript:void(0)">Reports</a>
                            </li>
                            <li
                                class="slide {{ request()->routeIs('admin.reports.marketplace-demand') ? 'active' : '' }}">
                                <a href="{{ route('admin.reports.marketplace-demand') }}"
                                    class="side-menu__item {{ request()->routeIs('admin.reports.marketplace-demand') ? 'active' : '' }}">Marketplace
                                    Demand Overview</a>
                            </li>
                            <li
                                class="slide {{ request()->routeIs('admin.reports.service-demand') ? 'active' : '' }}">
                                <a href="{{ route('admin.reports.service-demand') }}"
                                    class="side-menu__item {{ request()->routeIs('admin.reports.service-demand') ? 'active' : '' }}">Service
                                    Demand Report</a>
                            </li>
                            <li
                                class="slide {{ request()->routeIs('admin.reports.vendor-performance') ? 'active' : '' }}">
                                <a href="{{ route('admin.reports.vendor-performance') }}"
                                    class="side-menu__item {{ request()->routeIs('admin.reports.vendor-performance') ? 'active' : '' }}">Vendor
                                    Performance
                                    Overview</a>
                            </li>
                           
                        </ul>
                    </li>
                @endif --}}
            </ul>

        </nav>
        <!-- End::nav -->

    </div>

</aside>
