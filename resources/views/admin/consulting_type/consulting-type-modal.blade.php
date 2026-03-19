<!-- Modal Body-->
<div class="modal fade" id="modalForm" tabindex="-1" status="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
    <div class="modal-dialog" status="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitleId">
                    {{ isset($consultingTypeData) ? 'Edit Consulting Type' : 'Create Consulting Type' }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form
                action="{{ !empty($consultingTypeData) ? route('consulting-type.update', $consultingTypeData->id) : route('consulting-type.store') }}"
                method="POST" id="consulting_type_form">
                @if (!empty($consultingTypeData))
                    @method('PUT')
                @endif
                @csrf
                <div class="modal-body">
                    <div class="row mb-1 align-items-center">
                        <label for="name" class="col-md-3 col-form-label required">Name</label>
                        <div class="form-group col-md-9">
                            <input type="text" name="name" id="name" class="form-control"
                                value="{{ $consultingTypeData->name ?? '' }}">
                            <span id="name_error"
                                class="help-inline text-danger mt-2">{{ $errors->first('name') }}</span>
                        </div>
                    </div>
                    <div class="row mb-1 align-items-center">
                        <label for="status" class="col-md-3 col-form-label">Status</label>
                        <div class="form-group col-md-6">
                            <select name="status" id="status" class="form-select">
                                <option value="1"
                                    {{ (isset($consultingTypeData) && $consultingTypeData->status == '1') || !isset($consultingTypeData) ? 'selected' : 'selected' }}>
                                    Active
                                </option>
                                <option value="0"
                                    {{ isset($consultingTypeData) && $consultingTypeData->status == '0' ? 'selected' : '' }}>
                                    Deactive
                                </option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn" data-bs-dismiss="modal">Close</button>
                    @if (!empty($consultingTypeData))
                        <button type="submit" class="btn btn-primary float-end" id="consulting_type_form_button"
                            data-url="{{ route('consulting-type.update', ['consulting_type' => $consultingTypeData->id]) }}">Update</button>
                    @else
                        <button type="submit"
                            class="btn btn-primary float-end {{ canAccess('consulting_type.create') ? '' : 'disabled' }}"
                            id="consulting_type_form_button" data-url="{{ route('consulting-type.store') }}">Submit</button>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>
