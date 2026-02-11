{{-- ================= Meeting Timeline Heading + Export ================= --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="text-primary fw-bold mb-0">
        📅 Meeting Timeline
    </h3>

    @if (!empty($taskData))
        <a href="{{ route('task.pdf', ['task' => $taskData->id]) }}" target="_blank"
            class="btn btn-outline-secondary d-flex align-items-center gap-2 shadow-sm export-pdf-btn">
            <i class="fas fa-file-pdf"></i>
            Export PDF
        </a>
    @endif
</div>

@php
    // ✅ If consulting exists → use consulting_datetime
    // ✅ Else → use today
    $meetingDate =
        isset($consultingData) && $consultingData
            ? \Carbon\Carbon::parse($consultingData->consulting_datetime)->toDateString()
            : now()->toDateString();

    $safeId = \Illuminate\Support\Str::slug($meetingDate);
    $isToday = $meetingDate === now()->toDateString();
    $formattedDate = \Carbon\Carbon::parse($meetingDate)->format('d M Y, l');
@endphp

<div class="accordion" id="taskAccordion">

    <div class="accordion-item mb-2 shadow-sm rounded">
        <h2 class="accordion-header" id="heading-{{ $safeId }}">
            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                data-bs-target="#collapse-{{ $safeId }}"
                style="background: linear-gradient(90deg, #1e90ff, #00bfff); font-weight:700; color:white;">


                <span class="me-2">🌟</span>

                {{ $formattedDate }}

                @if ($isToday)
                    <span class="badge bg-light text-primary ms-3">Today</span>
                @endif
            </button>
        </h2>

        <div id="collapse-{{ $safeId }}" class="accordion-collapse collapse show">
            <div class="accordion-body">

                {{-- ================= Meeting Content ================= --}}
                <h6 class="mt-3 mb-2 text-success fw-bold">📝 Meeting Details</h6>
                <textarea class="form-control mb-3" name="content[{{ $meetingDate }}]" id="content_{{ $safeId }}">
                    {{ optional($contentByDate->get($meetingDate))->task_content }}
                </textarea>

                {{-- ================= Commitments ================= --}}
                <h6 class="mt-4 mb-2 text-warning fw-bold d-inline-block">📌 Commitments</h6>
                <button type="button" class="btn btn-sm btn-outline-primary ms-2 open-commitment-modal"
                    data-date="{{ $meetingDate }}">
                    + Add Commitment
                </button>

                <div class="table-responsive">
                    <table class="table table-bordered table-sm mt-2">
                        <thead class="table-light">
                            <tr>
                                <th style="width:150px;">Created Date</th>
                                <th style="width:150px;">Due Date</th>
                                <th>Commitment</th>
                                <th style="width:80px;">Action</th>
                            </tr>
                        </thead>
                        <tbody id="commitments_table">

                            @forelse ($commitmentsByDate->get($meetingDate, []) as $commitment)
                                <tr data-id="{{ $commitment->id }}">
                                    <td>{{ \Carbon\Carbon::parse($commitment->commitment_date)->format('d M Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($commitment->due_date)->format('d M Y') }}</td>
                                    <td>{{ $commitment->commitment }}</td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-primary edit-commitment"
                                            data-id="{{ $commitment->id }}" data-date="{{ $meetingDate }}">✎</button>

                                        <button type="button" class="btn btn-sm btn-danger delete-commitment"
                                            data-id="{{ $commitment->id }}" data-date="{{ $meetingDate }}">✕</button>
                                    </td>
                                </tr>
                            @empty
                                <tr class="no-commitments">
                                    <td colspan="4" class="text-muted text-center">
                                        No commitments for this date
                                    </td>
                                </tr>
                            @endforelse

                        </tbody>
                    </table>
                </div>

                {{-- ================= Deliverables ================= --}}
                <h6 class="mt-4 mb-2 text-info fw-bold d-inline-block">🎯 Deliverables</h6>
                <button type="button" class="btn btn-sm btn-outline-success ms-2 open-deliverable-modal"
                    data-date="{{ $meetingDate }}">
                    + Add Deliverable
                </button>

                <div class="table-responsive">
                    <table class="table table-bordered table-sm mt-2">
                        <thead class="table-light">
                            <tr>
                                <th style="width:150px;">Created Date</th>
                                <th style="width:150px;">Due Date</th>
                                <th>Deliverable</th>
                                <th style="width:80px;">Action</th>
                            </tr>
                        </thead>
                        <tbody id="deliverables_{{ $safeId }}">

                            @forelse ($deliverablesByDate->get($meetingDate, []) as $deliverable)
                                <tr data-id="{{ $deliverable->id }}">
                                    <td>{{ \Carbon\Carbon::parse($deliverable->deliverable_date)->format('d M Y') }}
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($deliverable->expected_date)->format('d M Y') }}</td>
                                    <td>{{ $deliverable->deliverable }}</td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-primary edit-deliverable"
                                            data-id="{{ $deliverable->id }}"
                                            data-date="{{ $meetingDate }}">✎</button>

                                        <button type="button" class="btn btn-sm btn-danger delete-deliverable"
                                            data-id="{{ $deliverable->id }}"
                                            data-date="{{ $meetingDate }}">✕</button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-muted text-center">
                                        No deliverables for this date
                                    </td>
                                </tr>
                            @endforelse

                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>

</div>
