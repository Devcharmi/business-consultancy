<!-- Modal Body-->
<div class="modal fade" id="modalForm" tabindex="-1" role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitleId">
                    Role
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ !empty($roleData) ? route('role.update', $roleData->id) : route('role.store') }}"
                method="POST" id="role_form">
                @if (!empty($roleData))
                    @method('PUT')
                @endif
                @csrf
                <div class="modal-body">
                    <div class="row g-3 align-items-center">
                        <div class="col-auto">
                            <label for="name" class="col-form-label required">Name</label>
                        </div>
                        <div class="col-auto">
                            <input type="text" name="name" id="name" class="form-control"
                                value="{{ $roleData->name ?? '' }}">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn" data-bs-dismiss="modal">Close</button>
                    @if (!empty($roleData))
                        <button type="submit" class="btn btn-primary float-end" id="role_form_button"
                            data-url="{{ route('role.update', ['role' => $roleData->id]) }}">Update</button>
                    @else
                        <button type="submit" class="btn btn-primary float-end" id="role_form_button"
                            data-url="{{ route('role.store') }}">Submit</button>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>
