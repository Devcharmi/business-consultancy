<header class="app-header sticky" id="header">

    <div class="main-header-container container-fluid">

        <!-- Start::header-content-left -->
        <div class="header-content-left">

            <!-- Start::header-element -->
            <div class="header-element">
                <div class="horizontal-logo">
                    <a href="{{ route('dashboard') }}" class="header-logo">
                        <img src="{{ asset('admin/assets/images/brand-logos/logo.png') }}" alt="logo"
                            class="desktop-logo">
                        <img src="{{ asset('admin/assets/images/brand-logos/logo-icon.png') }}" alt="logo"
                            class="toggle-dark">
                        <img src="{{ asset('admin/assets/images/brand-logos/logo.png') }}" alt="logo"
                            class="desktop-dark">
                        <img src="{{ asset('admin/assets/images/brand-logos/logo-icon.png') }}" alt="logo"
                            class="toggle-logo">
                        <img src="{{ asset('admin/assets/images/brand-logos/toggle-white.png') }}" alt="logo"
                            class="toggle-white">
                        <img src="{{ asset('admin/assets/images/brand-logos/desktop-white.png') }}" alt="logo"
                            class="desktop-white">
                    </a>
                </div>
            </div>
            <!-- End::header-element -->

            <!-- Start::header-element -->
            <div class="header-element mx-lg-0 mx-2">
                <a aria-label="Hide Sidebar"
                    class="sidemenu-toggle header-link animated-arrow hor-toggle horizontal-navtoggle"
                    data-bs-toggle="sidebar" href="javascript:void(0);"><span></span></a>
            </div>
            <!-- End::header-element -->

        </div>
        <!-- End::header-content-left -->

        <!-- Start::header-content-right -->
        <ul class="header-content-right">

            <!-- Start::header-element -->
            <li class="header-element header-fullscreen">
                <!-- Start::header-link -->
                <a onclick="openFullscreen();" href="javascript:void(0);" class="header-link">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 full-screen-open header-link-icon"
                        fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3.75 3.75v4.5m0-4.5h4.5m-4.5 0L9 9M3.75 20.25v-4.5m0 4.5h4.5m-4.5 0L9 15M20.25 3.75h-4.5m4.5 0v4.5m0-4.5L15 9m5.25 11.25h-4.5m4.5 0v-4.5m0 4.5L15 15" />
                    </svg>
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 full-screen-close header-link-icon d-none"
                        fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 9V4.5M9 9H4.5M9 9 3.75 3.75M9 15v4.5M9 15H4.5M9 15l-5.25 5.25M15 9h4.5M15 9V4.5M15 9l5.25-5.25M15 15h4.5M15 15v4.5m0-4.5 5.25 5.25" />
                    </svg>
                </a>
                <!-- End::header-link -->
            </li>
            <!-- End::header-element -->

            <!-- Start::header-element -->
            <li class="header-element dropdown">
                <!-- Start::header-link|dropdown-toggle -->
                <a href="javascript:void(0);" class="header-link dropdown-toggle" id="mainHeaderProfile"
                    data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                    <div class="d-flex flex-column align-items-center">
                        @php
                            $profileImage = auth()->user()->profile_image ?? 'admin/assets/images/faces/15.jpg';
                        @endphp

                        <img src="{{ asset($profileImage) }}" alt="img"
                            class="avatar custom-header-avatar avatar-rounded">
                        <span class="text-dark fw-medium fs-12 mt-1">
                            {{ auth()->user()->name }}
                        </span>
                    </div>
                </a>
                <!-- End::header-link|dropdown-toggle -->
                <ul class="main-header-dropdown dropdown-menu pt-0 overflow-hidden header-profile-dropdown dropdown-menu-end"
                    aria-labelledby="mainHeaderProfile">
                    <li>
                        <div class="dropdown-item text-center border-bottom">
                            <span class="fw-medium">
                                {{ auth()->user()->name }}
                            </span>
                            <span class="d-block fs-12 text-muted">{{ auth()->user()->roles->first()?->name }}</span>
                        </div>
                    </li>
                    <li><a class="dropdown-item d-flex align-items-center"
                            href="{{ route('admin.profile.edit') }}"><i
                                class="ri-user-line lh-1 p-1 rounded-circle bg-primary-transparent text-primary me-2 fs-14"></i>Profile</a>
                    </li>
                    @if (canAccess('permissions.allow'))
                        <li><a class="dropdown-item d-flex align-items-center"
                                href="{{ route('permissions.index') }}"><i
                                    class="ri-user-settings-line lh-1 p-1 rounded-circle bg-primary-transparent text-primary me-2 fs-14"></i>Permission
                                Manager</a>
                        </li>
                    @endif
                    @if (canAccess('role.allow'))
                        <li><a class="dropdown-item d-flex align-items-center" href="{{ route('role.index') }}"><i
                                    class="ri-user-settings-line lh-1 p-1 rounded-circle bg-primary-transparent text-primary me-2 fs-14"></i>Role
                                Manager</a>
                        </li>
                    @endif
                    @if (canAccess('user.allow'))
                        <li><a class="dropdown-item d-flex align-items-center" href="{{ route('user-manager.index') }}"><i
                                    class="ri-team-line lh-1 p-1 rounded-circle bg-primary-transparent text-primary me-2 fs-14"></i>User
                                Manager</a>
                        </li>
                    @endif
                    <li>
                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <a class="dropdown-item d-flex align-items-center" href="{{ route('logout') }}"
                                onclick="event.preventDefault();
                                        this.closest('form').submit();"><i
                                    class="ri-door-lock-line lh-1 p-1 rounded-circle bg-primary-transparent text-primary me-2 fs-14"></i>Log
                                Out</a>
                        </form>
                    </li>
                </ul>
            </li>
            <!-- End::header-element -->

        </ul>
        <!-- End::header-content-right -->

    </div>

</header>
