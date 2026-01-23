<!-- Modal Body-->
<div class="modal fade" id="consultingForm" tabindex="-1" status="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
    <div class="modal-dialog" status="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitleId">
                    Consulting Manager
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form
                action="{{ !empty($consultingData) ? route('consulting.update', $consultingData->id) : route('consulting.store') }}"
                method="POST" id="consulting_form">
                @if (!empty($consultingData))
                    @method('PUT')
                @endif
                @csrf

                @if (!empty($taskId))
                    <input type="hidden" name="task_id" value="{{ $taskId }}">
                @endif

                <div class="modal-body">
                    <div class="row">
                        {{-- Client --}}
                        <div class="col-md-12 mb-3">
                            <label>Client Objective</label>
                            <select name="client_objective_id" class="form-control select2">
                                <option value="">Select Client Objective</option>
                                @foreach ($clientObjectives as $co)
                                    <option value="{{ $co->id }}" @selected(old('client_objective_id', $consultingData->client_objective_id ?? null) == $co->id)>
                                        {{ $co->client->client_name }} - {{ $co->objective_manager->name }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-danger" id="client_objective_id_error"></small>
                        </div>

                        {{-- Expertise --}}
                        <div class="col-md-12 mb-3">
                            <label>Expertises</label><span class="text-danger">*</span>
                            <select name="expertise_manager_id" class="form-select">
                                @foreach ($expertises as $expertise)
                                    <option value="{{ $expertise->id }}" @selected(old('expertise_manager_id', $consultingData->expertise_manager_id ?? null) == $expertise->id)>
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
                                    <option value="{{ $focusArea->id }}" @selected(old('focus_area_manager_id', $consultingData->focus_area_manager_id ?? null) == $focusArea->id)>
                                        {{ $focusArea->name }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-danger" id="focus_area_manager_id_error"></small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Date</label>
                            <input type="datetime-local" id="consulting_datetime" name="consulting_datetime"
                                class="form-control form-control-sm"
                                value="{{ $consultingData->consulting_datetime ?? '' ? \Carbon\Carbon::parse($consultingData->consulting_datetime)->format('Y-m-d\TH:i') : '' }}">
                        </div>
                    </div>


                </div>
                <div class="modal-footer">
                    <button type="button" class="btn" data-bs-dismiss="modal">Close</button>
                    @if (!empty($consultingData))
                        <button type="submit" class="btn btn-primary float-end" id="consulting_form_button"
                            data-url="{{ route('consulting.update', ['consulting' => $consultingData->id]) }}">Update</button>
                    @else
                        <button type="submit"
                            class="btn btn-primary float-end {{ canAccess('client-objective.create') ? '' : 'disabled' }}"
                            id="consulting_form_button" data-url="{{ route('consulting.store') }}">Submit</button>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>
