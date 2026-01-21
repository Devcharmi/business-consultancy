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
                    <h6 class="mt-4 mb-3">Commitments</h6>

                    @forelse ($commitmentsByDate->get($date, []) as $commitment)
                        <input type="text" class="form-control mb-2" value="{{ $commitment->commitment }}" readonly>
                    @empty
                        <p class="text-muted">No commitments for this date</p>
                    @endforelse

                    <div id="commitments_{{ $date }}"></div>
                    <button type="button" class="btn btn-sm btn-outline-primary open-commitment-modal"
                        data-date="{{ $date }}">
                        + Add Commitment
                    </button>

                    {{-- ================= Deliverables ================= --}}
                    <h6 class="mt-4 mb-3">Deliverables</h6>

                    @forelse ($deliverablesByDate->get($date, []) as $deliverable)
                        <input type="text" class="form-control mb-2" value="{{ $deliverable->deliverable }}"
                            readonly>
                    @empty
                        <p class="text-muted">No deliverables for this date</p>
                    @endforelse

                    <div id="deliverables_{{ $date }}"></div>
                    <button type="button" class="btn btn-sm btn-outline-success open-deliverable-modal"
                        data-date="{{ $date }}">
                        + Add Deliverable
                    </button>

                </div>

            </div>
        </div>
    @endforeach

</div>
