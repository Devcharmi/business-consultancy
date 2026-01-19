   <!DOCTYPE html>
   <html lang="en" dir="ltr" data-nav-layout="vertical" data-vertical-style="overlay" data-theme-mode="light"
       data-header-styles="light" data-menu-styles="light" data-toggled="close">

   <head>
       @include('auth.auth_layout.head')
       <!-- Title -->
       <title> {{ config('app.name', '') }} - Reset Password </title>
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
                           <p class="h5 mb-2 text-center">Reset Password</p>
                           <p class="text-muted mb-4 text-center">Let's get started</p>
                           <form method="POST" action="{{ route('password.store') }}">
                               @csrf

                               <!-- Password Reset Token -->
                               <input type="hidden" name="token" value="{{ $request->route('token') }}">
                               <div class="row gy-3">
                                   <div class="col-xl-12">
                                       <label for="email" class="form-label text-default">Email</label>
                                       <input type="email" class="form-control" id="email" name="email"
                                           placeholder="email" name="email">
                                       <ul class="text-sm text-danger space-y-1">
                                           @error('email')
                                               {{ $message }}
                                           @enderror
                                       </ul>
                                   </div>
                                   <div class="col-xl-12">
                                       <label for="reset-newpassword" class="form-label text-default">New
                                           Password</label>
                                       <div class="position-relative">
                                           <input type="password" class="form-control create-password-input"
                                               id="reset-newpassword" placeholder="new password" name="password">
                                           <a href="javascript:void(0);" class="show-password-button text-muted"
                                               onclick="createpassword('reset-newpassword',this)" id="button-addon21"><i
                                                   class="ri-eye-off-line align-middle"></i></a>
                                       </div>
                                       <ul class="text-sm text-danger space-y-1">
                                           @error('password')
                                               {{ $message }}
                                           @enderror
                                       </ul>
                                   </div>
                                   <div class="col-xl-12 mb-2">
                                       <label for="reset-confirmpassword" class="form-label text-default">Confirm
                                           Password</label>
                                       <div class="position-relative">
                                           <input type="password" class="form-control create-password-input"
                                               id="reset-confirmpassword" placeholder="confirm password"
                                               name="password_confirmation">
                                           <a href="javascript:void(0);" class="show-password-button text-muted"
                                               onclick="createpassword('reset-confirmpassword',this)"
                                               id="button-addon22"><i class="ri-eye-off-line align-middle"></i></a>
                                       </div>
                                       <ul class="text-sm text-danger space-y-1">
                                           @error('password_confirmation')
                                               {{ $message }}
                                           @enderror
                                       </ul>
                                   </div>
                               </div>
                               <div class="d-grid mt-3">
                                   <button type="submit" class="btn btn-primary">Reset Password</button>
                               </div>
                               <div class="text-center">
                                   <p class="text-muted mt-3">Remembered your password? <a href="{{ route('login') }}"
                                           class="text-primary fw-medium">Sign In</a></p>
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
