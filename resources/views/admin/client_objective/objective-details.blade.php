<div class="objective-details">
    <div class="card-body">

        {{-- =====================
            HEADER (SMALLER + COLOR)
        ====================== --}}
        <div class="mb-3">
            <div class="text-muted small fw-semibold">
                {{ $clientObjective->client->client_name }} : {{ $clientObjective->objective_manager->name }}
            </div>

            {{-- <div class="fw-bold text-uppercase"
                 style="font-size: 0.95rem; color:#0d6efd;">
                {{ $clientObjective->objective_manager->name }}
            </div> --}}
        </div>

        {{-- =====================
            EXPERTISE TABS
        ====================== --}}
        <ul class="nav nav-tabs mb-4 expertise-tabs" id="expertiseTabs{{ $clientObjective->id }}" role="tablist">

            @if (!empty($expertiseManagers) && $expertiseManagers->count())
                @foreach ($expertiseManagers as $key => $expertise)
                    <li class="nav-item" role="presentation">
                        <button class="nav-link expertise-tab-btn {{ $expertise->is_active ? 'active' : '' }}"
                            id="tab-{{ $expertise->id }}" data-bs-toggle="tab"
                            data-bs-target="#expertise-{{ $expertise->id }}" type="button"
                            data-expertise-id="{{ $expertise->id }}" data-objective-id="{{ $clientObjective->id }}"
                            role="tab" style="--tab-color: {{ $expertise->color_name ?? '#0d6efd' }}">

                            {{ $expertise->name }}

                            <span class="badge ms-2">{{ $expertise->total_tasks }}</span>
                        </button>
                        {{-- <button
                        class="nav-link {{ $key === 0 ? 'active' : '' }}"
                        id="tab-{{ $expertise->id }}"
                        data-bs-toggle="tab"
                        data-bs-target="#expertise-{{ $expertise->id }}"
                        type="button"
                        role="tab"
                        style="--tab-color: {{ $expertise->color_name ?? '#0d6efd' }}">

                        {{ $expertise->name }}

                        <span class="badge ms-2">
                            {{ $expertise->total_tasks }}
                        </span>
                    </button> --}}
                    </li>
                @endforeach
            @else
                <div class="p-4 text-center text-muted">
                    No expertise found
                </div>
            @endif
        </ul>

        {{-- =====================
            TAB CONTENT
        ====================== --}}
        <div class="tab-content">

            @foreach ($expertiseManagers as $key => $expertise)
                <div class="tab-pane fade {{ $expertise->is_active ? 'show active' : '' }}"
                    id="expertise-{{ $expertise->id }}" role="tabpanel">


                    @if ($expertise->total_tasks > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered text-nowrap w-100 task-datatable">
                                <thead class="bg-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Meeting Title</th>
                                        {{-- <th>Task Details</th> --}}
                                        <th>Due Date</th>
                                        <th>Status</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($expertise->tasks as $i => $task)
                                        <tr class="bg-white border-bottom">
                                            <td class="text-muted">{{ $i + 1 }}</td>

                                            <td class="fw-medium">
                                                {{ $task->title ?? '—' }}
                                            </td>

                                            {{-- <td>
                                                {{ optional($task->content->first())->content ?? 'No details provided' }}
                                            </td> --}}

                                            <td>
                                                @if ($task->task_due_date)
                                                    <span class="badge bg-light border text-dark">
                                                        {{ \Carbon\Carbon::parse($task->task_due_date)->format('d/m/Y') }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>

                                            <td>
                                                @if ($task->status_manager)
                                                    <span class="badge"
                                                        style="background: {{ $task->status_manager->color_name }};
                                                                 color:#fff;">
                                                        {{ $task->status_manager->name }}
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary">Pending</span>
                                                @endif
                                            </td>

                                            <td class="text-center">
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('task.pdf', $task->id) }}" title="Download Pdf"
                                                        target="_blank" class="btn btn-outline-secondary">
                                                        <i class="fas fa-file-pdf"></i>
                                                    </a>
                                                    <a href="{{ route('task.show', $task->id) }}"
                                                        class="btn btn-outline-primary open-task-modal">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="javascript:void(0)"
                                                        data-url="{{ route('task.destroy', $task->id) }}"
                                                        class="btn btn-outline-danger delete-task-data">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-3">
                            <a href="{{ route('task.show', 'new') }}?client_objective_id={{ $clientObjective->id }}&expertise_manager_id={{ $expertise->id }}"
                                class="btn btn-success open-task-modal">
                                <i class="fas fa-plus-circle me-1"></i> Add Meeting
                            </a>
                        </div>
                    @else
                        <div class="p-5 text-center">
                            <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                            <h6 class="text-muted mb-3">No meetings found</h6>
                            <a href="{{ route('task.show', 'new') }}?client_objective_id={{ $clientObjective->id }}&expertise_manager_id={{ $expertise->id }}"
                                class="btn btn-success open-task-modal">
                                <i class="fas fa-plus-circle me-1"></i> Add First Meeting
                            </a>
                        </div>
                    @endif

                </div>
            @endforeach
        </div>
    </div>
</div>
@include('admin.client_objective.followup-modal')
