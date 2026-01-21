@extends('admin.layouts.app')

@section('content')
    <!-- Page Header -->
    <div class="d-flex align-items-center justify-content-between page-header-breadcrumb flex-wrap gap-2">
        {{-- <div>
            <nav>
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Client</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Client</li>
                </ol>
            </nav>
            <h1 class="page-title fw-medium fs-18 mb-0">Client</h1>
        </div> --}}
    </div>
    <!-- Page Header Close -->
    <div class="row">
        <div class="col-xl-12">
            <div class="card custom-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Task Manager</h4>
                    <a href="{{ route('task.index') }}" class="btn btn-primary mt-10 d-block text-center">
                        Back
                    </a>
                </div>

                <div class="card-body">
                    <form action="{{ !empty($taskData) ? route('task.update', $taskData->id) : route('task.store') }}"
                        method="POST" id="task_form">
                        @csrf
                        @if (!empty($taskData))
                            @method('PUT')
                        @endif

                        <div class="row">

                            {{-- Client Objective --}}
                            <div class="col-md-8 mb-3">
                                <label class="required">Client Objective</label>
                                <select name="client_objective_id" class="form-control select2">
                                    <option value="">Select Client Objective</option>
                                    @foreach ($clientObjectives as $co)
                                        <option value="{{ $co->id }}" @selected(old('client_objective_id', optional($taskData)->client_objective_id) == $co->id)>
                                            {{ $co->client->client_name }} - {{ $co->objective_manager->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-danger" id="client_objective_id_error"></small>
                            </div>

                            {{-- Expertise --}}
                            <div class="col-md-4 mb-3">
                                <label class="required">Expertise</label>
                                <select name="expertise_manager_id" class="form-select">
                                    @foreach ($expertises as $expertise)
                                        <option value="{{ $expertise->id }}" @selected(old('expertise_manager_id', optional($taskData)->expertise_manager_id) == $expertise->id)>
                                            {{ $expertise->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-danger" id="expertise_manager_id_error"></small>
                            </div>

                            {{-- Title --}}
                            <div class="col-md-6 mb-3">
                                <label class="required">Task Title</label>
                                <input type="text" name="title" class="form-control"
                                    value="{{ old('title', optional($taskData)->title) }}">
                                <small class="text-danger" id="title_error"></small>
                            </div>

                            {{-- Due Date --}}
                            <div class="col-md-3 mb-3">
                                <label>Due Date</label>
                                <input type="date" name="task_due_date" class="form-control"
                                    value="{{ old(
                                        'task_due_date',
                                        optional($taskData)->task_due_date ? \Carbon\Carbon::parse($taskData->task_due_date)->format('Y-m-d') : '',
                                    ) }}">
                            </div>

                            {{-- Type --}}
                            <div class="col-md-3 mb-3">
                                <label class="required">Type</label>
                                <select name="type" class="form-select">
                                    <option value="">Select Type</option>
                                    <option value="meeting" @selected(old('type', optional($taskData)->type) === 'meeting')>
                                        Meeting
                                    </option>
                                    <option value="task" @selected(old('type', optional($taskData)->type) === 'task')>
                                        Task
                                    </option>
                                </select>
                                <small class="text-danger" id="type_error"></small>
                            </div>

                        </div>

                        {{-- =========================
                           TASK ACTIVITY (Accordion)
                        ========================= --}}
                        <div class="row mt-4">
                            @include('admin.task.task-activity')
                            @include('admin.task.commitment-modal')
                            @include('admin.task.deliverable-modal')
                        </div>

                        {{-- Footer --}}
                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-primary">
                                {{ !empty($taskData) ? 'Update Task' : 'Create Task' }}
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        const csrf_token = '{{ csrf_token() }}';
        window.taskContentEditorIds = [
            @foreach ($dates as $date)
                "content_{{ \Illuminate\Support\Str::slug($date) }}",
            @endforeach
        ];
        initAllCKEditors(window.taskContentEditorIds);
    </script>
    <script>
        let commitments = {}; // { date: [ {text, id?} ] }
        let deliverables = {}; // { date: [ {text, id?} ] }
    </script>
    @if (!empty($taskData))
        <script>
            commitments = @json(
                $commitmentsByDate->map(fn($items) => $items->map(fn($c) => [
                            'id' => $c->id,
                            'text' => $c->commitment,
                        ])));

            deliverables = @json(
                $deliverablesByDate->map(fn($items) => $items->map(fn($d) => [
                            'id' => $d->id,
                            'text' => $d->deliverable,
                        ])));
        </script>
    @endif

    <script src="{{ asset('admin/assets/js/custom/task.js') }}"></script>
@endsection
