<!-- Modal Body-->
<div class="modal fade" id="modalForm" tabindex="-1" status="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
    <div class="modal-dialog" status="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitleId">
                   {{ isset($ClientObjectiveData) ? 'Edit Client Objective' : 'Create Client Objective' }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form
                action="{{ !empty($ClientObjectiveData) ? route('client-objective-manager.update', $ClientObjectiveData->id) : route('client-objective-manager.store') }}"
                method="POST" id="client_objective_form">
                @if (!empty($ClientObjectiveData))
                    @method('PUT')
                @endif
                @csrf
                <div class="modal-body">

                    <div class="row">
                        {{-- Client --}}
                        <div class="col-md-12 mb-3">
                            <label>Client</label>
                            <select name="client_id" id="client_id" class="form-control select2"
                                {{ isset($ClientObjectiveData) ? 'disabled' : '' }}>
                                <option value="">Select Client</option>
                                @foreach ($clients as $client)
                                    <option value="{{ $client->id }}"
                                        {{ isset($ClientObjectiveData) && $ClientObjectiveData->client_id == $client->id ? 'selected' : '' }}>
                                        {{ $client->client_name }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-danger" id="client_id_error"></small>
                        </div>

                        {{-- Objective --}}
                        <div class="col-md-12 mb-3">
                            <label>Objective</label><span class="text-danger">*</span>
                            <select name="objective_manager_id" class="form-control select2">
                                <option value="">Select Objective</option>

                                @foreach ($objectives as $objective)
                                    <option value="{{ $objective->id }}" @selected(old('objective_manager_id', $ClientObjectiveData->objective_manager_id ?? null) == $objective->id)>
                                        {{ $objective->name }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-danger" id="objective_manager_id_error"></small>
                        </div>

                        {{-- Note --}}
                        <div class="col-md-12 mb-3">
                            <label>Note</label>
                            <textarea name="note" id="note" class="form-control" rows="3">{{ $ClientObjectiveData->note ?? '' }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn" data-bs-dismiss="modal">Close</button>
                    @if (!empty($ClientObjectiveData))
                        <button type="submit" class="btn btn-primary float-end" id="client_objective_form_button"
                            data-url="{{ route('client-objective-manager.update', ['client_objective_manager' => $ClientObjectiveData->id]) }}">Update</button>
                    @else
                        <button type="submit"
                            class="btn btn-primary float-end {{ canAccess('client-objective.create') ? '' : 'disabled' }}"
                            id="client_objective_form_button"
                            data-url="{{ route('client-objective-manager.store') }}">Submit</button>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>
