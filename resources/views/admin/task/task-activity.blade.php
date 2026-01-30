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


<div class="accordion" id="taskAccordion">

    @foreach ($dates as $date)
        @php
            $isToday = $date === now()->toDateString();
            $formattedDate = \Carbon\Carbon::parse($date)->format('d M Y, l');
        @endphp

        <div class="accordion-item mb-2 shadow-sm rounded">
            <h2 class="accordion-header" id="heading-{{ $date }}">
                <button class="accordion-button {{ $isToday ? '' : 'collapsed' }}" type="button"
                    data-bs-toggle="collapse" data-bs-target="#collapse-{{ $date }}"
                    style="
                        color: white;
                        {{ $isToday
                            ? 'background: linear-gradient(90deg, #1e90ff, #00bfff); font-weight: 700;'
                            : 'background: linear-gradient(90deg, #f0f0f0, #d9d9d9); color: #333;' }};
                        transition: all 0.3s;
                    ">
                    @if ($isToday)
                        <span class="me-2">🌟</span>
                    @else
                        <span class="me-2">📌</span>
                    @endif

                    {{ $formattedDate }}

                    @if ($isToday)
                        <span class="badge bg-light text-primary ms-3">Today</span>
                    @endif
                </button>
            </h2>

            <div id="collapse-{{ $date }}" class="accordion-collapse collapse {{ $isToday ? 'show' : '' }}">
                <div class="accordion-body">

                    {{-- ================= Content (1 per day) ================= --}}
                    <h6 class="mt-3 mb-2 text-success fw-bold">📝 Meeting Details</h6>
                    <textarea class="form-control mb-3" name="content[{{ $date }}]"
                        id="content_{{ \Illuminate\Support\Str::slug($date) }}">
                        {{ optional($contentByDate->get($date))->task_content }}
                    </textarea>

                    {{-- ================= Commitments ================= --}}
                    <h6 class="mt-4 mb-2 text-warning d-inline-block fw-bold">📌 Commitments</h6>
                    <button type="button" class="btn btn-sm btn-outline-primary ms-2 open-commitment-modal"
                        data-date="{{ $date }}">
                        + Add Commitment
                    </button>

                    <div class="table-responsive">
                        <table class="table table-bordered table-sm mt-2">
                            <thead class="table-light">
                                <tr>
                                    <th style="width:150px;">Created Date</th>
                                    <th style="width:150px;">Commitment Date</th>
                                    <th>Commitment</th>
                                    <th style="width:80px;">Action</th>
                                </tr>
                            </thead>
                            <tbody id="commitments_{{ $date }}">
                                @forelse ($commitmentsByDate->get($date, []) as $commitment)
                                    <tr data-id="{{ $commitment->id }}">
                                        {{-- <td>{{ $commitmnt->created_at->format('d M Y') }}</td> --}}
                                        <td>{{ \Carbon\Carbon::parse($commitment->commitment_date)->format('d M Y') }}
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($commitment->due_date)->format('d M Y') }}</td>
                                        <td>{{ $commitment->commitment }}

                                            <input type="hidden"
                                                name="commitments_existing[{{ $commitment->id }}][text]"
                                                value="{{ $commitment->commitment }}">

                                            <input type="hidden"
                                                name="commitments_existing[{{ $commitment->id }}][due_date]"
                                                value="{{ $commitment->due_date->toDateString() }}">
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-primary edit-commitment"
                                                data-id="{{ $commitment->id }}"
                                                data-text="{{ $commitment->commitment }}"
                                                data-due="{{ $commitment->due_date->toDateString() }}"
                                                data-date="{{ $date }}">✎</button>
                                            <button type="button" class="btn btn-sm btn-danger delete-commitment"
                                                data-id="{{ $commitment->id }}"
                                                data-date="{{ $date }}">✕</button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="no-commitments">
                                        <td colspan="4" class="text-muted text-center">No commitments for this date
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- ================= Deliverables ================= --}}
                    <h6 class="mt-4 mb-2 text-info d-inline-block fw-bold">🎯 Deliverables</h6>
                    <button type="button" class="btn btn-sm btn-outline-success ms-2 open-deliverable-modal"
                        data-date="{{ $date }}">
                        + Add Deliverable
                    </button>

                    <div class="table-responsive">
                        <table class="table table-bordered table-sm mt-2">
                            <thead class="table-light">
                                <tr>
                                    <th style="width:150px;">Created Date</th>
                                    <th style="width:150px;">Expected Date</th>
                                    <th>Deliverable</th>
                                    <th style="width:80px;">Action</th>
                                </tr>
                            </thead>
                            <tbody id="deliverables_{{ $date }}">
                                @forelse ($deliverablesByDate->get($date, []) as $deliverable)
                                    <tr data-id="{{ $deliverable->id }}">
                                        {{-- <td>{{ $deliverable->created_at->format('d M Y') }}</td> --}}
                                        <td>{{ \Carbon\Carbon::parse($deliverable->deliverable_date)->format('d M Y') }}
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($deliverable->expected_date)->format('d M Y') }}
                                        </td>
                                        <td>{{ $deliverable->deliverable }}

                                            <input type="hidden"
                                                name="deliverables_existing[{{ $deliverable->id }}][text]"
                                                value="{{ $deliverable->deliverable }}">

                                            <input type="hidden"
                                                name="deliverables_existing[{{ $deliverable->id }}][expected_date]"
                                                value="{{ $deliverable->expected_date->toDateString() }}">
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-primary edit-deliverable"
                                                data-id="{{ $deliverable->id }}"
                                                data-text="{{ $deliverable->deliverable }}"
                                                data-expected="{{ $deliverable->expected_date->toDateString() }}"
                                                data-date="{{ $date }}">✎</button>
                                            <button type="button" class="btn btn-sm btn-danger delete-deliverable"
                                                data-id="{{ $deliverable->id }}"
                                                data-date="{{ $date }}">✕</button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="no-deliverables">
                                        <td colspan="4" class="text-muted text-center">No deliverables for this date
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                </div>
            </div>
        </div>
    @endforeach

</div>
