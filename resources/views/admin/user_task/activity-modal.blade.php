<!-- Modal Body-->
<div class="modal fade" id="activityModal" tabindex="-1" status="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
    <div class="modal-dialog modal-xl" status="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    Task Activity - {{ $task->task_name }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                @if ($task->activities->count())

                    <div class="timeline">

                        @foreach ($task->activities as $activity)
                            <div class="timeline-item mb-4">

                                {{-- ICON --}}
                                <div class="timeline-icon">
                                    @switch($activity->activity_type)
                                        @case('created')
                                            <i class="ri-add-circle-fill text-success"></i>
                                        @break

                                        @case('status_changed')
                                            <i class="ri-refresh-line text-primary"></i>
                                        @break

                                        @case('delayed')
                                            <i class="ri-time-line text-danger"></i>
                                        @break

                                        @case('reassigned')
                                            <i class="ri-user-settings-line text-warning"></i>
                                        @break

                                        @case('deleted')
                                            <i class="ri-delete-bin-line text-danger"></i>
                                        @break

                                        @default
                                            <i class="ri-information-line text-secondary"></i>
                                    @endswitch
                                </div>

                                {{-- CONTENT --}}
                                <div class="timeline-content">

                                    <div class="d-flex justify-content-between">

                                        <strong>
                                            {{ $activity->user->name ?? 'System' }}
                                        </strong>

                                        <small class="text-muted">
                                            {{ $activity->created_at->format('d M Y') }}
                                        </small>

                                    </div>

                                    <div class="mt-1">
                                        {!! $activity->description !!}
                                    </div>

                                    {{-- META DETAILS --}}
                                    @if (!empty($activity->meta))
                                        <div class="mt-2 p-2 bg-light rounded small">

                                            @php
                                                $hiddenKeys = ['old_staff_id', 'new_staff_id'];
                                            @endphp

                                            @foreach ($activity->meta as $key => $value)
                                                @if (!in_array($key, $hiddenKeys) && !empty($value))
                                                    <div>
                                                        <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong>
                                                        {{ $value }}
                                                    </div>
                                                @endif
                                            @endforeach

                                        </div>
                                    @endif

                                </div>
                            </div>
                        @endforeach

                    </div>
                @else
                    <div class="text-center text-muted">
                        No activities found.
                    </div>
                @endif

            </div>
        </div>
    </div>
</div>
