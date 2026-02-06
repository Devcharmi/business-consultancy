<!-- Modal Body-->
<div class="modal fade" id="modalForm" tabindex="-1" status="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
    <div class="modal-dialog" status="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitleId">
                    {{ isset($statusData) ? 'Edit Status' : 'Create Status' }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form
                action="{{ !empty($statusData) ? route('status-manager.update', $statusData->id) : route('status-manager.store') }}"
                method="POST" id="status_form">
                @if (!empty($statusData))
                    @method('PUT')
                @endif
                @csrf
                <div class="modal-body">
                    <div class="row mb-1 align-items-center">
                        <label for="name" class="col-md-3 col-form-label required">Name</label>
                        <div class="form-group col-md-6">
                            <input type="text" name="name" id="name" class="form-control"
                                value="{{ $statusData->name ?? '' }}">
                            <span id="name_error"
                                class="help-inline text-danger mt-2">{{ $errors->first('name') }}</span>
                        </div>
                    </div>
                    <div class="row mb-1 align-items-center">
                        <label for="color_name" class="col-md-3 col-form-label">Color Name</label>
                        <div class="form-group col-md-6">
                            <input type="text" name="color_name" id="color_name" class="form-control"
                                value="{{ $statusData->color_name ?? '' }}">
                            <span id="color_name_error"
                                class="help-inline text-danger mt-2">{{ $errors->first('color_name') }}</span>
                        </div>
                    </div>
                    <div class="row mb-1 align-items-center">
                        <label for="status" class="col-md-3 col-form-label">Status</label>
                        <div class="form-group col-md-6">
                            <select name="status" id="status" class="form-select">
                                <option value="1"
                                    {{ (isset($statusData) && $statusData->status == '1') || !isset($statusData) ? 'selected' : 'selected' }}>
                                    Active
                                </option>
                                <option value="0"
                                    {{ isset($statusData) && $statusData->status == '0' ? 'selected' : '' }}>
                                    Deactive
                                </option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn" data-bs-dismiss="modal">Close</button>
                    @if (!empty($statusData))
                        <button type="submit" class="btn btn-primary float-end" id="status_form_button"
                            data-url="{{ route('status-manager.update', ['status_manager' => $statusData->id]) }}">Update</button>
                    @else
                        <button type="submit"
                            class="btn btn-primary float-end {{ canAccess('status manager.create') ? '' : 'disabled' }}"
                            id="status_form_button" data-url="{{ route('status-manager.store') }}">Submit</button>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>
