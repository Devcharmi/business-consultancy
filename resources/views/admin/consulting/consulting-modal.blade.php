<!-- Modal Body-->
<div class="modal fade" id="consultingModal" tabindex="-1" status="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
    <div class="modal-dialog" status="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitleId">
                    {{ isset($consultingData) ? 'Edit Consulting' : 'Create Consulting' }}
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
                            <label class="required">Client</label>
                            <select name="client_id" class="form-control select2">
                                <option value="">Select Client</option>
                                @foreach ($clients as $client)
                                    <option value="{{ $client->id }}" @selected(old('client_id', $consultingData->client_objective->client_id ?? null) == $client->id)>
                                        {{ $client->client_name }}
                                    </option>
                                @endforeach
                            </select>

                            <small class="text-danger" id="client_id_error"></small>
                        </div>

                        {{-- Objective --}}
                        <div class="col-md-12 mb-3">
                            <label class="required">Objective</label>
                            <select name="objective_manager_id" class="form-control select2">
                                <option value="">Select Objective</option>
                                @foreach ($objectives as $obj)
                                    <option value="{{ $obj->id }}" @selected(old('objective_manager_id', $consultingData->client_objective->objective_manager_id ?? null) == $obj->id)>
                                        {{ $obj->name }}
                                    </option>
                                @endforeach
                            </select>

                            <small class="text-danger" id="objective_manager_id_error"></small>
                        </div>

                        {{-- Expertise --}}
                        <div class="col-md-12 mb-3">
                            <label class="required">Expertises</label>
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
                            <label class="required">Focus Area</label>
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
                        {{-- Consulting Date --}}
                        <div class="col-md-4 mb-3">
                            <label class="required">Date</label>
                            <input type="date" name="consulting_date" id="consulting_date"
                                class="form-control form-control-sm"
                                value="{{ old('consulting_date', isset($consultingData) ? $consultingData->consulting_date?->format('Y-m-d') : '') }}">
                            <small class="text-danger" id="consulting_date_error"></small>
                        </div>

                        {{-- Start Time --}}
                        <div class="col-md-4 mb-3">
                            <label class="required">Start Time</label>
                            <input type="time" name="start_time" id="start_time" class="form-control form-control-sm"
                                value="{{ old('start_time', isset($consultingData->start_time) ? \Carbon\Carbon::parse($consultingData->start_time)->format('H:i') : '') }}">

                            <small class="text-danger" id="start_time_error"></small>
                        </div>

                        {{-- End Time --}}
                        <div class="col-md-4 mb-3">
                            <label class="required">End Time</label>
                            <input type="time" name="end_time" id="end_time" class="form-control form-control-sm"
                                value="{{ old('end_time', isset($consultingData->end_time) ? \Carbon\Carbon::parse($consultingData->end_time)->format('H:i') : '') }}">
                            <small class="text-danger" id="end_time_error"></small>
                        </div>

                        {{-- <div class="col-md-6 mb-3">
                            <label>Date</label>
                            <input type="datetime-local" id="consulting_datetime" name="consulting_datetime"
                                class="form-control form-control-sm"
                                value="{{ $consultingData->consulting_datetime ?? '' ? \Carbon\Carbon::parse($consultingData->consulting_datetime)->format('Y-m-d\TH:i') : '' }}">
                        </div> --}}
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
