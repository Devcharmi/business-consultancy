<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Task PDF</title>

    <style>
        body {
            font-family: DejaVu Sans;
            font-size: 12px;
            color: #333;
            line-height: 1.5;
        }

        .page-title {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 20px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .section {
            margin-bottom: 25px;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            background: #eef3fb;
            padding: 8px 10px;
            border-left: 4px solid #2c7be5;
            margin-bottom: 15px;
        }

        /* DATE CARD */
        .date-card {
            border: 1px solid #cfe0ff;
            background: #f7faff;
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 4px;
        }

        .date-label {
            font-size: 14px;
            font-weight: bold;
            color: #1f4fd8;
            margin-bottom: 10px;
            border-bottom: 1px solid #dbe7ff;
            padding-bottom: 5px;
        }

        .sub-title {
            font-size: 12px;
            font-weight: bold;
            margin: 10px 0 6px;
            color: #444;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }

        th {
            background: #2c7be5;
            color: #fff;
            font-weight: bold;
            padding: 7px;
            font-size: 11px;
            text-align: left;
        }

        td {
            border: 1px solid #ddd;
            padding: 7px;
            font-size: 11px;
            vertical-align: top;
        }

        .details-table td {
            border: none;
            padding: 4px 6px;
            font-size: 12px;
        }

        .details-table td:first-child {
            width: 25%;
            font-weight: bold;
            color: #555;
        }

        .content-box {
            background: #ffffff;
            border-left: 4px solid #2c7be5;
            padding: 8px 10px;
            margin-bottom: 10px;
        }

        .empty-text {
            font-style: italic;
            color: #777;
            font-size: 11px;
        }

        .footer {
            text-align: center;
            font-size: 10px;
            color: #999;
            margin-top: 30px;
        }

        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
            color: #fff;
        }
    </style>

</head>

<body>

    <div class="page-title">Task Report</div>

    <!-- TASK DETAILS -->
    <div class="section">
        <div class="section-title">Task Details</div>
        <table class="details-table">
            <tr>
                <td>Title</td>
                <td>{{ $task->title }}</td>
            </tr>
            <tr>
                <td>Client</td>
                <td>{{ $task->client_objective->client->client_name ?? '-' }}</td>
            </tr>
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
                    @else
                        -
                    @endif
                </td>
            </tr>
            <tr>
                <td>Status</td>
                <td>
                    @if ($task->status_manager)
                        <span class="badge" style="background: {{ $task->status_manager->color_name }}">
                            {{ $task->status_manager->name }}
                        </span>
                    @else
                        -
                    @endif
                </td>
            </tr>
            <tr>
                <td>Due Date</td>
                <td>
                    {{ $task->task_due_date ? \Carbon\Carbon::parse($task->task_due_date)->format('d M Y') : '-' }}
                </td>
            </tr>
        </table>
    </div>

    <!-- DATE WISE TIMELINE -->
    <div class="section">
        <div class="section-title">Task Timeline</div>

        @php
            $dates = collect()
                ->merge($task->content->pluck('content_date'))
                ->merge($task->commitments->pluck('commitment_date'))
                ->merge($task->deliverables->pluck('deliverable_date'))
                ->unique()
                ->sortDesc();
        @endphp

        @forelse($dates as $date)

            <div class="date-card">
                <div class="date-label">
                    {{ \Carbon\Carbon::parse($date)->format('d M Y') }}
                </div>

                <!-- TASK CONTENT -->
                @php
                    $contents = $task->content->where('content_date', $date);
                @endphp

                @if ($contents->count())
                    <div class="sub-title">Meeting Details</div>
                    @foreach ($contents as $content)
                        <div class="content-box">
                            {!! $content->task_content !!}
                        </div>
                    @endforeach
                @endif

                <!-- COMMITMENTS -->
                @php
                    $commitments = $task->commitments->where('commitment_date', $date);
                @endphp

                @if ($commitments->count())
                    <div class="sub-title">Commitments</div>
                    <table>
                        <tr>
                            <th style="width:5%">#</th>
                            <th style="width:20%">Due Date</th>
                            <th>Commitment</th>
                        </tr>

                        @foreach ($commitments as $i => $commitment)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>
                                    {{ $commitment->due_date ? \Carbon\Carbon::parse($commitment->due_date)->format('d M Y') : '-' }}
                                </td>
                                <td>{{ $commitment->commitment }}</td>
                            </tr>
                        @endforeach
                    </table>
                @endif

                <!-- DELIVERABLES -->
                @php
                    $deliverables = $task->deliverables->where('deliverable_date', $date);
                @endphp

                @if ($deliverables->count())
                    <div class="sub-title">Deliverables</div>
                    <table>
                        <tr>
                            <th style="width:5%">#</th>
                            <th style="width:20%">Expected Date</th>
                            <th>Deliverable</th>
                        </tr>

                        @foreach ($deliverables as $j => $deliverable)
                            <tr>
                                <td>{{ $j + 1 }}</td>
                                <td>
                                    {{ $deliverable->expected_date ? \Carbon\Carbon::parse($deliverable->expected_date)->format('d M Y') : '-' }}
                                </td>
                                <td>{{ $deliverable->deliverable }}</td>
                            </tr>
                        @endforeach
                    </table>
                @endif
            </div>

        @empty
            <p class="empty-text">No task activity found.</p>
        @endforelse
    </div>


    <div class="footer">
        Generated on {{ now()->format('d M Y') }}
    </div>

</body>

</html>
