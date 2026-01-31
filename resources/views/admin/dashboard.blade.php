@extends('admin.layouts.app')

@section('content')
    <div class="d-flex align-items-center justify-content-between page-header-breadcrumb flex-wrap gap-2 mb-3">
        <div>
            <nav>
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item active"><a href="javascript:void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item">Today's Tasks</li>
                </ol>
            </nav>
            <h1 class="page-title fw-medium fs-18 mb-0">Dashboard</h1>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card custom-card">

                {{-- Tabs --}}
                <div class="card-header d-flex justify-content-between align-items-center">
                    <ul class="nav nav-tabs card-header-tabs">
                        <li class="nav-item">
                            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#task-statistics">
                                Task Statistics
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#today-tasks-tab">
                                Today's Task & Followups
                            </button>
                        </li>
                    </ul>
                </div>

                {{-- <div class="card-body"> --}}
                <div class="tab-content">

                    {{-- ================= TAB 1 ================= --}}
                    <div class="tab-pane fade show active" id="task-statistics">

                        {{-- Expertise Cards --}}
                        <div class="row mb-4">
                            <div class="col-12">
                                {{-- <div class="card custom-card">
                                        <div class="card-body"> --}}
                                <div class="row g-4 text-center">
                                    @foreach ($expertises as $expertise)
                                        @php
                                            $stats = $expertiseTaskCounts[$expertise->id] ?? null;
                                            $done = $stats->done_tasks ?? 0;
                                            $total = $stats->total_tasks ?? 0;
                                        @endphp

                                        <div class="col-6 col-sm-6 col-md-3 col-lg-3">
                                            <div class="expertise-card h-100"
                                                style="--card-color: {{ $expertise->color_name ?? '#6c757d' }};">
                                                <div class="task-count">{{ $done }} /
                                                    {{ $total }}</div>
                                                <div class="expertise-name">{{ $expertise->name }}</div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                {{-- </div>
                                    </div> --}}
                            </div>
                        </div>

                        {{-- Month Navigation --}}
                        <div class="row mb-3">
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">{{ $monthName }}</h5>
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

                        {{-- ================= CALENDAR ================= --}}
                        <div class="calendar-container">
                            {{-- <div class="calendar-scroll"> --}}
                            <div class="calendar-body">
                                @php
                                    $firstDay = Carbon\Carbon::createFromDate($selectedYear, $selectedMonth, 1);
                                    $daysInMonth = $firstDay->daysInMonth;
                                    $startDay = $firstDay->dayOfWeek;
                                    $prevMonth = $firstDay->copy()->subMonth();
                                    $prevMonthDays = $prevMonth->daysInMonth;
                                    $dayCount = 1;
                                    $nextMonthDay = 1;
                                @endphp

                                @for ($i = 0; $i < 6; $i++)
                                    <div class="calendar-week">
                                        @for ($j = 0; $j < 7; $j++)
                                            @php
                                                $isCurrentMonth = false;
                                                $isToday = false;

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
                                                    $dayNumber = $nextMonthDay++;
                                                    $nextMonth = $firstDay->copy()->addMonth();
                                                    $currentDay = Carbon\Carbon::createFromDate(
                                                        $nextMonth->year,
                                                        $nextMonth->month,
                                                        $dayNumber,
                                                    );
                                                }

                                                $dateKey = $currentDay->format('Y-m-d');
                                                $dateConsultings = $consultingsByDate[$dateKey] ?? [];
                                            @endphp

                                            <div class="calendar-day {{ !$isCurrentMonth ? 'calendar-day-other-month' : '' }} {{ $isToday ? 'calendar-day-today' : '' }}"
                                                data-date="{{ $currentDay->format('Y-m-d') }}">
                                                <div class="calendar-day-header">
                                                    <span>
                                                        <span class="day-name">{{ $currentDay->format('D') }},</span>
                                                        <span class="day-date">{{ $currentDay->format('d') }}</span>
                                                    </span>
                                                </div>

                                                <div class="calendar-day-content">
                                                    @foreach ($dateConsultings as $consulting)
                                                        @php
                                                            $expertise = $consulting->expertise_manager;
                                                            $initial = strtoupper(
                                                                substr($expertise->name ?? 'N', 0, 1),
                                                            );
                                                            $color = $expertise->color_name ?? '#6c757d';
                                                            $date = \Carbon\Carbon::parse(
                                                                $consulting->consulting_datetime,
                                                            )->format('h:i A');
                                                        @endphp
                                                        <div class="calendar-dot"
                                                            title="{{ $expertise->name }} | {{ $consulting->client_objective->client->client_name ?? 'N/A' }} | {{ $date }}"
                                                            style="--dot-color: {{ $color }}">
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endfor
                                    </div>
                                @endfor
                            </div>
                            {{-- </div> --}}
                        </div>
                    </div>

                    {{-- ================= TAB 2 ================= --}}
                    <div class="tab-pane fade" id="today-tasks-tab">
                        <p>Other content goes here…</p>
                    </div>

                </div>
                {{-- </div> --}}
            </div>
        </div>
    </div>
    @include('admin.dashboard-task-modal')
@endsection

@section('script')
    <script>
        var csrf_token = "{{ csrf_token() }}";
        var edit_path = "{{ route('task.show', ['task' => ':task']) }}";
        var delete_path = "{{ route('task.destroy', ['task' => ':task']) }}";
        var pdf_path = "{{ route('task.pdf', ['task' => ':task']) }}";
        var routeDayConsultings = "{{ route('dashboard.dayConsultings') }}";

        window.canEditTask = @json(canAccess('task.edit'));
        window.canDeleteTask = @json(canAccess('task.delete'));
    </script>
    <script src="{{ asset('admin/assets/js/custom/dashboard.js') }}"></script>
@endsection
