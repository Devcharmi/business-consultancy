<div class="row g-3 mb-4">
    <div class="col-xl-4 col-lg-6">
        <div class="card shadow-sm border-0 h-100 dashboard-card followup-card">
            <div class="card-header bg-transparent border-0 d-flex align-items-center justify-content-between">
                <h6 class="mb-0 fw-semibold text-primary">
                    <i class="fas fa-calendar-check me-2"></i> Follow-ups
                </h6>
                <span class="badge bg-primary-subtle text-primary">
                    {{ $todayFollowUps->count() }}
                </span>
            </div>

            <div class="card-body p-2">
                @forelse ($todayFollowUps as $followup)
                    <div class="dashboard-item d-flex justify-content-between align-items-start">

                        {{-- LEFT : REMARK + TIME --}}
                        <div class="pe-2">
                            <a href="{{ route('lead.show', $followup->lead_id) }}"
                                class="text-decoration-none text-dark">

                                <div class="fw-semibold">
                                    {{ Str::limit($followup->remark, 45) }}
                                </div>

                                <small class="text-muted">
                                    {{ $followup->next_follow_up_at->format('d M Y h:i A') }}
                                </small>
                            </a>
                        </div>

                        {{-- RIGHT : STATUS + ACTION --}}
                        <div class="d-flex align-items-center gap-1">

                            {{-- STATUS BADGE --}}
                            <span
                                class="badge rounded-pill
                        bg-{{ $followup->status === 'completed' ? 'success' : 'warning' }}">
                                <i
                                    class="fas {{ $followup->status === 'completed' ? 'fa-check-circle' : 'fa-hourglass-half' }} me-1"></i>
                                {{ ucfirst($followup->status) }}
                            </span>

                            {{-- MARK COMPLETED --}}
                            @if ($followup->status === 'pending')
                                <button class="btn btn-xs btn-outline-success mark-completed" data-type="followup"
                                    data-id="{{ $followup->id }}">
                                    <i class="fas fa-check"></i>
                                </button>
                            @endif

                        </div>

                    </div>
                @empty
                    <div class="empty-state">
                        <i class="fas fa-check-circle"></i>
                        <p>No follow-ups today</p>
                    </div>
                @endforelse
            </div>

            <div class="card-footer bg-transparent border-0 text-end">
                <a href="{{ route('lead.index') }}" class="small fw-semibold">
                    View all →
                </a>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-lg-6">
        <div class="card shadow-sm border-0 h-100 dashboard-card task-card">
            <div class="card-header bg-transparent border-0 d-flex align-items-center justify-content-between">
                <h6 class="mb-0 fw-semibold text-success">
                    <i class="fas fa-tasks me-2"></i> Tasks
                </h6>
                <span class="badge bg-success-subtle text-success">
                    {{ $todayTasks->count() }}
                </span>
            </div>

            <div class="card-body p-2">
                @forelse ($todayTasks as $task)
                    <div class="dashboard-item d-flex justify-content-between align-items-start">

                        <div>
                            <a href="{{ route('user-task.show', $task->id) }}" class="text-decoration-none text-dark">

                                <div class="fw-semibold">
                                    {{ Str::limit($task->task_name, 40) }}
                                </div>

                                <small class="text-muted">
                                    Due: {{ \Carbon\Carbon::parse($task->task_due_date)->format('d M Y') }}
                                </small>
                            </a>
                        </div>

                        <div class="d-flex align-items-center gap-1">

                            {{-- STATUS BADGE --}}
                            <span class="badge rounded-pill bg-{{ $task->status_manager->bootstrapColor() }}">
                                <i class="fas {{ $task->status_manager->icon() }} me-1"></i>
                                {{ $task->status_manager->name }}
                            </span>

                            {{-- MARK COMPLETED --}}
                            @if (!$task->status_manager->isCompleted())
                                <button class="btn btn-xs btn-outline-success mark-completed" data-type="task"
                                    data-id="{{ $task->id }}" title="Mark Completed">
                                    <i class="fas fa-check"></i>
                                </button>
                            @endif

                        </div>
                    </div>
                @empty
                    <div class="empty-state">
                        <i class="fas fa-smile"></i>
                        <p>No tasks today</p>
                    </div>
                @endforelse
            </div>

            <div class="card-footer bg-transparent border-0 text-end">
                <a href="{{ route('user-task.index') }}" class="small fw-semibold">
                    View tasks →
                </a>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-lg-12">
        <div class="card shadow-sm border-0 h-100 dashboard-card overdue-card">
            <div class="card-header bg-transparent border-0 d-flex align-items-center justify-content-between">
                <h6 class="mb-0 fw-semibold text-warning">
                    <i class="fas fa-hourglass-half me-2"></i> Pending Tasks
                </h6>
                <span class="badge bg-warning-subtle text-warning">
                    {{ $pendingTasks->count() }}
                </span>
            </div>

            <div class="card-body p-2">
                @forelse ($pendingTasks as $task)
                    <div class="dashboard-item d-flex justify-content-between align-items-start">

                        {{-- LEFT --}}
                        <div>
                            <a href="{{ route('user-task.show', $task->id) }}" class="text-decoration-none">

                                <div class="fw-semibold text-{{ $task->status_manager->bootstrapColor() }}">
                                    {{ Str::limit($task->task_name, 40) }}
                                </div>

                                <small class="text-muted">
                                    Due: {{ \Carbon\Carbon::parse($task->task_due_date)->format('d M Y') }}
                                </small>
                            </a>

                        </div>

                        {{-- RIGHT --}}
                        <div class="d-flex align-items-center gap-1">

                            {{-- STATUS BADGE --}}
                            {{-- <span class="badge rounded-pill bg-{{ $task->status_manager->bootstrapColor() }}">
                                <i class="fas {{ $task->status_manager->icon() }} me-1"></i>
                                {{ $task->status_manager->name }}
                            </span> --}}

                            {{-- MARK DONE --}}
                            @if (!$task->status_manager->isCompleted())
                                <button class="btn btn-xs btn-outline-success mark-completed" data-type="task"
                                    data-id="{{ $task->id }}" title="Mark Completed">
                                    <i class="fas fa-check"></i>
                                </button>
                            @endif

                        </div>
                    </div>
                @empty
                    <div class="empty-state">
                        <i class="fas fa-hourglass-half text-warning"></i>
                        <p>No pending tasks 🎉</p>
                    </div>
                @endforelse
            </div>

            <div class="card-footer bg-transparent border-0 text-end">
                <a href="{{ route('user-task.index', ['filter' => 'pending']) }}"
                    class="small fw-semibold text-warning">
                    Resolve now →
                </a>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-lg-12">
        <div class="card shadow-sm border-0 h-100 dashboard-card overdue-card">
            <div class="card-header bg-transparent border-0 d-flex align-items-center justify-content-between">
                <h6 class="mb-0 fw-semibold text-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i> Overdue Tasks
                </h6>
                <span class="badge bg-danger-subtle text-danger">
                    {{ $overdueTasks->count() }}
                </span>
            </div>

            <div class="card-body p-2">
                @forelse ($overdueTasks as $task)
                    <div class="dashboard-item d-flex justify-content-between align-items-start danger">

                        {{-- LEFT --}}
                        <div>
                            <a href="{{ route('user-task.show', $task->id) }}"
                                class="text-decoration-none text-danger">

                                <div class="fw-semibold">
                                    {{ Str::limit($task->task_name, 40) }}
                                </div>

                                <small class="text-muted">
                                    Overdue since:
                                    {{ \Carbon\Carbon::parse($task->task_due_date)->format('d M Y') }}
                                </small>
                            </a>
                        </div>

                        {{-- RIGHT --}}
                        <div class="d-flex align-items-center gap-1">

                            {{-- STATUS BADGE --}}
                            {{-- <span class="badge rounded-pill bg-danger">
                                <i class="fas fa-exclamation-triangle me-1"></i>
                                Overdue
                            </span> --}}

                            {{-- MARK DONE --}}
                            @if (!$task->status_manager->isCompleted())
                                <button class="btn btn-xs btn-outline-success mark-completed" data-type="task"
                                    data-id="{{ $task->id }}" title="Mark Completed">
                                    <i class="fas fa-check"></i>
                                </button>
                            @endif

                        </div>
                    </div>
                @empty
                    <div class="empty-state">
                        <i class="fas fa-thumbs-up text-success"></i>
                        <p>No overdue tasks 🎉</p>
                    </div>
                @endforelse
            </div>

            <div class="card-footer bg-transparent border-0 text-end">
                <a href="{{ route('user-task.index', ['filter' => 'overdue']) }}"
                    class="small fw-semibold text-danger">
                    Resolve now →
                </a>
            </div>
        </div>
    </div>

</div>
