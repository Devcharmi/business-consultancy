  <div class="row">
      <div class="col-xl-12">
          <div class="card custom-card">
              <div class="card-header">
                  <div class="card-title">
                      Password
                  </div>
              </div>
              <div class="card-body">
                  {{-- <!-- Current Password -->
                  @if (!empty($userData))
                      <div class="row mb-1">
                          <label for="current_password" class="required col-form-label col-md-2">Current
                              Password</label>
                          <div class="form-group col-md-6">
                              <input type="password" name="current_password" id="current_password" class="form-control"
                                  autocomplete="current-password">
                              <span id="current_password_error"
                                  class="help-inline text-danger mt-2">{{ $errors->first('current_password') }}</span>
                          </div>
                      </div>
                  @endif --}}
                  <!-- New Password -->
                  <div class="row mb-1">
                      @if (!empty($userData))
                          <label for="password" class="required col-form-label col-md-2">New
                              Password</label>
                      @else
                          <label for="password" class="required col-form-label col-md-2">Password</label>
                      @endif
                      <div class="form-group col-md-6">
                          <input type="password" name="password" id="password" class="form-control"
                              autocomplete="new-password">
                          <span id="password_error"
                              class="help-inline text-danger mt-2">{{ $errors->first('password') }}</span>
                      </div>
                  </div>

                  <!-- Confirm Password -->
                  <div class="row mb-1">
                      <label for="password_confirmation" class="col-form-label col-md-2">Confirm
                          Password</label>
                      <div class="form-group col-md-6">
                          <input type="password" name="password_confirmation" id="password_confirmation"
                              class="form-control" autocomplete="new-password">
                      </div>
                  </div>
                  <div>
                      @if (!empty($userData))
                          <button type="button" class="btn btn-primary float-end"
                              data-url="{{ route('user-manager.update', ['user_manager' => $userData->id]) }}"
                              id="user_form_button">Update</button>
                      @else
                          <button type="button" class="btn btn-primary float-end" id="user_form_button"
                              data-url="{{ route('user-manager.store') }}">Submit</button>
                      @endif
                  </div>
              </div>
          </div>
      </div>
  </div>
