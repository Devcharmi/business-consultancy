  <div class="row">
      <div class="col-xl-12">
          <div class="card custom-card">
              <div class="card-header d-flex justify-content-between align-items-center">
                  <div class="card-title">
                      User Information
                      {{-- @if (!empty($userData))
                          <a href="javascript:void(0);"
                              data-url="{{ route('user.permission.modal', ['user' => $userData->id]) }}"
                              title="Give Permission" data-tool-tips="Give Permission"
                              class="btn btn-success user-permission-modal">
                              <i class="fas fa-cog p-1"></i>User Permission
                          </a>
                      @endif --}}
                  </div>
                  <a href="{{ route('user-manager.index') }}" class="btn btn-primary mt-10 d-block text-center">Back</a>
              </div>
              <div class="card-body">
                  <!-- User update form -->

                  <div class="row mb-3">
                      <!-- User Image -->
                      <div class="col-xl-12">
                          <div class="d-flex align-items-start flex-wrap gap-3">
                              <div>
                                  <span class="avatar avatar-xxl">
                                      <img id="profilePreview"
                                          src="{{ !empty($userData) && $userData->profile_image ? asset($userData->profile_image) : '' }}"
                                          alt="profile">
                                  </span>
                              </div>
                              <div>
                                  <span class="fw-medium d-block mb-2">User Picture</span>
                                  <input type="file" name="profile_image" accept="image/*"
                                      class="form-control mb-2 d-none" id="profileImage">
                                  <button type="button" class="btn btn-sm btn-primary btn-wave" id="profileImageBtn"><i
                                          class="ri-upload-2-line me-1"></i>Change
                                      Image</button>
                                  <button type="button" class="btn btn-sm btn-success-light btn-wave" id="removeUser">
                                      <i class="ri-delete-bin-line me-1"></i>Remove
                                  </button>
                                  <span class="d-block fs-12 text-muted">Use JPEG, PNG</span>
                                  <input type="hidden" name="remove_profile_image" id="removeUserInput" value="0">
                              </div>
                          </div>
                      </div>
                  </div>
                  <hr>
                  <div class="row mb-1">
                      <label for="name" class="required col-form-label col-md-2">Name</label>
                      <div class="form-group col-md-6">
                          <input type="text" name="name" id="name" class="form-control" autocomplete="name"
                              placeholder="Enter user name" value="{{ $userData->name ?? '' }}">
                          <span id="name_error" class="help-inline text-danger mt-2">{{ $errors->first('name') }}</span>
                      </div>
                  </div>
                  <div class="row mb-1">
                      <label for="email" class="required col-form-label col-md-2">Email</label>
                      <div class="form-group col-md-6">
                          <input type="email" name="email" id="email" class="form-control" autocomplete="email"
                              placeholder="Enter email" value="{{ $userData->email ?? '' }}">
                          <span id="email_error"
                              class="help-inline text-danger mt-2">{{ $errors->first('email') }}</span>
                      </div>
                  </div>
                  <div class="row mb-1">
                      <label for="phone" class="col-form-label col-md-2">Phone</label>
                      <div class="form-group col-md-6">
                          <input type="phone" name="phone" id="phone" class="form-control" autocomplete="phone"
                              placeholder="Enter phone" value="{{ $userData->phone ?? '' }}">
                          <span id="phone_error"
                              class="help-inline text-danger mt-2">{{ $errors->first('phone') }}</span>
                      </div>
                  </div>
                  <div class="row mb-1">
                      <label for="role" class="col-form-label col-md-2">Roles</label>
                      <div class="form-group col-md-6">
                          <select name="role" id="role" class="form-select">
                              @foreach ($roles as $role)
                                  <option value="{{ $role->name }}"
                                      {{ isset($userRole) && $userRole == $role->id ? 'selected' : '' }}>
                                      {{ $role->name }}</option>
                              @endforeach
                          </select>
                          <span id="role_error"
                              class="help-inline text-danger mt-2">{{ $errors->first('role') }}</span>
                      </div>
                  </div>
                  <div class="row mb-1">
                      <label for="expertise_manager_ids" class="col-form-label col-md-2">
                          Expertises
                      </label>

                      <div class="form-group col-md-6">
                          <select name="expertise_manager_ids[]" id="expertise_manager_ids" class="form-control select2"
                              multiple>

                              @foreach ($expertises as $expertise)
                                  <option value="{{ $expertise->id }}"
                                      {{ isset($userExpertiseIds) && in_array($expertise->id, $userExpertiseIds) ? 'selected' : '' }}>
                                      {{ $expertise->name }}
                                  </option>
                              @endforeach
                          </select>

                          <span class="help-inline text-danger mt-2">
                              {{ $errors->first('expertise_manager_ids') }}
                          </span>
                      </div>
                  </div>

              </div>
          </div>
      </div>
  </div>
