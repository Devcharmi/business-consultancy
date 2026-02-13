<div class="modal fade" id="dayConsultingModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">

            <div class="modal-header d-flex align-items-center">
                <h6 class="modal-title mb-0">
                    Consultings –
                    @if (!empty($date))
                        {{ \Carbon\Carbon::parse($date)->format('d M Y') }}
                    @endif
                    @if (!empty($clientName))
                        {{ $clientName }}
                    @endif

                </h6>

                <div class="ms-auto d-flex align-items-center gap-2">
                    <a href="#" data-url="{{ route('consulting.show', ['consulting' => 'new']) }}"
                        data-date="{{ !empty($date) ? \Carbon\Carbon::parse($date)->format('Y-m-d') : '' }}"
                        title="Add Consulting"
                        class="btn btn-success btn-sm calendar-add-btn {{ canAccess('consulting.create') ? '' : 'disabled' }}">
                        + Add Consulting
                    </a>

                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
            </div>

            <div class="modal-body p-2">
                @if ($consultings->isEmpty())
                    <div class="text-center text-muted py-4">
                        No consultings for this day
                    </div>
                @else
                    @foreach ($consultings as $consulting)
                        @php
                            $expertise = $consulting->expertise_manager;
                            $initial = strtoupper(substr($expertise->name ?? 'N', 0, 1));
                            $color = $expertise->color_name ?? '#6c757d';
                            $start = $consulting->start_time
                                ? \Carbon\Carbon::parse($consulting->start_time)->format('h:i A')
                                : null;

                            $end = $consulting->end_time
                                ? \Carbon\Carbon::parse($consulting->end_time)->format('h:i A')
                                : null;
                        @endphp

                        <div class="calendar-event d-flex justify-content-between align-items-center mb-2 px-2 py-1 rounded"
                            style="background: {{ $color }}15; border-left: 4px solid {{ $color }}">

                            <div class="small text-truncate">
                                <strong class="me-1">({{ $initial }})</strong>
                                {{-- {{ \Carbon\Carbon::parse($consulting->consulting_date)->format('d-m-Y') }} --}}
                                @if ($start && $end)
                                    {{ $start }}–{{ $end }}
                                @else
                                    -
                                @endif

                                —
                                {{ $consulting->client_objective->client->client_name ?? 'N/A' }}
                                —
                                {{ $consulting->client_objective->objective_manager->name ?? 'N/A' }}
                            </div>

                            <div class="d-flex align-items-center gap-1 flex-shrink-0">
                                <button class="btn btn-xs btn-outline-success open-meeting-modal"
                                    data-consulting-id="{{ $consulting->id }}"
                                    data-client-objective-id="{{ $consulting->client_objective_id }}"
                                    data-client-name="{{ $consulting->client_objective->client->client_name ?? '' }}"
                                    data-objective-name="{{ $consulting->client_objective->objective_manager->name ?? '' }}"
                                    title="Counsulting Visit Report">CVR
                                    {{-- <i class="bi bi-list-task"></i> --}}
                                </button>

                                <button class="btn btn-xs btn-outline-primary calendar-edit-btn"
                                    data-url="{{ route('consulting.show', $consulting->id) }}" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </button>

                                <button class="btn btn-xs btn-outline-danger calendar-delete-btn"
                                    data-url="{{ route('consulting.destroy', $consulting->id) }}" title="Delete">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    @endforeach

                @endif
            </div>

        </div>
    </div>
</div>
