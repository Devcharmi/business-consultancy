<!-- Modal Body-->
<div class="modal fade" id="modalForm" tabindex="-1" status="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
    <div class="modal-dialog" status="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitleId">
                   {{ isset($focusAreaData) ? 'Edit Focus Area' : 'Create Focus Area' }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form
                action="{{ !empty($focusAreaData) ? route('focus-area-manager.update', $focusAreaData->id) : route('focus-area-manager.store') }}"
                method="POST" id="focus_area_form">
                @if (!empty($focusAreaData))
                    @method('PUT')
                @endif
                @csrf
                <div class="modal-body">
                    <div class="row mb-1 align-items-center">
                        <label for="name" class="col-md-3 col-form-label required">Name</label>
                        <div class="form-group col-md-9">
                            <input type="text" name="name" id="name" class="form-control"
                                value="{{ $focusAreaData->name ?? '' }}">
                            <span id="name_error"
                                class="help-inline text-danger mt-2">{{ $errors->first('name') }}</span>
                        </div>
                    </div>
                    <div class="row mb-1 align-items-center">
                        <label for="status" class="col-md-3 col-form-label">Status</label>
                        <div class="form-group col-md-6">
                            <select name="status" id="status" class="form-select">
                                <option value="1"
                                    {{ (isset($focusAreaData) && $focusAreaData->status == '1') || !isset($focusAreaData) ? 'selected' : 'selected' }}>
                                    Active
                                </option>
                                <option value="0"
                                    {{ isset($focusAreaData) && $focusAreaData->status == '0' ? 'selected' : '' }}>
                                    Deactive
                                </option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn" data-bs-dismiss="modal">Close</button>
                    @if (!empty($focusAreaData))
                        <button type="submit" class="btn btn-primary float-end" id="focus_area_form_button"
                            data-url="{{ route('focus-area-manager.update', ['focus_area_manager' => $focusAreaData->id]) }}">Update</button>
                    @else
                        <button type="submit"
                            class="btn btn-primary float-end {{ canAccess('focus-area.create') ? '' : 'disabled' }}"
                            id="focus_area_form_button" data-url="{{ route('focus-area-manager.store') }}">Submit</button>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>
