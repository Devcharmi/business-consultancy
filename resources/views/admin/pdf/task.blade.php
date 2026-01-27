<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Task PDF</title>
    <style>
        body {
            font-family: DejaVu Sans;
            font-size: 12px;
        }
        h2, h3 {
            margin-bottom: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        table, th, td {
            border: 1px solid #000;
        }
        th, td {
            padding: 6px;
        }
        .no-border td {
            border: none;
        }
    </style>
</head>
<body>

    <h2>Task Details</h2>
    <table class="no-border">
        <tr>
            <td><strong>Title:</strong></td>
            <td>{{ $task->title }}</td>
        </tr>
        <tr>
            <td><strong>Client:</strong></td>
            <td>{{ $task->client_objective->client->client_name ?? '-' }}</td>
        </tr>
        <tr>
            <td><strong>Objective:</strong></td>
            <td>{{ $task->client_objective->objective_manager->name ?? '-' }}</td>
        </tr>
        <tr>
            <td><strong>Expertise:</strong></td>
            <td>{{ $task->expertise_manager->name ?? '-' }}</td>
        </tr>
        <tr>
            <td><strong>Status:</strong></td>
            <td>{{ $task->status_manager->name ?? '-' }}</td>
        </tr>
        <tr>
            <td><strong>Start Date:</strong></td>
            <td>{{ $task->task_start_date }}</td>
        </tr>
        <tr>
            <td><strong>Due Date:</strong></td>
            <td>{{ $task->task_due_date }}</td>
        </tr>
    </table>

    <h3>Date-wise Commitments (DESC)</h3>

    @forelse($commitmentsByDate as $date => $commitments)
        <strong>Date: {{ \Carbon\Carbon::parse($date)->format('d M Y') }}</strong>
        <table>
            <tr>
                <th>#</th>
                <th>Commitment</th>
            </tr>
            @foreach($commitments as $index => $commitment)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $commitment->text }}</td>
                </tr>
            @endforeach
        </table>
    @empty
        <p>No commitments available.</p>
    @endforelse

    <h3>Deliverables (Latest First)</h3>
    <table>
        <tr>
            <th>#</th>
            <th>Deliverable</th>
            <th>Date</th>
        </tr>
        @forelse($task->deliverables as $key => $deliverable)
            <tr>
                <td>{{ $key + 1 }}</td>
                <td>{{ $deliverable->title ?? $deliverable->text }}</td>
                <td>{{ $deliverable->created_at?->format('d M Y') }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="3">No deliverables found.</td>
            </tr>
        @endforelse
    </table>

</body>
</html>
