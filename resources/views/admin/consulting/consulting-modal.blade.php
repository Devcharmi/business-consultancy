<!-- Modal Body-->
<div class="modal fade" id="consultingForm" tabindex="-1" status="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
    <div class="modal-dialog" status="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitleId">
                    Client Objective Manager
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form
                action="{{ !empty($ConsultingData) ? route('consulting.update', $ConsultingData->id) : route('consulting.store') }}"
                method="POST" id="consulting_form">
                @if (!empty($ConsultingData))
                    @method('PUT')
                @endif
                @csrf
                <div class="modal-body">

                    {{-- Client --}}
                    <div class="col-md-12 mb-3">
                        <label>Client Objective</label>
                        <select name="client_objective_id" class="form-control select2">
                            <option value="">Select Client Objective</option>

                            @foreach ($clientObjectives as $co)
                                <option value="{{ $co->id }}">
                                    {{ $co->client->client_name }} - {{ $co->objectiveManager->name }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-danger" id="client_objective_id_error"></small>
                    </div>

                    {{-- Expertise --}}
                    <div class="col-md-12 mb-3">
                        <label>Expertises</label><span class="text-danger">*</span>
                        <select name="expertise_manager_id" class="form-control select2">
                            <option value="">Select Expertises</option>

                            @foreach ($expertises as $expertise)
                                <option value="{{ $expertise->id }}" @selected(old('expertise_manager_id', $ConsultingData->expertise_manager_id ?? null) == $expertise->id)>
                                    {{ $expertise->name }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-danger" id="expertise_manager_id_error"></small>
                    </div>

                    {{-- focusArea --}}
                    <div class="col-md-12 mb-3">
                        <label>Focus Area</label><span class="text-danger">*</span>
                        <select name="focus_area_manager_id" class="form-control select2">
                            <option value="">Select focus Area</option>

                            @foreach ($focusAreas as $focusArea)
                                <option value="{{ $focusArea->id }}" @selected(old('focus_area_manager_id', $ConsultingData->focus_area_manager_id ?? null) == $focusArea->id)>
                                    {{ $focusArea->name }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-danger" id="focus_area_manager_id_error"></small>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>Date</label>
                        <input type="datetime-local" id="consulting_datetime" name="consulting_datetime"
                            class="form-control form-control-sm">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn" data-bs-dismiss="modal">Close</button>
                    @if (!empty($ConsultingData))
                        <button type="submit" class="btn btn-primary float-end" id="consulting_form_button"
                            data-url="{{ route('consulting.update', ['consulting' => $ConsultingData->id]) }}">Update</button>
                    @else
                        <button type="submit"
                            class="btn btn-primary float-end {{ canAccess('client-objective.create') ? '' : 'disabled' }}"
                            id="consulting_form_button"
                            data-url="{{ route('consulting.store') }}">Submit</button>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>
