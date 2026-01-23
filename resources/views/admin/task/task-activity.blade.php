<div class="accordion" id="taskAccordion">

    @foreach ($dates as $date)
        @php
            $isToday = $date === now()->toDateString();
        @endphp

        <div class="accordion-item mb-2">
            <h2 class="accordion-header" id="heading-{{ $date }}">
                <button class="accordion-button {{ $isToday ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse"
                    data-bs-target="#collapse-{{ $date }}">

                    {{ \Carbon\Carbon::parse($date)->format('d M Y') }}
                    @if ($isToday)
                        <span class="badge bg-primary ms-2">Today</span>
                    @endif
                </button>
            </h2>

            <div id="collapse-{{ $date }}" class="accordion-collapse collapse {{ $isToday ? 'show' : '' }}">
                <div class="accordion-body">

                    {{-- ================= Content (1 per day) ================= --}}
                    <h6 class="mt-4 mb-3">Content</h6>
                    <textarea class="form-control mb-3" name="content[{{ $date }}]"
                        id="content_{{ \Illuminate\Support\Str::slug($date) }}">
                        {{ optional($contentByDate->get($date))->task_content }}
                    </textarea>

                    {{-- ================= Commitments ================= --}}
                    {{-- <div class="d-flex align-items-center justify-content-between mt-4 mb-3"> --}}
                    <h6 class="mt-4 mb-3 d-inline-block">
                        Commitments
                    </h6>

                    <button type="button" class="btn btn-sm btn-outline-primary ms-2 open-commitment-modal"
                        data-date="{{ $date }}">
                        + Add Commitment
                    </button>

                    {{-- </div> --}}

                    <table class="table table-bordered table-sm">
                        <thead>
                            <tr>
                                <th style="width:150px;">Created Date</th>
                                <th style="width:150px;">Commitment Date</th>
                                <th>Commitment</th>
                                <th style="width:80px;">Action</th>
                            </tr>
                        </thead>
                        <tbody id="commitments_{{ $date }}">
                            @forelse ($commitmentsByDate->get($date, []) as $commitment)
                                <tr>
                                    <td>{{ $commitment->created_at->format('d M Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($commitment->due_date)->format('d M Y') }}</td>

                                    <td>{{ $commitment->commitment }}</td>


                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-primary edit-commitment">✎</button>
                                        <button type="button"
                                            class="btn btn-sm btn-danger remove-commitment">✕</button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-muted text-center">
                                        No commitments for this date
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>

                    </table>

                    {{-- ================= Deliverables ================= --}}
                    <h6 class="mt-4 mb-3 d-inline-block">
                        Deliverables
                    </h6>

                    <button type="button" class="btn btn-sm btn-outline-success ms-2 open-deliverable-modal"
                        data-date="{{ $date }}">
                        + Add Deliverable
                    </button>
                    <table class="table table-bordered table-sm">
                        <thead>
                            <tr>
                                <th style="width:150px;">Created Date</th>
                                <th style="width:150px;">Expected Date</th>
                                <th>Deliverable</th>
                                <th style="width:80px;">Action</th>
                            </tr>
                        </thead>

                        <tbody id="deliverables_{{ $date }}">
                            @forelse ($deliverablesByDate->get($date, []) as $deliverable)
                                <tr>
                                    <td>{{ $commitment->created_at->format('d M Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($deliverable->expected_date)->format('d M Y') }}</td>
                                    <td>{{ $deliverable->deliverable }}</td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-danger">✕</button>
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
    @endforeach

</div>
