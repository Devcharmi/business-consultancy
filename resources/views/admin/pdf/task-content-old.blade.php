<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    {{-- 🔥 PDF TITLE --}}
    <title>Task Report - {{ $task->title }}</title>

    <style>
        body {
            font-family: DejaVu Sans;
            font-size: 11.5px;
            color: #2f2f2f;
            line-height: 1.6;
        }

        .header {
            border: 1px solid #dbe3f3;
            background: #f4f7ff;
            padding: 14px;
            margin-bottom: 25px;
        }

        .header h1 {
            margin: 0;
            font-size: 20px;
            text-transform: uppercase;
        }

        .header-meta {
            margin-top: 6px;
            font-size: 11px;
            color: #555;
        }

        .section {
            margin-bottom: 28px;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            padding: 6px 10px;
            background: #eef3fb;
            border-left: 4px solid #2c7be5;
            margin-bottom: 12px;
        }

        .details-table {
            width: 100%;
            border-collapse: collapse;
        }

        .details-table td {
            padding: 6px 8px;
        }

        .details-table td:first-child {
            width: 22%;
            font-weight: bold;
        }

        .badge {
            display: inline-block;
            padding: 3px 8px;
            font-size: 10px;
            font-weight: bold;
            color: #fff;
            border-radius: 3px;
        }

        .date-card {
            border: 1px solid #d6e0ff;
            background: #f9fbff;
            padding: 12px;
            margin-bottom: 18px;
        }

        .date-title {
            font-size: 13px;
            font-weight: bold;
            color: #1f4fd8;
            border-bottom: 1px solid #dde6ff;
            margin-bottom: 10px;
            padding-bottom: 4px;
        }

        .sub-title {
            font-weight: bold;
            margin: 10px 0 6px;
            font-size: 12px;
        }

        .content-box {
            background: #ffffff;
            border-left: 4px solid #2c7be5;
            padding: 8px 10px;
            margin-bottom: 8px;
        }

        .item-row {
            border: 1px solid #e1e8ff;
            background: #ffffff;
            padding: 8px 10px;
            margin-bottom: 6px;
        }

        .item-header {
            display: table;
            width: 100%;
        }

        .item-title {
            display: table-cell;
            font-weight: bold;
        }

        .item-status {
            display: table-cell;
            text-align: right;
        }

        .item-meta {
            font-size: 10px;
            color: #666;
            margin-top: 3px;
        }
    </style>
</head>


<body>
    <div style="text-align:center; margin-bottom:14px;">
        <div
            style="
        font-size:18px;
        font-weight:bold;
        text-transform:uppercase;
        color:#2c7be5;
        letter-spacing:1px;
    ">
            Consulting Visit Report
        </div>
        {{-- <div style="font-size:11px; color:#777;">
            Detailed Task Summary
        </div> --}}
    </div>

    {{-- ================= HEADER ================= --}}
    <div class="header">
        <table width="100%" cellspacing="0" cellpadding="0">
            <tr>
                {{-- LEFT : TITLE + META --}}
                <td style="vertical-align: top;">
                    <h1>{{ $task->title }}</h1>

                    <div class="header-meta">
                        Client: {{ $task->client_objective->client->client_name ?? '-' }} |
                        Start: {{ $task->task_start_date?->format('d M Y') ?? '-' }} |
                        Due: {{ $task->task_due_date?->format('d M Y') ?? '-' }}
                    </div>

                    {{-- 🔥 STATUS NEW LINE --}}
                    @if ($task->status_manager)
                        <div style="margin-top:6px;">
                            <span class="badge" style="background: {{ $task->status_manager->color_name }}">
                                {{ $task->status_manager->name }}
                            </span>
                        </div>
                    @endif
                </td>

                {{-- RIGHT : LOGO --}}
                <td style="text-align:right; width:120px; vertical-align: top;">
                    <img src="{{ public_path('images/sample-logo.png') }}" style="max-width:100px; height:auto;">
                </td>
            </tr>
        </table>
    </div>

    {{-- <div class="header">
    <h1>{{ $task->title }}</h1>

    <div class="header-meta">
        Client: {{ $task->client_objective->client->client_name ?? '-' }} |
        Start: {{ $task->task_start_date?->format('d M Y') ?? '-' }} |
        Due: {{ $task->task_due_date?->format('d M Y') ?? '-' }}
    </div>
</div> --}}

    {{-- ================= TASK INFO ================= --}}
    <div class="section">
        <div class="section-title">Task Information</div>
        <table class="details-table">
            <tr>
                <td>Objective</td>
                <td>{{ $task->client_objective->objective_manager->name ?? '-' }}</td>
            </tr>
            <tr>
                <td>Expertise</td>
                <td>
                    @if ($task->expertise_manager)
                        <span class="badge" style="background: {{ $task->expertise_manager->color_name }}">
                            {{ $task->expertise_manager->name }}
                        </span>
                    @endif
                </td>
            </tr>
        </table>
    </div>

    {{-- ================= TIMELINE ================= --}}
    <div class="section">
        <div class="section-title">Task Timeline</div>

        @php
            $dates = collect()
                ->merge($task->content->pluck('content_date'))
                ->merge($task->commitments->pluck('commitment_date'))
                ->merge($task->deliverables->pluck('deliverable_date'))
                ->filter()
                ->map(fn($d) => \Carbon\Carbon::parse($d)->toDateString())
                ->unique()
                ->sortDesc();
        @endphp

        @foreach ($dates as $date)
            @php
                $contents = $task->content->filter(
                    fn($c) => \Carbon\Carbon::parse($c->content_date)->toDateString() === $date,
                );

                // {{-- SORT COMMITMENTS BY commitment_date DESC --}}
                $commitments = $task->commitments
                    ->filter(fn($c) => \Carbon\Carbon::parse($c->commitment_date)->toDateString() === $date)
                    ->sortByDesc('commitment_date');

                // {{-- SORT DELIVERABLES BY deliverable_date DESC --}}
                $deliverables = $task->deliverables
                    ->filter(fn($d) => \Carbon\Carbon::parse($d->deliverable_date)->toDateString() === $date)
                    ->sortByDesc('deliverable_date');
            @endphp

            <div class="date-card">
                <div class="date-title">
                    {{ \Carbon\Carbon::parse($date)->format('d M Y') }}
                </div>

                {{-- ================= CONTENT ================= --}}
                @if ($contents->count())
                    <div class="sub-title">CVR Notes</div>
                    @foreach ($contents as $content)
                        <div class="content-box">{!! $content->task_content !!}</div>
                    @endforeach
                @endif

                {{-- ================= COMMITMENTS ================= --}}
                @if ($commitments->count())
                    <div class="sub-title">Actionables</div>
                    @foreach ($commitments as $c)
                        <div class="item-row">
                            <div class="item-header">
                                <div class="item-title">{{ $c->commitment }}</div>
                                <div class="item-status">
                                    @if ($c->userTask?->status_manager)
                                        <span class="badge"
                                            style="background: {{ $c->userTask->status_manager->color_name }}">
                                            {{ $c->userTask->status_manager->name }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            {{-- 🔥 COMMITMENT DATE + DUE DATE --}}
                            <div class="item-meta">
                                Commitment Date:
                                {{ $c->commitment_date?->format('d M Y') ?? '-' }}
                                |
                                Due:
                                {{ $c->due_date?->format('d M Y') ?? '-' }}
                                @if ($c->staff)
                                    | Assigned to: {{ $c->staff->name }}
                                @endif
                            </div>
                        </div>
                    @endforeach
                @endif

                {{-- ================= DELIVERABLES ================= --}}
                @if ($deliverables->count())
                    <div class="sub-title">Deliverables</div>
                    @foreach ($deliverables as $d)
                        <div class="item-row">
                            <div class="item-header">
                                <div class="item-title">{{ $d->deliverable }}</div>
                                <div class="item-status">
                                    @if ($d->userTask?->status_manager)
                                        <span class="badge"
                                            style="background: {{ $d->userTask->status_manager->color_name }}">
                                            {{ $d->userTask->status_manager->name }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            {{-- 🔥 DELIVERABLE DATE + EXPECTED DATE --}}
                            <div class="item-meta">
                                Deliverable Date:
                                {{ $d->deliverable_date?->format('d M Y') ?? '-' }}
                                |
                                Expected:
                                {{ $d->expected_date?->format('d M Y') ?? '-' }}
                            </div>
                        </div>
                    @endforeach
                @endif

            </div>
        @endforeach
    </div>

    <div style="text-align:center;font-size:10px;color:#999">
        Generated on {{ now()->format('d M Y') }}
    </div>

</body>

</html>
