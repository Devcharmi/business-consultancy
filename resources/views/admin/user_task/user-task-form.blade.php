@extends('admin.layouts.app')
@section('content')
    <!-- Page Header -->
    <div class="d-flex align-items-center justify-content-between page-header-breadcrumb flex-wrap gap-2">
        {{-- <div>
            <nav>
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Task</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Task</li>
                </ol>
            </nav>
            <h1 class="page-title fw-medium fs-18 mb-0">Task Manager</h1>
        </div> --}}
    </div>
    <!-- Page Header Close -->
    <div class="row">
        <div class="col-xl-12">
            <div class="card custom-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="card-title">
                        Task Manager
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
                            <label for="client_id" class="col-md-2 col-form-label">Related to</label>
                            <div class="form-group col-md-8">
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
                            </div>
                        </div>
                        @if (auth()->user()->hasRole(['Super Admin', 'Admin']))
                            <div class="row mb-1">
                                <label for="staff_manager_id" class="required col-form-label col-md-2">Assign to</label>
                                <div class="form-group col-md-8">
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
                            </div>
                        @else
                            <input type="hidden" name="staff_manager_id" id="staff_manager_id"
                                value="{{ isset($taskData) ? $taskData->staff_manager_id : auth()->id() }}">
                        @endif
                        <div class="row mb-1">
                            <label for="task_start_date" class="col-md-2 col-form-label">Start Date</label>
                            <div class="form-group col-md-3">
                                <input type="date" name="task_start_date" id="task_start_date" class="form-control"
                                    value="{{ isset($taskData->task_start_date) ? \Carbon\Carbon::parse($taskData->task_start_date)->format('Y-m-d') : date('Y-m-d') }}">
                                <span id="task_start_date_error"
                                    class="help-inline text-danger mt-2">{{ $errors->first('task_start_date') }}</span>
                            </div>
                        </div>
                        <div class="row mb-1">
                            <label for="task_end_date" class="col-md-2 col-form-label">End Date</label>
                            <div class="form-group col-md-3">
                                <input type="date" name="task_end_date" id="task_end_date" class="form-control"
                                    value="{{ isset($taskData->task_end_date) ? \Carbon\Carbon::parse($taskData->task_end_date)->format('Y-m-d') : '' }}">
                                <span id="task_end_date_error"
                                    class="help-inline text-danger mt-2">{{ $errors->first('task_end_date') }}</span>
                            </div>
                        </div>
                        <div class="row mb-1">
                            <label for="task_due_date" class="col-md-2 col-form-label">Due Date</label>
                            <div class="form-group col-md-3">
                                <input type="date" name="task_due_date" id="task_due_date" class="form-control"
                                    value="{{ isset($taskData->task_due_date) ? \Carbon\Carbon::parse($taskData->task_due_date)->format('Y-m-d') : date('Y-m-d') }}">
                                <span id="task_due_date_error"
                                    class="help-inline text-danger mt-2">{{ $errors->first('task_due_date') }}</span>
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
                        </div>
                        <div class="row mb-1">
                            <label class="col-md-2 col-form-label required">Status</label>
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
