<!-- MODAL -->
<div class="modal fade" id="userPermissionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">

        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">User Permission - {{ $user->name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="permission_form">
                @csrf
                <div class="modal-body">

                    <!-- ROLE + DISPLAY -->
                    <div class="row mt-3">
                        <div class="col-md-4">
                            <label>Role</label>
                            <select id="role_select" class="form-control">
                                <option value="">Select role</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}" data-url="{{ route('user-permission-fetch') }}">
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2 mt-3 p-1">
                            <button id="display_btn" class="btn btn-primary w-100">Display</button>
                        </div>
                    </div>

                    <hr>
                    <div id="permission_form_container">
                        @include('admin.user_manager.permissions.permission-form')
                    </div>
                </div>

                <!-- Footer -->
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="ri-close-circle-line me-1"></i> Close
                    </button>

                    <button type="submit" id="update_permission_button"
                        data-url="{{ route('user.permission.update', $user->id) }}" class="btn btn-primary">
                        <i class="ri-save-3-line me-1"></i> Update Permissions
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>
