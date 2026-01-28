@extends('admin.layouts.app')
@section('styles')
    <style>
        .calendar-container {
            border: 1px solid #dee2e6;
            border-radius: 0.5rem;
            overflow: hidden;
        }

        .calendar-header {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            background-color: #f8f9fa;
            text-align: center;
            font-weight: 600;
            border-bottom: 1px solid #dee2e6;
        }

        .calendar-day-header {
            padding: 0.5rem 0;
        }

        .calendar-body {
            display: grid;
            grid-template-rows: repeat(6, 1fr);
        }

        .calendar-week {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
        }

        .calendar-day {
            border-right: 1px solid #dee2e6;
            border-bottom: 1px solid #dee2e6;
            min-height: 100px;
            padding: 0.25rem;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            font-size: 0.85rem;
        }

        .calendar-day:last-child {
            border-right: none;
        }

        .calendar-day-other-month {
            background-color: #f8f9fa;
            color: #6c757d;
        }

        .calendar-day-today {
            border: 2px solid #0d6efd;
            position: relative;
        }

        .calendar-day-number {
            font-weight: 600;
            margin-bottom: 0.25rem;
            display: inline-block;
        }

        .calendar-add-btn {
            font-size: 0.9rem;
            line-height: 1;
            padding: 0;
            margin-left: 0.25rem;
            vertical-align: middle;
        }

        .calendar-day-content {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
            margin-top: 0.25rem;
            overflow-y: auto;
            max-height: 75px;
        }

        .calendar-event {
            border-radius: 0.25rem;
            color: #fff;
            padding: 0.2rem 0.35rem;
            display: flex;
            align-items: center;
            font-size: 0.75rem;
            word-break: break-word;
            cursor: pointer;
        }

        .calendar-event:hover {
            opacity: 0.9;
        }

        .calendar-event .calendar-event-text {
            margin-left: 0.25rem;
            line-height: 1.2;
        }

        .day-name {
            display: none;
        }

        @media (max-width: 767px) {

            .calendar-header {
                display: none;
            }

            .calendar-body {
                display: block;
            }

            .calendar-week {
                display: block;
            }

            .calendar-day {
                border: 1px solid #dee2e6;
                border-radius: 10px;
                margin-bottom: 12px;
                min-height: auto;
                padding: 10px;
                background-color: #fff;
            }

            .calendar-day-other-month {
                display: none;
            }

            .calendar-day-today {
                border: 2px solid #0d6efd;
                background-color: #f8fbff;
            }

            .calendar-day-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 6px;
                font-size: 0.9rem;
            }

            .day-name {
                display: inline;
                font-weight: 600;
                margin-right: 4px;
            }

            .day-date {
                font-weight: 700;
            }

            .calendar-day-number {
                font-size: 1rem;
                font-weight: 700;
            }

            .calendar-day-content {
                max-height: none;
                overflow: visible;
                gap: 6px;
            }

            .calendar-event {
                font-size: 0.8rem;
                padding: 6px 8px;
                border-radius: 6px;
            }
        }
    </style>
@endsection
@section('content')
    <div class="d-flex align-items-center justify-content-between page-header-breadcrumb flex-wrap gap-2">
        <div>
            <nav>
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item active"><a href="javascript:void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item" aria-current="page">Today's Tasks</li>
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
                            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#task-statistics"
                                type="button" role="tab">
                                Task Statistics
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#today-tasks-tab" type="button"
                                role="tab">
                                Today's Task and Followups
                            </button>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="task-statistics" role="tabpanel">

                            <div class="row mb-4">
                                <div class="col-xl-12">
                                    <div class="card custom-card">
                                        <div class="card-body p-0">
                                            {{-- <div
                                                class="d-flex justify-content-between align-items-center p-3 border-bottom">
                                                <h6 class="mb-0">Task Statistics</h6>
                                            </div> --}}

                                            <div class="p-3">
                                                <div class="row g-4 text-center">
                                                    @foreach ($expertises as $expertise)
                                                        @php
                                                            $stats = $expertiseTaskCounts[$expertise->id] ?? null;
                                                            $done = $stats->done_tasks ?? 0;
                                                            $total = $stats->total_tasks ?? 0;
                                                        @endphp

                                                        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                                                            <div class="expertise-card h-100"
                                                                style="--card-color: {{ $expertise->color_name ?? '#6c757d' }};">

                                                                {{-- Done / Total --}}
                                                                <div class="task-count">
                                                                    {{ $done }} / {{ $total }}
                                                                </div>

                                                                {{-- Expertise Name --}}
                                                                <div class="expertise-name">
                                                                    {{ $expertise->name }}
                                                                </div>
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
                                                                <span class="calendar-day-number">
                                                                    <span
                                                                        class="day-name">{{ $currentDay->format('D') . ',' }}</span>
                                                                    <span
                                                                        class="day-date">{{ $currentDay->format('d') }}</span>
                                                                </span>

                                                                @if ($isCurrentMonth && $canCreateConsulting)
                                                                    <button type="button"
                                                                        data-url="{{ route('consulting.show', ['consulting' => 'new']) }}"
                                                                        class="btn btn-sm btn-link p-0 calendar-add-btn"
                                                                        data-date="{{ $currentDay->format('Y-m-d') }}">
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
                        <div class="tab-pane fade" id="today-tasks-tab" role="tabpanel">
                            <p>Other content goes here...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
    <script src="{{ asset('admin/assets/js/custom/dashboard.js') }}"></script>
@endsection