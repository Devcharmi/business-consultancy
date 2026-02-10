@extends('admin.layouts.app')
@section('content')
    <div class="row mt-4">
        <div class="col-xl-12">
            <div class="card custom-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="card-title">
                        {{ isset($taskData) ? 'Edit Task' : 'Create Task' }}
                    </div>
                    <a href="{{ route('user-task.index') }}" class="btn btn-primary mt-10 d-block text-center">Back</a>
                </div>
                <div class="card-body">
                    <form method="POST" id="task_form" class="mt-6 space-y-6">
                        @if (!empty($taskData))
                            @method('PUT')
                        @endif
                        @csrf

                        <div class="row mb-1 align-items-center">
                            <label for="task_name" class="required col-form-label col-md-2">Task</label>
                            <div class="form-group col-md-8">
                                <input type="text" name="task_name" id="task_name" class="form-control"
                                    value="{{ $taskData->task_name ?? '' }}">
                                <span id="task_name_error"
                                    class="help-inline text-danger mt-2">{{ $errors->first('task_name') }}</span>
                            </div>
                        </div>
                        <div class="row mb-1">
                            <label class="required col-md-2 col-form-label">For</label>
                            <div class="form-group col-md-4">
                                <select name="entity_type" id="entity_type" class="form-select">
                                    <option value="client"
                                        {{ isset($taskData) && $taskData->entity_type == 'client' ? 'selected' : '' }}>
                                        Client
                                    </option>
                                    <option value="lead"
                                        {{ isset($taskData) && $taskData->entity_type == 'lead' ? 'selected' : '' }}>
                                        Lead
                                    </option>
                                </select>
                                <span class="text-danger">{{ $errors->first('entity_type') }}</span>
                            </div>
                            <label class="required col-md-2 col-form-label text-end">Type</label>
                            <div class="form-group col-md-4">
                                <select name="task_type" id="task_type" class="form-select">
                                    <option value="task"
                                        {{ isset($taskData) && $taskData->task_type == 'task' ? 'selected' : '' }}>
                                        Task
                                    </option>
                                    <option value="meeting"
                                        {{ isset($taskData) && $taskData->task_type == 'meeting' ? 'selected' : '' }}>
                                        Meeting
                                    </option>
                                </select>
                                <span class="text-danger">{{ $errors->first('task_type') }}</span>
                            </div>
                        </div>

                        <div class="row mb-1">
                            {{-- <label for="client_id" class="col-md-2 col-form-label">Related to</label>
                            <div class="form-group col-md-4">
                                <select name="client_id" id="client_id" class="form-select select2">
                                    <option value="">Select Client</option>
                                    @foreach ($clients as $client)
                                        <option value="{{ $client->id }}"
                                            {{ isset($taskData) && $taskData->client_id == $client->id ? 'selected' : '' }}>
                                            {{ $client->client_name }}</option>
                                    @endforeach
                                </select>
                                <span id="client_id_error"
                                    class="help-inline text-danger mt-2">{{ $errors->first('client_id') }}</span>
                            </div> --}}
                            <label class="col-md-2 col-form-label">Related to</label>

                            {{-- CLIENT DROPDOWN --}}
                            <div class="form-group col-md-4" id="client_wrapper">
                                <select name="client_id" id="client_id" class="form-select select2">
                                    <option value="">Select Client</option>
                                    @foreach ($clients as $client)
                                        <option value="{{ $client->id }}"
                                            {{ isset($taskData) && $taskData->client_id == $client->id ? 'selected' : '' }}>
                                            {{ $client->client_name }}
                                        </option>
                                    @endforeach
                                </select>
                                <span class="text-danger">{{ $errors->first('client_id') }}</span>
                            </div>

                            {{-- LEAD DROPDOWN --}}
                            <div class="form-group col-md-4 d-none" id="lead_wrapper">
                                <select name="lead_id" id="lead_id" class="form-select select2">
                                    <option value="">Select Lead</option>
                                    @foreach ($leads as $lead)
                                        <option value="{{ $lead->id }}"
                                            {{ isset($taskData) && $taskData->lead_id == $lead->id ? 'selected' : '' }}>
                                            {{ $lead->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <span class="text-danger">{{ $errors->first('lead_id') }}</span>
                            </div>

                            @if (auth()->user()->hasRole(['Super Admin', 'Admin']))
                                {{-- <div class="row mb-1"> --}}
                                <label for="staff_manager_id" class="required col-form-label col-md-2 text-end">Assign
                                    to</label>
                                <div class="form-group col-md-4">
                                    <select name="staff_manager_id" id="staff_manager_id" class="form-control select2">
                                        <option value="">Select</option>
                                        @foreach ($staffs as $staff)
                                            <option value="{{ $staff->id }}"
                                                {{ isset($taskData) && $taskData->staff_manager_id == $staff->id ? 'selected' : '' }}>
                                                {{ $staff->name }}</option>
                                        @endforeach
                                    </select>
                                    <span id="staff_manager_id_error"
                                        class="help-inline text-danger mt-2">{{ $errors->first('staff_manager_id') }}</span>
                                </div>
                            @else
                                <input type="hidden" name="staff_manager_id" id="staff_manager_id"
                                    value="{{ isset($taskData) ? $taskData->staff_manager_id : auth()->id() }}">
                            @endif
                        </div>

                        <div class="row mb-1">
                            <label for="task_start_date" class="col-md-2 col-form-label">Start Date</label>
                            <div class="form-group col-md-2">
                                <input type="date" name="task_start_date" id="task_start_date" class="form-control"
                                    value="{{ isset($taskData->task_start_date) ? \Carbon\Carbon::parse($taskData->task_start_date)->format('Y-m-d') : date('Y-m-d') }}">
                                <span id="task_start_date_error"
                                    class="help-inline text-danger mt-2">{{ $errors->first('task_start_date') }}</span>
                            </div>
                            <label for="task_due_date" class="col-md-2 col-form-label text-end">Due Date</label>
                            <div class="form-group col-md-2">
                                <input type="date" name="task_due_date" id="task_due_date" class="form-control"
                                    value="{{ isset($taskData->task_due_date) ? \Carbon\Carbon::parse($taskData->task_due_date)->format('Y-m-d') : date('Y-m-d') }}">
                                <span id="task_due_date_error"
                                    class="help-inline text-danger mt-2">{{ $errors->first('task_due_date') }}</span>
                            </div>
                            <label for="task_end_date" class="col-md-2 col-form-label text-end">End Date</label>
                            <div class="form-group col-md-2">
                                <input type="date" name="task_end_date" id="task_end_date" class="form-control"
                                    value="{{ isset($taskData->task_end_date) ? \Carbon\Carbon::parse($taskData->task_end_date)->format('Y-m-d') : '' }}">
                                <span id="task_end_date_error"
                                    class="help-inline text-danger mt-2">{{ $errors->first('task_end_date') }}</span>
                            </div>
                        </div>
                        <div class="row mb-1">
                            <label for="priority_manager_id" class="col-md-2 col-form-label">Priority</label>
                            <div class="form-group col-md-3">
                                <select name="priority_manager_id" id="priority_manager_id" class="form-select">
                                    @foreach ($priorities as $priority)
                                        <option value="{{ $priority->id }}"
                                            {{ isset($taskData) && $taskData->priority_manager_id == $priority->id ? 'selected' : '' }}>
                                            {{ $priority->name }}</option>
                                    @endforeach
                                </select>
                                <span id="priority_manager_id_error"
                                    class="help-inline text-danger mt-2">{{ $errors->first('priority_manager_id') }}</span>
                            </div>
                            <label class="col-md-1 col-form-label required">Status</label>
                            <div class="form-group col-md-3">
                                <select name="status_manager_id" class="form-select">
                                    @foreach ($statuses as $status)
                                        <option value="{{ $status->id }}"
                                            {{ isset($taskData) && $taskData->status_manager_id == $status->id ? 'selected' : '' }}>
                                            {{ $status->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-danger"
                                    id="status_manager_id_error">{{ $errors->first('status_manager_id') }}</small>
                            </div>
                        </div>
                        <div class="row mb-1">
                            <label for="description" class="col-form-label col-md-2">Description</label>
                            <div class="form-group col-md-8">
                                <textarea name="description" id="description" rows="3" class="form-control">{{ isset($taskData) ? $taskData->description : '' }}</textarea>
                            </div>
                        </div>
                        <div>
                            @if (!empty($taskData))
                                <button type="button" class="btn btn-primary float-end"
                                    data-url="{{ route('user-task.update', ['user_task' => $taskData->id]) }}"
                                    id="task_form_button">Update</button>
                            @else
                                <button type="button" class="btn btn-primary float-end" id="task_form_button"
                                    data-url="{{ route('user-task.store') }}">Submit</button>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        var csrf_token = '{{ csrf_token() }}';
        var index_path = "{{ route('user-task.index') }}";
        initAllCKEditors(
            [
                "description"
            ]
        );
    </script>
    <script src="{{ asset('admin/assets/js/custom/user_task.js') }}"></script>
@endsection
