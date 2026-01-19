  <div class="row">
      <div class="col-xl-12">
          <div class="card custom-card">
              <div class="card-header">
                  <div class="card-title">
                      Update Password
                  </div>
              </div>
              <div class="card-body">
                  <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
                      <!-- CSRF Token -->
                      @csrf
                      <!-- PUT method -->
                      <input type="hidden" name="_method" value="PUT">

                      @if (auth()->user()->hasRole(['Super Admin', 'Admin']))
                          <div class="row mb-1">
                              <label for="update_password_current_password" class="required col-form-label col-md-2">
                                  Current Password
                              </label>
                              <div class="form-group col-md-6">
                                  <input type="password" name="current_password" id="update_password_current_password"
                                      class="form-control" autocomplete="current-password">
                                  <span class="help-inline text-danger mt-2">
                                      {{ $errors->first('current_password') }}
                                  </span>
                              </div>
                          </div>
                      @endif

                      <!-- New Password -->
                      <div class="row mb-1">
                          <label for="update_password_password" class="required col-form-label col-md-2">New
                              Password</label>
                          <div class="form-group col-md-6">
                              <input type="password" name="password" id="update_password_password" class="form-control"
                                  autocomplete="new-password">
                              <span id="password_error"
                                  class="help-inline text-danger mt-2">{{ $errors->first('password') }}</span>
                          </div>
                      </div>

                      <!-- Confirm Password -->
                      <div class="row mb-1">
                          <label for="update_password_password_confirmation" class="col-form-label col-md-2">Confirm
                              Password</label>
                          <div class="form-group col-md-6">
                              <input type="password" name="password_confirmation"
                                  id="update_password_password_confirmation" class="form-control"
                                  autocomplete="new-password">
                          </div>
                      </div>

                      <div>
                          <button type="submit" class="btn btn-primary float-end">Save</button>
                      </div>
                  </form>
              </div>
          </div>
      </div>
  </div>
  {{-- <section>
      <header>
          <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
              Update Password
          </h2>

          <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
              Ensure your account is using a long, random password to stay secure.
          </p>
      </header>

      <form method="post" action="/user/password" class="mt-6 space-y-6">
          <!-- CSRF Token -->
          <input type="hidden" name="_token" value="CSRF_TOKEN_HERE">
          <!-- PUT method -->
          <input type="hidden" name="_method" value="PUT">

          <!-- Current Password -->
          <div>
              <label for="update_password_current_password"
                  class="block font-medium text-sm text-gray-700 dark:text-gray-300">
                  Current Password
              </label>
              <input id="update_password_current_password" name="current_password" type="password"
                  class="mt-1 block w-full border-gray-300 dark:border-gray-700 rounded-md shadow-sm"
                  autocomplete="current-password">
              <!-- Error message -->
              <!-- <p class="text-sm text-red-600">Current password is incorrect.</p> -->
          </div>

          <!-- New Password -->
          <div>
              <label for="update_password_password" class="block font-medium text-sm text-gray-700 dark:text-gray-300">
                  New Password
              </label>
              <input id="update_password_password" name="password" type="password"
                  class="mt-1 block w-full border-gray-300 dark:border-gray-700 rounded-md shadow-sm"
                  autocomplete="new-password">
              <!-- Error message -->
              <!-- <p class="text-sm text-red-600">Password must be at least 8 characters.</p> -->
          </div>

          <!-- Confirm Password -->
          <div>
              <label for="update_password_password_confirmation"
                  class="block font-medium text-sm text-gray-700 dark:text-gray-300">
                  Confirm Password
              </label>
              <input id="update_password_password_confirmation" name="password_confirmation" type="password"
                  class="mt-1 block w-full border-gray-300 dark:border-gray-700 rounded-md shadow-sm"
                  autocomplete="new-password">
              <!-- Error message -->
              <!-- <p class="text-sm text-red-600">Passwords do not match.</p> -->
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
