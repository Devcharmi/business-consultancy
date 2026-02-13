<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Task PDF</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #000;
        }

        h3 {
            margin-bottom: 6px;
            border-bottom: 1px solid #000;
            padding-bottom: 4px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 6px;
            vertical-align: top;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: left;
        }

        .no-border td {
            border: none;
            padding: 4px 0;
        }


        .page-title {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 20px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }


        .badge {
            display: inline-block;
            padding: 4px 8px;
            font-size: 11px;
            font-weight: bold;
            color: #ffffff;
            border-radius: 4px;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            background: #e9eef5;
            padding: 6px 10px;
            border-left: 5px solid #2c7be5;
            margin: 20px 0 10px;
        }
    </style>
</head>

<body>
    <div class="page-title">Task Report</div>

    <div class="section-title">Task Details</div>

    <table class="no-border">
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
    <div class="section-title">Visit Schedule</div>

    <table>
        <thead>
            <tr>
                <th width="20%">Date</th>
                <th width="80%">Details</th>
            </tr>
        </thead>
        <tbody>
            @forelse($task->content as $content)
                <tr>
                    <td>
                        {{ \Carbon\Carbon::parse($content->content_date)->format('d-m-Y') }}
                    </td>
                    <td>
                        {!! $content->task_content !!}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="2">No content available</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div class="section-title">Actionables</div>

    <table>
        <thead>
            <tr>
                <th width="20%">Commitment Date</th>
                <th width="20%">Due Date</th>
                <th width="60%">Commitment</th>
            </tr>
        </thead>
        <tbody>
            @forelse($task->commitments as $commitment)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($commitment->commitment_date)->format('d-m-Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($commitment->due_date)->format('d-m-Y') }}</td>
                    <td>{{ $commitment->commitment }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3">No commitments</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div class="section-title">Deliverables</div>

    <table>
        <thead>
            <tr>
                <th width="20%">Deliverable Date</th>
                <th width="20%">Expected Date</th>
                <th width="60%">Deliverable</th>
            </tr>
        </thead>
        <tbody>
            @forelse($task->deliverables as $deliverable)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($deliverable->deliverable_date)->format('d-m-Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($deliverable->expected_date)->format('d-m-Y') }}</td>
                    <td>{{ $deliverable->deliverable }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3">No deliverables</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>

</html>
