<!-- Modal -->
<div class="modal fade" id="rolePermissionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content shadow-lg">

            <!-- Header -->
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="ri-shield-user-line me-2"></i>
                    Role Permission For - {{ $role->name }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <form id="permission_form" method="put">
                @csrf

                <div class="modal-body">

                    <!-- Card Wrapper -->
                    <div class="card border-0 shadow-sm mb-3">
                        <div class="card-body pb-1">

                            {{-- <div class="d-flex align-items-center mb-3">
                                <input type="checkbox" id="masterSelectAll" class="me-2"
                                    style=" width: 18px;
        height: 18px;
        cursor: pointer;">
                                <label class="fw-bold">Select All Permissions</label>
                            </div> --}}

                            {{-- <div class="table-responsive"> --}}
                                <table class="table table-bordered table-striped align-middle">

                                    <thead class="table-light">
                                        <tr class="text-center bg-light">
                                            <th style="width: 60px;">
                                                <input type="checkbox" id="masterSelectAll">
                                            </th>
                                            <th class="text-start">Module</th>

                                            <th>
                                                <input type="checkbox" class="columnSelect" data-column="allow">
                                                <br><small class="text-muted">Allow</small>
                                            </th>

                                            <th>
                                                <input type="checkbox" class="columnSelect" data-column="create">
                                                <br><small class="text-muted">Create</small>
                                            </th>

                                            <th>
                                                <input type="checkbox" class="columnSelect" data-column="edit">
                                                <br><small class="text-muted">Edit</small>
                                            </th>

                                            <th>
                                                <input type="checkbox" class="columnSelect" data-column="delete">
                                                <br><small class="text-muted">Delete</small>
                                            </th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @foreach ($permissions as $group => $items)
                                            @php
                                                $allow = $items->firstWhere('name', $group . '.allow');
                                                $create = $items->firstWhere('name', $group . '.create');
                                                $edit = $items->firstWhere('name', $group . '.edit');
                                                $delete = $items->firstWhere('name', $group . '.delete');
                                            @endphp

                                            <tr>
                                                <td class="text-center">
                                                    <input type="checkbox" class="rowSelect">
                                                </td>

                                                <td class="fw-semibold text-primary">
                                                    <i class="ri-folder-2-line me-1 text-secondary"></i>
                                                    {{ ucfirst($group) }}
                                                </td>

                                                <td class="text-center">
                                                    @if ($allow)
                                                        <input type="checkbox" name="permissions[]"
                                                            value="{{ $allow->name }}"
                                                            class="permission-checkbox checkbox-allow"
                                                            {{ in_array($allow->name, $rolePermissions) ? 'checked' : '' }}>
                                                    @endif
                                                </td>

                                                <td class="text-center">
                                                    @if ($create)
                                                        <input type="checkbox" name="permissions[]"
                                                            value="{{ $create->name }}"
                                                            class="permission-checkbox checkbox-create"
                                                            {{ in_array($create->name, $rolePermissions) ? 'checked' : '' }}>
                                                    @endif
                                                </td>

                                                <td class="text-center">
                                                    @if ($edit)
                                                        <input type="checkbox" name="permissions[]"
                                                            value="{{ $edit->name }}"
                                                            class="permission-checkbox checkbox-edit"
                                                            {{ in_array($edit->name, $rolePermissions) ? 'checked' : '' }}>
                                                    @endif
                                                </td>

                                                <td class="text-center">
                                                    @if ($delete)
                                                        <input type="checkbox" name="permissions[]"
                                                            value="{{ $delete->name }}"
                                                            class="permission-checkbox checkbox-delete"
                                                            {{ in_array($delete->name, $rolePermissions) ? 'checked' : '' }}>
                                                    @endif
                                                </td>

                                            </tr>
                                        @endforeach
                                    </tbody>

                                </table>
                            {{-- </div> --}}

                        </div>
                    </div>

                </div>

                <!-- Footer -->
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="ri-close-circle-line me-1"></i> Close
                    </button>

                    <button type="submit" id="update_permission_button"
                        data-url="{{ route('give-role-permission', $role->id) }}" class="btn btn-primary">
                        <i class="ri-save-3-line me-1"></i> Update Permissions
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>
