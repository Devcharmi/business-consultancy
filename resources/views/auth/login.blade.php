<!DOCTYPE html>
<html lang="en" dir="ltr" data-nav-layout="vertical" data-vertical-style="overlay" data-theme-mode="light"
    data-header-styles="light" data-menu-styles="light" data-toggled="close">

<head>
    @include('auth.auth_layout.head')
    <!-- Title -->
    <title> {{ config('app.name', '') }} - Login </title>
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
                                    class="desktop-logo" style="height: 4rem;">
                                <img src="{{ asset('admin/assets/images/brand-logos/logo.png') }}" alt="logo"
                                    class="desktop-white">
                            </a>
                        </div>
                        <p class="h5 mb-2 text-center">Sign In</p>
                        <p class="text-muted mb-4 text-center">Let's get started</p>
                        <form action="{{ route('login') }}" method="post">
                            @csrf
                            <div class="row mb-1">
                                <div class="col-xl-12">
                                    <label for="signin-username" class="form-label text-default">User Name</label>
                                    <input type="text" class="form-control" id="signin-username" name="username"
                                        placeholder="user name">
                                    <ul class="text-sm text-danger space-y-1">
                                        @error('name')
                                            {{ $message }}
                                        @enderror
                                    </ul>
                                </div>
                                <div class="col-xl-12 mb-2">
                                    <label for="signin-password" class="form-label text-default d-block">Password<a
                                            href="{{ route('password.request') }}"
                                            class="float-end fw-normal text-primary fw-medium">Forget password
                                            ?</a></label>
                                    <div class="position-relative">
                                        <input type="password" class="form-control create-password-input"
                                            id="signin-password" placeholder="password" name="password">
                                        <a href="javascript:void(0);" class="show-password-button text-muted"
                                            onclick="createpassword('signin-password',this)" id="button-addon2"><i
                                                class="ri-eye-off-line align-middle"></i></a>
                                    </div>
                                    <ul class="text-sm text-danger space-y-1">
                                        @error('password')
                                            {{ $message }}
                                        @enderror
                                    </ul>
                                </div>
                            </div>
                            <div class="d-grid mt-3">
                                <button type="submit" class="btn btn-primary">Sign In</button>
                                {{-- @if ($errors->has('username'))
                                    <a href="{{ route('verification.send') }}">
                                        Resend verification email
                                    </a>
                                @endif --}}
                                {{-- <p class="text-muted mt-3 mb-0 text-center">Dont have an account? <a
                                        href="{{ route('register') }}" class="text-primary fw-medium">Sign Up</a></p> --}}
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('auth.auth_layout.footerscript')

</body>

</html>
