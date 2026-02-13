@extends('admin.layouts.app')

@section('content')
    <div class="row mt-4">
        <div class="col-xl-12">
            <div class="card custom-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="card-title">
                        {{ isset($taskData) ? 'Edit CVR' : 'Create CVR' }}
                    </div>
                    <a href="{{ route('consulting.index') }}" class="btn btn-primary mt-10 d-block text-center">
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

                                    {{-- =========================
                                        Readonly View: 2 per row
                                        ========================= --}}
                                    @if (isset($consultingData) && $consultingData)
                                        {{-- Row 1: Client & Objective --}}
                                        <input type="hidden" name="consulting_id" value="{{ $consultingData->id }}">
                                        <div class="col-md-6 mb-3">
                                            <label class="fw-bold">Client:</label>
                                            <span>{{ $consultingData->client_objective->client->client_name }}</span>
                                            <input type="hidden" name="client_objective_id"
                                                value="{{ $consultingData->client_objective_id }}">
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="fw-bold">Objective:</label>
                                            <span>{{ $consultingData->client_objective->objective_manager->name }}</span>
                                        </div>

                                        {{-- Row 2: CVR Date & Expertise --}}
                                        <div class="col-md-3 mb-3">
                                            <label class="fw-bold">Date:</label>
                                            <span>{{ \Carbon\Carbon::parse($consultingData->consulting_datetime)->format('d-m-Y h:i') }}</span>
                                            <input type="hidden" name="task_start_date"
                                                value="{{ \Carbon\Carbon::parse($consultingData->consulting_datetime)->format('Y-m-d') }}">

                                        </div>

                                        <div class="col-md-3 mb-3">
                                            <label class="fw-bold">Expertise:</label>
                                            <span
                                                class="badge bg-success">{{ $consultingData->expertise_manager->name ?? '-' }}</span>
                                            <input type="hidden" name="expertise_manager_id"
                                                value="{{ $consultingData->expertise_manager_id }}">
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="fw-bold">Focus Area:</label>
                                            <span>{{ $consultingData->focus_area_manager->name ?? '-' }}</span>
                                            <input type="hidden" name="focus_area_manager_id"
                                                value="{{ $consultingData->focus_area_manager_id }}">
                                        </div>
                                    @else
                                        {{-- Row 1: Client & Objective --}}
                                        <div class="col-md-6 mb-3">
                                            <label class="fw-bold">Client:</label>
                                            <span>{{ $taskData->client_objective->client->client_name }}</span>
                                            <input type="hidden" name="client_objective_id"
                                                value="{{ $taskData->client_objective_id }}">
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="fw-bold">Objective:</label>
                                            <span>{{ $taskData->client_objective->objective_manager->name }}</span>
                                        </div>

                                        {{-- Row 2: Meeting Date & Expertise --}}
                                        <div class="col-md-3 mb-3">
                                            <label class="fw-bold">Date:</label>
                                            <span>{{ \Carbon\Carbon::parse($taskData->task_start_date)->format('d-m-Y h:i') }}</span>
                                            <input type="hidden" name="task_start_date"
                                                value="{{ \Carbon\Carbon::parse($taskData->task_start_date)->format('Y-m-d') }}">

                                        </div>

                                        <div class="col-md-3 mb-3">
                                            <label class="fw-bold">Expertise:</label>
                                            <span
                                                class="badge bg-success">{{ $taskData->expertise_manager->name ?? '-' }}</span>
                                            <input type="hidden" name="expertise_manager_id"
                                                value="{{ $taskData->expertise_manager_id }}">
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="fw-bold">Focus Area:</label>
                                            <span>{{ $taskData->consulting->focus_area_manager->name ?? '-' }}</span>
                                            <input type="hidden" name="focus_area_manager_id"
                                                value="{{ optional(optional($taskData)->consulting)->focus_area_manager_id }}">

                                        </div>
                                    @endif
                                </div>
                                <hr>
                                {{-- =========================
                                    CVR Title
                                    ========================= --}}
                                <div class="row">

                                    <div class="col-md-5 mb-3">
                                        <label class="required">Title</label>
                                        <input type="text" name="title" id="title" class="form-control"
                                            value="{{ old('title', optional($taskData)->title) }}">
                                        <small class="text-danger">{{ $errors->first('title') }}</small>
                                    </div>

                                    {{-- Due Date --}}
                                    <div class="col-md-2 mb-3">
                                        <label class="required">Due Date</label>
                                        <input type="date" name="task_due_date" id="task_due_date" class="form-control"
                                            value="{{ old('task_due_date', optional($taskData)->task_due_date ? \Carbon\Carbon::parse($taskData->task_due_date)->format('Y-m-d') : \Carbon\Carbon::parse($consultingData->consulting_datetime)->format('Y-m-d')) }}">
                                        <small class="text-danger">{{ $errors->first('task_due_date') }}</small>
                                    </div>

                                    {{-- Status --}}
                                    <div class="col-md-3 mb-3">
                                        <label class="required">Status</label>
                                        <select name="status_manager_id" class="form-select">
                                            @foreach ($statuses as $status)
                                                <option value="{{ $status->id }}" @selected(old('status_manager_id', optional($taskData)->status_manager_id) == $status->id)>
                                                    {{ $status->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <small class="text-danger">{{ $errors->first('status_manager_id') }}</small>
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

                                <input type="file" id="attachmentInput" name="attachments[]" multiple
                                    accept="image/*" class="form-control mb-3">

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
                                    {{ !empty($taskData) ? 'Update CVR' : 'Create CVR' }}
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
        let canAssignStaff = @json(auth()->user()->hasRole(['Super Admin', 'Admin']));
        let commitments = {}; // new or edited items
        let commitmentsToDelete = [];
        let deliverables = {};
        let deliverablesToDelete = [];
        var index_path = "{{ route('consulting.index') }}";
        window.deleteAttachment = "{{ route('task.attachments.delete', ':id') }}";
        window.taskContentEditorIds = [
            @foreach ($dates as $date)
                "content_{{ \Illuminate\Support\Str::slug($date) }}",
            @endforeach
        ];
        initAllCKEditors(window.taskContentEditorIds);
    </script>

    <script src="{{ asset('admin/assets/js/custom/task.js') }}"></script>
    <script src="{{ asset('admin/assets/js/custom/task-commitment.js') }}"></script>
    <script src="{{ asset('admin/assets/js/custom/task-deliverable.js') }}"></script>
    <script src="{{ asset('admin/assets/js/custom/task-attachments.js') }}"></script>
@endsection
