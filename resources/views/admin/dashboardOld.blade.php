@extends('admin.layouts.app')
@section('content')
    <div class="d-flex align-items-center justify-content-between page-header-breadcrumb flex-wrap gap-2">
        <div>
            <nav>
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Today's Tasks</li>
                </ol>
            </nav>
            <h1 class="page-title fw-medium fs-18 mb-0">Dashboard</h1>
        </div>
    </div>


    <div class="row">
        <div class="col-xl-12">
            <div class="card custom-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <ul class="nav nav-tabs card-header-tabs" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#other-tab" type="button"
                                role="tab">
                                Task Statistics
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#today-tasks-tab"
                                type="button" role="tab">
                                Today's Task and Followups
                            </button>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="today-tasks-tab" role="tabpanel">

                            <div class="row mb-4">
                                <div class="col-xl-12">
                                    <div class="card custom-card">
                                        <div class="card-body p-0">
                                            <div
                                                class="d-flex justify-content-between align-items-center p-3 border-bottom">
                                                <h6 class="mb-0">Task Statistics</h6>
                                            </div>

                                            <div class="p-3">
                                                <div class="row g-3 text-center"> {{-- g-3 = proper gap --}}
                                                    @foreach ($expertises as $expertise)
                                                        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                                                            <div class="rounded d-flex align-items-center justify-content-center"
                                                                style="
                                                                background-color: {{ $expertise->color_name ?? '#6c757d' }};
                                                                min-height: 80px;">
                                                                <span class="fw-semibold text-light">
                                                                    {{ $expertise->name }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h5 class="mb-0">{{ $monthName }}</h5>
                                        </div>
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('dashboard', ['month' => $prevMonth->month, 'year' => $prevMonth->year]) }}"
                                                class="btn btn-sm btn-outline-secondary">
                                                <i class="bi bi-chevron-left"></i>
                                            </a>
                                            <a href="{{ route('dashboard', ['month' => $nextMonth->month, 'year' => $nextMonth->year]) }}"
                                                class="btn btn-sm btn-outline-secondary">
                                                <i class="bi bi-chevron-right"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Calendar -->
                            <div class="row">
                                <div class="col-12">
                                    <div class="calendar-container">
                                        <div class="calendar-header">
                                            <div class="calendar-day-header">Sun</div>
                                            <div class="calendar-day-header">Mon</div>
                                            <div class="calendar-day-header">Tue</div>
                                            <div class="calendar-day-header">Wed</div>
                                            <div class="calendar-day-header">Thu</div>
                                            <div class="calendar-day-header">Fri</div>
                                            <div class="calendar-day-header">Sat</div>
                                        </div>

                                        <div class="calendar-body">
                                            @php
                                                $firstDay = Carbon\Carbon::createFromDate(
                                                    $selectedYear,
                                                    $selectedMonth,
                                                    1,
                                                );
                                                $daysInMonth = $firstDay->daysInMonth;
                                                $startDay = $firstDay->dayOfWeek;

                                                $prevMonth = $firstDay->copy()->subMonth();
                                                $prevMonthDays = $prevMonth->daysInMonth;

                                                $dayCount = 1;
                                                $nextMonthDay = 1;
                                            @endphp

                                            @for ($i = 0; $i < 6; $i++)
                                                {{-- 6 weeks max --}}
                                                <div class="calendar-week">
                                                    @for ($j = 0; $j < 7; $j++)
                                                        @php
                                                            $dayNumber = null;
                                                            $isCurrentMonth = false;
                                                            $isToday = false;
                                                            $currentDay = null;

                                                            if ($i === 0 && $j < $startDay) {
                                                                $dayNumber = $prevMonthDays - ($startDay - $j - 1);
                                                                $currentDay = Carbon\Carbon::createFromDate(
                                                                    $prevMonth->year,
                                                                    $prevMonth->month,
                                                                    $dayNumber,
                                                                );
                                                            } elseif ($dayCount <= $daysInMonth) {
                                                                $dayNumber = $dayCount;
                                                                $isCurrentMonth = true;
                                                                $currentDay = Carbon\Carbon::createFromDate(
                                                                    $selectedYear,
                                                                    $selectedMonth,
                                                                    $dayNumber,
                                                                );
                                                                $isToday = $currentDay->isToday();
                                                                $dayCount++;
                                                            } else {
                                                                $dayNumber = $nextMonthDay;
                                                                $nextMonth = $firstDay->copy()->addMonth();
                                                                $currentDay = Carbon\Carbon::createFromDate(
                                                                    $nextMonth->year,
                                                                    $nextMonth->month,
                                                                    $dayNumber,
                                                                );
                                                                $nextMonthDay++;
                                                            }

                                                            $dateKey = $currentDay->format('Y-m-d');
                                                            $dateConsultings = $consultingsByDate[$dateKey] ?? [];
                                                        @endphp

                                                        <div
                                                            class="calendar-day {{ !$isCurrentMonth ? 'calendar-day-other-month' : '' }} {{ $isToday ? 'calendar-day-today' : '' }}">
                                                            <div class="calendar-day-header">
                                                                <span class="calendar-day-number">{{ $dayNumber }}</span>
                                                                @if ($isCurrentMonth && $canCreateConsulting)
                                                                    <button type="button"
                                                                        class="btn btn-sm btn-link p-0 calendar-add-btn"
                                                                        data-date="{{ $currentDay->format('Y-m-d') }}"
                                                                        title="Add Consulting">
                                                                        <i class="bi bi-plus-circle text-success"></i>
                                                                    </button>
                                                                @endif
                                                            </div>

                                                            <div class="calendar-day-content">
                                                                @foreach ($dateConsultings as $consulting)
                                                                    @php
                                                                        $expertise = $consulting->expertise_manager;
                                                                        $expertiseInitial = strtoupper(
                                                                            substr($expertise->name ?? 'N', 0, 1),
                                                                        );
                                                                        $expertiseColor =
                                                                            $expertise->color_name ?? '#6c757d';
                                                                    @endphp
                                                                    <div class="calendar-event mb-1">
                                                                        <div class="d-flex align-items-center gap-1"
                                                                            style="background-color: {{ $expertiseColor }}">
                                                                            <span class="calendar-event-badge"></span>
                                                                            <small class="calendar-event-text">
                                                                                ({{ $expertiseInitial }})
                                                                                {{ $consulting->client_objective->client->client_name ?? 'N/A' }}
                                                                                </br>
                                                                                {{ $consulting->consulting_datetime ? \Carbon\Carbon::parse($consulting->consulting_datetime)->format('h:i A') : '-' }}
                                                                            </small>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @endfor
                                                </div>
                                            @endfor
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Other Tab -->
                        <div class="tab-pane fade" id="other-tab" role="tabpanel">
                            <p>Other content goes here...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Placeholder -->
    <div id="modal-container"></div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            $(document).on('click', '.calendar-add-btn', function() {
                const date = $(this).data('date');
                const url = '{{ route('consulting.show', ['consulting' => 'new']) }}';

                $('#modal-container').html(
                    '<div class="text-center p-5"><i class="bi bi-hourglass-split fs-1"></i><p>Loading...</p></div>'
                    );

                $.ajax({
                    url: url,
                    method: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        $('#modal-container').html(response.html);

                        const datetimeInput = $('#consulting_datetime');
                        if (datetimeInput.length) {
                            datetimeInput.val(date + 'T09:00');
                        }

                        if ($.fn.select2) {
                            $('.select2').select2({
                                placeholder: "Select...",
                                width: "100%",
                                dropdownParent: $("#consultingForm"),
                                allowClear: true
                            });
                        }

                        $('#consultingForm').modal('show');
                    },
                    error: function() {
                        alert('Error loading form');
                    }
                });
            });

            $(document).on('submit', '#consulting_form', function(e) {
                e.preventDefault();

                let form = $('#consulting_form');
                let url = form.attr('action');
                let method = form.find('input[name="_method"]').length ? 'PUT' : 'POST';

                $("[id$='_error']").empty();

                let formData = new FormData(form[0]);

                formData.append('_token', '{{ csrf_token() }}');

                $.ajax({
                    url: url,
                    type: method,
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function(result) {
                        if (result.success) {
                            $('#consultingForm').modal('hide');

                            let message = result.message;
                            if (result.task_id) {
                                message += ' (Task ID: ' + result.task_id + ' created/updated)';
                            }
                            showToastr('success', message);

                            setTimeout(function() {
                                window.location.reload();
                            }, 1500);
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            $.each(errors, function(k, v) {
                                var id_arr = k.split(".");
                                $('#consulting_form').find('#' + id_arr[0] + '_error')
                                    .text(v);
                            });
                            showToastr('error', 'Please fix the validation errors');
                        } else {
                            showToastr('error', xhr.responseJSON.message ||
                                'Something went wrong!');
                        }
                    }
                });

                return false;
            });

            $(document).on('hidden.bs.modal', '#consultingForm', function() {
                $("[id$='_error']").empty();
            });
        });
    </script>
@endsection
