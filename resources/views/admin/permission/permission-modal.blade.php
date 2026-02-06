<!-- Modal Body-->
<div class="modal fade" id="modalForm" tabindex="-1" role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitleId">
                    {{ isset($permissionData) ? 'Edit Permission' : 'Create Permission' }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form
                action="{{ !empty($permissionData) ? route('permissions.update', $permissionData->id) : route('permissions.store') }}"
                method="POST" id="permission_form">
                @if (!empty($permissionData))
                    @method('PUT')
                @endif
                @csrf
                <div class="modal-body">
                    <div class="row mb-1 align-items-center">
                        <label for="name" class="col-md-4 col-form-label required">Permission Name</label>
                        <div class="form-group col-md-6">
                            <input type="text" name="name" id="name" class="form-control"
                                value="{{ $permissionData->name ?? '' }}">
                            <span id="name_error"
                                class="help-inline text-danger mt-2">{{ $errors->first('name') }}</span>
                        </div>
                    </div>
                    @if (empty($permissionData))
                        <div class="row mb-1 align-items-center">
                            <label class="col-md-4 col-form-label">Create full set?</label>
                            <div class="col-md-8">
                                <label style="cursor:pointer;">
                                    <input type="checkbox" name="full_set" id="full_set" value="1">
                                    <span class="ms-1">Create allow/create/edit/delete permissions</span>
                                </label>
                            </div>
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn" data-bs-dismiss="modal">Close</button>
                    @if (!empty($permissionData))
                        <button type="submit" class="btn btn-primary" id="permission_form_button"
                            data-url="{{ route('permissions.update', ['permission' => $permissionData->id]) }}">Submit</button>
                    @else
                        <button type="submit" class="btn btn-primary" id="permission_form_button"
                            data-url="{{ route('permissions.store') }}">Submit</button>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>
