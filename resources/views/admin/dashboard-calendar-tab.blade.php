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
                     <div class="expertise-card h-100" style="--card-color: {{ $expertise->color_name ?? '#6c757d' }};">
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
                             $currentDay = Carbon\Carbon::createFromDate($selectedYear, $selectedMonth, $dayNumber);
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
                                     $initial = strtoupper(substr($expertise->name ?? 'N', 0, 1));
                                     $color = $expertise->color_name ?? '#6c757d';
                                     $date = \Carbon\Carbon::parse($consulting->consulting_datetime)->format('h:i A');
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
