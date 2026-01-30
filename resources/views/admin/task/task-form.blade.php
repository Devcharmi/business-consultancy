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
                    <h4 class="card-title">Meeting Manager</h4>
                    <a href="{{ route('task.index') }}" class="btn btn-primary mt-10 d-block text-center">
                        Back
                    </a>
                </div>

                <div class="card-body">
                    <ul class="nav nav-tabs mb-4" id="taskTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="basic-tab" data-bs-toggle="tab" data-bs-target="#basic"
                                type="button" role="tab">
                                🧾 Basic Details
                            </button>
                        </li>

                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="attachment-tab" data-bs-toggle="tab" data-bs-target="#attachments"
                                type="button" role="tab">
                                📎 Attachments
                            </button>
                        </li>
                    </ul>

                    <form action="{{ !empty($taskData) ? route('task.update', $taskData->id) : route('task.store') }}"
                        method="POST" id="task_form" enctype="multipart/form-data">

                        @csrf
                        @if (!empty($taskData))
                            @method('PUT')
                        @endif
                        <div class="tab-content" id="taskTabsContent">
                            <div class="tab-pane fade show active" id="basic" role="tabpanel">
                                <div class="row">

                                    {{-- Client Objective --}}
                                    <div class="col-md-9 mb-3">
                                        <label class="required">Client Objective</label>

                                        <select name="client_objective_id" class="form-control select2">
                                            <option value="">Select Client Objective</option>

                                            @foreach ($clientObjectives as $co)
                                                @php
                                                    $selectedClientObjective = old(
                                                        'client_objective_id',
                                                        optional($taskData)->client_objective_id ??
                                                            request()->query('client_objective_id'),
                                                    );
                                                @endphp

                                                <option value="{{ $co->id }}"
                                                    {{ $selectedClientObjective == $co->id ? 'selected' : '' }}>
                                                    {{ $co->client->client_name }} - {{ $co->objective_manager->name }}
                                                </option>
                                            @endforeach
                                        </select>

                                        <small class="text-danger" id="client_objective_id_error">
                                            {{ $errors->first('client_objective_id') }}
                                        </small>
                                    </div>

                                    {{-- Expertise --}}
                                    <div class="col-md-3 mb-3">
                                        <label class="required">Expertise</label>

                                        <select name="expertise_manager_id" class="form-select">
                                            {{-- <option value="">Select Expertise</option> --}}

                                            @foreach ($expertises as $expertise)
                                                @php
                                                    $selectedExpertise = old(
                                                        'expertise_manager_id',
                                                        optional($taskData)->expertise_manager_id ??
                                                            request()->query('expertise_manager_id'),
                                                    );
                                                @endphp

                                                <option value="{{ $expertise->id }}"
                                                    {{ $selectedExpertise == $expertise->id ? 'selected' : '' }}>
                                                    {{ $expertise->name }}
                                                </option>
                                            @endforeach
                                        </select>

                                        <small class="text-danger" id="expertise_manager_id_error">
                                            {{ $errors->first('expertise_manager_id') }}
                                        </small>
                                    </div>

                                    {{-- Title --}}
                                    <div class="col-md-6 mb-3">
                                        <label class="required">Meeting Title</label>
                                        <input type="text" name="title" id="title" class="form-control"
                                            value="{{ old('title', optional($taskData)->title) }}">
                                        <small class="text-danger" id="title_error">{{ $errors->first('title') }}</small>
                                    </div>

                                    {{-- Due Date --}}
                                    <div class="col-md-3 mb-3">
                                        <label>Due Date</label>
                                        <input type="date" name="task_due_date" id="task_due_date" class="form-control"
                                            value="{{ old(
                                                'task_due_date',
                                                optional($taskData)->task_due_date
                                                    ? \Carbon\Carbon::parse($taskData->task_due_date)->format('Y-m-d')
                                                    : now()->format('Y-m-d'),
                                            ) }}">

                                        <small class="text-danger"
                                            id="task_due_date_error">{{ $errors->first('task_due_date') }}</small>

                                    </div>

                                    {{-- Expertise --}}
                                    <div class="col-md-3 mb-3">
                                        <label class="required">Status</label>
                                        <select name="status_manager_id" class="form-select">
                                            @foreach ($statuses as $status)
                                                <option value="{{ $status->id }}" @selected(old('status_manager_id', optional($taskData)->status_manager_id) == $status->id)>
                                                    {{ $status->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <small class="text-danger"
                                            id="status_manager_id_error">{{ $errors->first('status_manager_id') }}</small>
                                    </div>
                                </div>

                                {{-- =========================
                                TASK ACTIVITY (Accordion)
                                ========================= --}}
                                <div class="row mt-4">
                                    @include('admin.task.task-activity')
                                </div>
                            </div>

                            {{-- =====================
                                Attachments TAB
                                ===================== --}}
                            {{-- Attachments --}}
                            <div class="tab-pane fade" id="attachments" role="tabpanel">

                                <input type="file" id="attachmentInput" name="attachments[]" multiple accept="image/*"
                                    class="form-control mb-3">

                                {{-- Selected previews --}}
                                <div class="row" id="previewContainer"></div>

                                <hr>

                                {{-- Existing attachments --}}
                                <div class="row" id="existingAttachments">
                                    @foreach ($taskData->attachments ?? [] as $file)
                                        <div class="col-md-3 mb-3 attachment-item" id="attachment-{{ $file->id }}">
                                            <div class="card border">

                                                {{-- 🖼 Preview --}}
                                                @if (Str::startsWith($file->file_type, 'image'))
                                                    <img src="{{ asset('storage/' . $file->file_path) }}"
                                                        class="card-img-top" style="height:150px;object-fit:cover">
                                                @else
                                                    <div class="d-flex align-items-center justify-content-center"
                                                        style="height:150px;font-size:40px;">
                                                        📄
                                                    </div>
                                                @endif

                                                <div class="card-body p-2 text-center">

                                                    {{-- ✏️ Editable file name --}}
                                                    <input type="text" name="existing_file_names[{{ $file->id }}]"
                                                        class="form-control form-control-sm text-center mb-2"
                                                        value="{{ pathinfo($file->original_name, PATHINFO_FILENAME) }}"
                                                        placeholder="File name">

                                                    {{-- 🔽 Download --}}
                                                    <a href="{{ asset('storage/' . $file->file_path) }}"
                                                        download="{{ $file->original_name }}"
                                                        class="btn btn-sm btn-success">
                                                        ⬇
                                                    </a>

                                                    {{-- 🗑 Delete --}}
                                                    <button type="button"
                                                        class="btn btn-sm btn-danger btn-remove-attachment"
                                                        data-url="{{ route('task.attachments.delete', $file->id) }}">
                                                        ✕
                                                    </button>

                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Footer --}}
                            <div class="d-flex justify-content-end mt-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ !empty($taskData) ? 'Update Meeting' : 'Create Meeting' }}
                                </button>
                            </div>
                        </div>
                        <input type="hidden" name="commitments" id="commitments_input">
                        <input type="hidden" name="commitments_to_delete" id="commitments_delete_input">

                        <input type="hidden" name="deliverables" id="deliverables_input">
                        <input type="hidden" name="deliverables_to_delete" id="deliverables_delete_input">
                    </form>
                </div>
            </div>
        </div>
    </div>

    @include('admin.task.commitment-modal')
    @include('admin.task.deliverable-modal')
@endsection
@section('script')
    <script>
        const csrf_token = '{{ csrf_token() }}';
        let commitments = {}; // new or edited items
        let commitmentsToDelete = [];
        let deliverables = {};
        let deliverablesToDelete = [];
        var index_path = "{{ route('task.index') }}";
        window.deleteAttachment = "{{ route('task.attachments.delete', ':id') }}";
        window.taskContentEditorIds = [
            @foreach ($dates as $date)
                "content_{{ \Illuminate\Support\Str::slug($date) }}",
            @endforeach
        ];
        initAllCKEditors(window.taskContentEditorIds);
    </script>
    {{-- 
    @if (!empty($taskData))
        <script>
            // Initialize commitments per date
            commitments = {};
            @foreach ($commitmentsByDate as $date => $items)
                commitments['{{ $date }}'] = [];
                @foreach ($items as $c)
                    commitments['{{ $date }}'].push({
                        id: {{ $c->id }},
                        text: @json($c->commitment),
                        created_at: @json($c->created_at->format('Y-m-d')),
                        commitment_due_date: @json(optional($c->due_date)->format('Y-m-d') ?? $date),
                        status: {{ $c->status ?? 1 }}
                    });
                @endforeach
            @endforeach

            // Initialize deliverables per date
            deliverables = {};
            @foreach ($deliverablesByDate as $date => $items)
                deliverables['{{ $date }}'] = [];
                @foreach ($items as $d)
                    deliverables['{{ $date }}'].push({
                        id: {{ $d->id }},
                        _tmp_id: Date.now(), // 👈 unique temp key
                        text: @json($d->deliverable),
                        created_at: @json($d->created_at->format('Y-m-d')),
                        // status: {{ $d->status ?? 1 }}
                    });
                @endforeach
            @endforeach

            Object.keys(commitments).forEach(date => {
                renderCommitments(date);
            });

            Object.keys(deliverables).forEach(date => {
                renderDeliverables(date);
            });
        </script>
    @endif --}}

    <script src="{{ asset('admin/assets/js/custom/task.js') }}"></script>
    <script src="{{ asset('admin/assets/js/custom/task-commitment.js') }}"></script>
    <script src="{{ asset('admin/assets/js/custom/task-deliverable.js') }}"></script>
    <script src="{{ asset('admin/assets/js/custom/task-attachments.js') }}"></script>
@endsection
