<!DOCTYPE html>
<html lang="en" dir="ltr" data-nav-layout="vertical" data-vertical-style="overlay" data-theme-mode="light"
    data-header-styles="light" data-menu-styles="light" data-toggled="close">

<head>
    @include('auth.auth_layout.head')
    <!-- Title -->
    <title> {{ config('app.name', '') }} - Registration </title>
</head>

<body class="authentication-background authentication">

    <div class="container">
        <div class="row justify-content-center align-items-center authentication authentication-basic h-100">
            <div class="col-xxl-4 col-xl-4 col-lg-4 col-md-6 col-sm-8 col-12">
                <div class="card custom-card my-4">
                    <div class="card-body p-5">
                        <div class="mb-4 d-flex justify-content-center">
                            <a href="{{ route('login') }}">
                                <img src="{{ asset('admin/assets/images/brand-logos/logo.png') }}" alt="logo"
                                    class="desktop-logo">
                                <img src="{{ asset('admin/assets/images/brand-logos/logo.png') }}" alt="logo"
                                    class="desktop-white">
                            </a>
                        </div>
                        <p class="h5 mb-2 text-center">Sign Up</p>
                        <p class="text-muted mb-4 text-center">Let's get started</p>
                        <form action="{{ route('register') }}" method="post">
                            @csrf
                            <div class="row mb-1">
                                <div class="col-xl-12">
                                    <label for="signup-username" class="form-label text-default">User Name</label>
                                    <input type="text" class="form-control" id="signup-username" name="name"
                                        placeholder="user name">
                                    <ul class="text-sm text-danger space-y-1">
                                        @error('name')
                                            {{ $message }}
                                        @enderror
                                    </ul>
                                </div>
                                <div class="col-xl-12">
                                    <label for="signup-email" class="form-label text-default">Email</label>
                                    <input type="email" class="form-control" id="signup-email" name="email"
                                        placeholder="email" name="email">
                                    <ul class="text-sm text-danger space-y-1">
                                        @error('email')
                                            {{ $message }}
                                        @enderror
                                    </ul>
                                </div>
                                <div class="col-xl-12">
                                    <label for="signup-password" class="form-label text-default">Password</label>
                                    <div class="position-relative">
                                        <input type="password" class="form-control create-password-input"
                                            name="password" id="signup-password" placeholder="password">
                                        <a href="javascript:void(0);" class="show-password-button text-muted"
                                            onclick="createpassword('signup-password',this)" id="button-addon2"><i
                                                class="ri-eye-off-line align-middle"></i></a>
                                    </div>
                                    <ul class="text-sm text-danger space-y-1">
                                        @error('password')
                                            {{ $message }}
                                        @enderror
                                    </ul>
                                </div>
                                <div class="col-xl-12">
                                    <label for="signup-confirmpassword" class="form-label text-default">Confirm
                                        Password</label>
                                    <div class="position-relative">
                                        <input type="password" class="form-control create-password-input"
                                            id="signup-confirmpassword" placeholder="confirm password"
                                            name="password_confirmation">
                                        <a href="javascript:void(0);" class="show-password-button text-muted"
                                            onclick="createpassword('signup-confirmpassword',this)"
                                            id="button-addon21"><i class="ri-eye-off-line align-middle"></i></a>
                                    </div>
                                    <ul class="text-sm text-danger space-y-1">
                                        @error('password_confirmation')
                                            {{ $message }}
                                        @enderror
                                    </ul>
                                </div>
                            </div>
                            <div class="d-grid mt-4">
                                <button type="submit" class="btn btn-primary">Sign Up</button>
                            </div>
                        </form>
                        <div class="text-center">
                            <p class="text-muted mt-3 mb-0">Already have an account? <a href="{{ route('login') }}"
                                    class="text-primary fw-medium">Sign In</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('auth.auth_layout.footerscript')
</body>

</html>
