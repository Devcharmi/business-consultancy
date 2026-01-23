<div class="objective-details">
    <div class="card-body">
        <h4 class="mb-4 fw-bold text-dark">
            {{ $clientObjective->client->client_name }} :
            <span class="text-primary">{{ strtoupper($clientObjective->objective_manager->name) }}</span>
        </h4>

        <div class="accordion" id="expertiseAccordion{{ $clientObjective->id }}">
            @foreach($expertiseManagers as $key => $expertise)
                @php

                    $hasTasks = $expertise->total_tasks > 0;
                @endphp

                <div class="accordion-item mb-3 border-0 shadow-sm">

                    <h2 class="accordion-header" id="heading{{ $clientObjective->id }}_{{ $expertise->id }}">
                        <button class="accordion-button collapsed py-3 px-4"
                                type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#collapse{{ $clientObjective->id }}_{{ $expertise->id }}"
                                style="
                                    background-color: {{ $expertise->color_name ?? '#4aa3df' }};
                                    color: #fff;
                                    border: none;
                                    font-weight: 600;
                                    font-size: 1.05rem;
                                ">
                            <div class="d-flex align-items-center w-100">
                                <i class="fas fa-caret-right me-3 accordion-icon"></i>
                                <span class="fw-bold">{{ $expertise->name }}</span>

                                <div class="ms-auto d-flex align-items-center">
                                    @if($hasTasks)
                                        <span class="badge bg-white text-dark px-3 py-2 me-3" style="border-radius: 20px;">
                                            <i class="fas fa-tasks me-1"></i>
                                            {{ $expertise->total_tasks }} {{ $expertise->total_tasks == 1 ? 'Task' : 'Tasks' }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </button>
                    </h2>

                    {{-- Accordion Body --}}
                    <div id="collapse{{ $clientObjective->id }}_{{ $expertise->id }}"
                         class="accordion-collapse collapse"
                         data-bs-parent="#expertiseAccordion{{ $clientObjective->id }}">

                        <div class="accordion-body p-0 bg-light">
                            @if($hasTasks)
                                {{-- Tasks Table --}}
                                <div class="p-4">
                                    <div class="table-container" style="border-radius: 8px; overflow: hidden;">
                                        <table class="table table-hover table-borderless mb-0">
                                            <thead style="background: #f8f9fa;">
                                                <tr>
                                                    <th width="50" class="py-3 ps-4">#</th>
                                                    <th class="py-3">Task</th>
                                                    <th class="py-3">Task Details / Output</th>
                                                    <th width="120" class="py-3">Due Date</th>
                                                    <th width="100" class="py-3">Status</th>
                                                    <th width="140" class="py-3 pe-4 text-center">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($expertise->tasks as $i => $task)
                                                    <tr class="bg-white border-bottom">
                                                        <td class="ps-4 fw-medium text-muted">{{ $i + 1 }}</td>
                                                        <td class="fw-medium">{{ $task->title ?? '—' }}</td>
                                                        <td>
                                                            <div class="task-details">
                                                                {{ $task->content->content ?? 'No details provided' }}
                                                            </div>
                                                        </td>
                                                        <td class="text-nowrap">
                                                            @if($task->task_due_date)
                                                                <span class="badge bg-light text-dark border px-3 py-1">
                                                                    <i class="far fa-calendar me-1"></i>
                                                                    {{ \Carbon\Carbon::parse($task->task_due_date)->format('d/m/Y') }}
                                                                </span>
                                                            @else
                                                                <span class="text-muted">—</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if($task->status_manager)
                                                                <span class="badge px-3 py-2"
                                                                      style="background-color: {{ $task->status_manager->color_name ?? '#6c757d' }}; color: #fff;">
                                                                    {{ $task->status_manager->name }}
                                                                </span>
                                                            @else
                                                                <span class="badge bg-secondary px-3 py-2">Pending</span>
                                                            @endif
                                                        </td>
                                                        <td class="text-center pe-4">
                                                            <div class="btn-group btn-group-sm" role="group">
                                                                <a href="javascript:void(0)"
                                                                    data-url="{{ route('task.show', $task->id) }}"
                                                                    class="btn btn-outline-primary border px-3 open-task-modal"
                                                                    title="Edit Task">
                                                                        <i class="fas fa-edit"></i>
                                                                    </a>
                                                               <a href="javascript:void(0)"
                                                                data-url="{{ route('task.destroy', $task->id) }}"
                                                                class="btn btn-outline-danger border px-3 delete-task-data"
                                                                title="Delete Task">
                                                                    <i class="fas fa-trash"></i>
                                                                </a>

                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>


                                <div class="p-4 pt-0">
                                    <div class="d-flex justify-content-end">
                                        <a href="javascript:void(0)"
                                           data-url="{{ route('task.show', 'new') }}?client_objective_id={{ $clientObjective->id }}&expertise_manager_id={{ $expertise->id }}"
                                           class="btn btn-success px-4 py-2 open-task-modal">
                                            <i class="fas fa-plus-circle me-2"></i> Add Task
                                        </a>
                                        <a href="javascript:void(0)"
                                           class="btn btn-outline-secondary px-4 py-2 ms-3">
                                            <i class="fas fa-plus-circle me-2"></i> Add Followup
                                        </a>
                                    </div>
                                </div>
                            @else

                                <div class="p-5 text-center">
                                    <div class="mb-4">
                                        <i class="fas fa-clipboard-list text-muted" style="font-size: 3rem;"></i>
                                    </div>
                                    <h5 class="text-muted mb-3">No tasks found for this expertise</h5>
                                    <p class="text-muted mb-4">Start by adding your first task to this expertise area</p>
                                    <a href="javascript:void(0)"
                                       data-url="{{ route('task.show', 'new') }}?client_objective_id={{ $clientObjective->id }}&expertise_manager_id={{ $expertise->id }}"
                                       class="btn btn-lg btn-success px-5 open-task-modal">
                                        <i class="fas fa-plus-circle me-2"></i> Add First Task
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>



<script>
document.addEventListener('DOMContentLoaded', function() {
    const accordionButtons = document.querySelectorAll('.accordion-button');

    accordionButtons.forEach(button => {
        button.addEventListener('click', function() {
            const icon = this.querySelector('.accordion-icon');
            const arrow = this.querySelector('.accordion-arrow');

            if (this.classList.contains('collapsed')) {
                icon.style.transform = 'rotate(90deg)';
                arrow.style.transform = 'rotate(180deg)';
            } else {
                icon.style.transform = 'rotate(0deg)';
                arrow.style.transform = 'rotate(0deg)';
            }
        });
    });
});
</script>
