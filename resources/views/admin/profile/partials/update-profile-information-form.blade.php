  <div class="row mt-4">
      <div class="col-xl-12">
          <div class="card custom-card">
              <div class="card-header">
                  <div class="card-title">
                      Profile Information
                  </div>
              </div>
              <div class="card-body">
                  <!-- Email verification resend form -->
                  {{-- <form id="send-verification" method="post" action="/email/verification-notification">
                      <input type="hidden" name="_token" value="CSRF_TOKEN_HERE">
                  </form> --}}

                  <!-- Profile update form -->
                  <form method="post" action="{{ route('admin.profile.update') }}" class="mt-6 space-y-6"
                      enctype="multipart/form-data">
                      @csrf
                      <input type="hidden" name="_method" value="PATCH">
                      <div class="row mb-3">
                          <!-- Profile Image -->
                          <div class="col-xl-12">
                              <div class="d-flex align-items-start flex-wrap gap-3">
                                  <div>
                                      <span class="avatar avatar-xxl">
                                          <img id="profilePreview"
                                              src="{{ $user->profile_image ? asset($user->profile_image) : '' }}"
                                              alt="profile">
                                      </span>
                                  </div>
                                  <div>
                                      <span class="fw-medium d-block mb-2">Profile Picture</span>
                                      <input type="file" name="profile_image" accept="image/*"
                                          class="form-control mb-2 d-none" id="profileImage">
                                      <button type="button" class="btn btn-sm btn-primary btn-wave"
                                          id="profileImageBtn"><i class="ri-upload-2-line me-1"></i>Change
                                          Image</button>
                                      <button type="button" class="btn btn-sm btn-success-light btn-wave"
                                          id="removeProfile">
                                          <i class="ri-delete-bin-line me-1"></i>Remove
                                      </button>
                                      <span class="d-block fs-12 text-muted">Use JPEG, PNG</span>
                                      <input type="hidden" name="remove_profile_image" id="removeProfileInput"
                                          value="0">
                                  </div>
                              </div>
                          </div>
                      </div>
                      <hr>
                      <div class="row mb-1">
                          <label for="name" class="required col-form-label col-md-2">Name</label>
                          <div class="form-group col-md-6">
                              <input type="text" name="name" id="name" class="form-control" required disabled
                                  autofocus autocomplete="name" placeholder="Enter user name"
                                  value="{{ $user->name ?? '' }}">
                              <span id="name_error"
                                  class="help-inline text-danger mt-2">{{ $errors->first('name') }}</span>
                          </div>
                      </div>
                      <div class="row mb-1">
                          <label for="email" class="required col-form-label col-md-2">Email</label>
                          <div class="form-group col-md-6">
                              <input type="email" name="email" id="email" class="form-control" required
                                  autofocus autocomplete="username" placeholder="Enter email"
                                  value="{{ $user->email ?? '' }}">
                              <span id="email_error"
                                  class="help-inline text-danger mt-2">{{ $errors->first('email') }}</span>
                          </div>
                      </div>
                      <div class="row mb-1">
                          <label for="phone" class="col-form-label col-md-2">Phone</label>
                          <div class="form-group col-md-6">
                              <input type="phone" name="phone" id="phone" class="form-control" autofocus
                                  autocomplete="username" placeholder="Enter phone" value="{{ $user->phone ?? '' }}">
                              <span id="phone_error"
                                  class="help-inline text-danger mt-2">{{ $errors->first('phone') }}</span>
                          </div>
                      </div>
                      {{-- <div class="row"> --}}
                      <div>
                          @if (!empty($user))
                              <button type="submit" class="btn btn-primary float-end"
                                  id="profile_form_button">Save</button>
                          @endif
                      </div>
                      {{-- </div> --}}
                  </form>
              </div>
          </div>
      </div>
  </div>
  {{-- <section>
      <header>
          <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
              Profile Information
          </h2>

          <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
              Update your account's profile information and email address.
          </p>
      </header>

      <!-- Email verification resend form -->
      <form id="send-verification" method="post" action="/email/verification-notification">
          <input type="hidden" name="_token" value="CSRF_TOKEN_HERE">
      </form>

      <!-- Profile update form -->
      <form method="post" action="/profile" class="mt-6 space-y-6">
          <input type="hidden" name="_token" value="CSRF_TOKEN_HERE">
          <input type="hidden" name="_method" value="PATCH">

          <!-- Name -->
          <div>
              <label for="name" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Name</label>
              <input id="name" name="name" type="text"
                  class="mt-1 block w-full border-gray-300 dark:border-gray-700 rounded-md shadow-sm" value="John Doe"
                  required autofocus autocomplete="name">
              <!-- Error messages for name -->
              <!-- <p class="text-sm text-red-600">Name is required.</p> -->
          </div>

          <!-- Email -->
          <div>
              <label for="email" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Email</label>
              <input id="email" name="email" type="email"
                  class="mt-1 block w-full border-gray-300 dark:border-gray-700 rounded-md shadow-sm"
                  value="john@example.com" required autocomplete="username">
              <!-- Error messages for email -->
              <!-- <p class="text-sm text-red-600">Invalid email address.</p> -->

              <!-- If email not verified -->
              <div>
                  <p class="text-sm mt-2 text-gray-800 dark:text-gray-200">
                      Your email address is unverified.
                      <button form="send-verification"
                          class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                          Click here to re-send the verification email.
                      </button>
                  </p>

                  <!-- After re-send success -->
                  <!-- <p class="mt-2 font-medium text-sm text-green-600 dark:text-green-400">
                    A new verification link has been sent to your email address.
                </p> -->
              </div>
          </div>

          <!-- Save Button -->
          <div class="flex items-center gap-4">
              <button type="submit"
                  class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                  Save
              </button>

              <!-- Saved message -->
              <!-- <p class="text-sm text-gray-600 dark:text-gray-400">Saved.</p> -->
          </div>
      </form>
  </section> --}}
