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
            line-height: 1.4;
        }

        /* Page heading */
        .page-title {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 20px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Section */
        .section {
            margin-bottom: 25px;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            background: #f2f4f8;
            padding: 8px 10px;
            border-left: 4px solid #2c7be5;
            margin-bottom: 10px;
        }

        /* Tables */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        th {
            background: #2c7be5;
            color: #fff;
            font-weight: bold;
            text-align: left;
            padding: 8px;
            font-size: 11px;
        }

        td {
            border: 1px solid #ddd;
            padding: 8px;
            vertical-align: top;
            font-size: 11px;
        }

        tr:nth-child(even) td {
            background: #f9fafb;
        }

        /* Task details table */
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

        /* Date label */
        .date-label {
            font-weight: bold;
            margin: 10px 0 5px;
            color: #2c7be5;
        }

        /* Empty message */
        .empty-text {
            font-style: italic;
            color: #777;
            margin-top: 5px;
        }

        /* Footer */
        .footer {
            text-align: center;
            font-size: 10px;
            color: #999;
            margin-top: 30px;
        }

        .expertise-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
            color: #fff;
        }

        .status-badge {
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

    <!-- Task Details -->
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
                        <span class="expertise-badge"
                            style="background-color: {{ $task->expertise_manager->color_name ?? '#2c7be5' }}">
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
                        <span class="status-badge"
                            style="background-color: {{ $task->status_manager->color_name ?? '#2c7be5' }}">
                            {{ $task->status_manager->name }}
                        </span>
                    @else
                        -
                    @endif
                </td>
            </tr>
            <tr>
                <td>Due Date</td>
                <td>{{ \Carbon\Carbon::parse($task->task_due_date)->format('d M Y') ?? '-' }}</td>
            </tr>
        </table>
    </div>

    <!-- Commitments -->
    <div class="section">
        <div class="section-title">Commitments</div>

        @forelse($commitmentsByDate as $date => $commitments)
            <table>
                <tr>
                    <th style="width:5%">#</th>
                    <th style="width:15%">Date</th>
                    <th style="width:15%">Due Date</th>
                    <th>Commitment</th>
                </tr>

                @foreach ($commitments as $index => $commitment)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            {{ \Carbon\Carbon::parse($commitment->commitment_date)->format('d M Y') ?? '-' }}
                        </td>
                        <td>{{ $commitment->due_date ? \Carbon\Carbon::parse($commitment->due_date)->format('d M Y') : '-' }}
                        </td>
                        <td>{{ $commitment->commitment }}</td>
                    </tr>
                @endforeach
            </table>
        @empty
            <p class="empty-text">No commitments available.</p>
        @endforelse
    </div>

    <!-- Deliverables -->
    <div class="section">
        <div class="section-title">Deliverables</div>

        <table>
            <tr>
                <th style="width:5%">#</th>
                <th style="width:15%">Date</th>
                <th style="width:20%">Expected Date</th>
                <th>Deliverable</th>
            </tr>

            @forelse($task->deliverables as $key => $deliverable)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>
                        {{ \Carbon\Carbon::parse($deliverable->deliverable_date)->format('d M Y') ?? '-' }}
                    </td>
                    <td>{{ $deliverable->expected_date ? \Carbon\Carbon::parse($deliverable->expected_date)->format('d M Y') : '-' }}
                    </td>
                    <td>{{ $deliverable->deliverable }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="empty-text">No deliverables found.</td>
                </tr>
            @endforelse
        </table>
    </div>

    <div class="footer">
        Generated on {{ now()->format('d M Y') }}
    </div>

</body>

</html>
