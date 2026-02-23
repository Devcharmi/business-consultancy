<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>CVR - {{ $task->title }}</title>
</head>

<body style="font-family: DejaVu Sans; font-size: 10.5px; color: #2f2f2f; line-height:1.35; margin:0; padding:0;">

    {{-- ================= TASK INFO ================= --}}
    {{-- <div style="margin-bottom:2px;"> --}}
    {{-- <div style="font-size:11px; font-weight:bold; padding:2px 4px; margin-bottom:3px;">Task Information</div> --}}

    <div style="background-color:#f8fbff; border:1px solid #d6e0ff; padding:3px 5px; margin-bottom:6px;">
        <div style="margin-bottom:1px;">
            <span style="display:inline-block; width:65px; font-weight:bold; color:#1f4fd8;">Objective</span>
            <span style="font-size:10.5px;">
                {{ $task->client_objective->objective_manager->name ?? '-' }}
            </span>
        </div>
        <div style="margin-bottom:1px;">
            <span style="display:inline-block; width:65px; font-weight:bold; color:#1f4fd8;">Expertise</span>
            <span style="font-size:10.5px;">
                @if ($task->expertise_manager)
                    <span
                        style="display:inline-block; padding:1px 4px; font-size:9px; font-weight:bold; color:#fff; border-radius:2px; background-color: {{ $task->expertise_manager->color_name }};">
                        {{ $task->expertise_manager->name }}
                    </span>
                @else
                    -
                @endif
            </span>
        </div>
        <div style="margin-bottom:1px;">
            <span style="display:inline-block; width:65px; font-weight:bold; color:#1f4fd8;">Status</span>
            <span style="font-size:10.5px;">
                @if ($task->status_manager)
                    <span
                        style="display:inline-block; padding:1px 4px; font-size:9px; font-weight:bold; color:#fff; border-radius:2px; background-color: {{ $task->status_manager->color_name }};">
                        {{ $task->status_manager->name }}
                    </span>
                @else
                    -
                @endif
            </span>
        </div>
    </div>
    {{-- </div> --}}

    @if ($task->participants)
        {{-- <div style="margin-bottom:2px;"> --}}
        <div style="font-size:11px; font-weight:bold;">Participants</div>

        <div style="background-color:#f8fbff; border:1px solid #d6e0ff;">
            <span style="font-size:10.5px;">
                {!! $task->participants !!}
            </span>
        </div>
        {{-- </div> --}}
    @endif

    {{-- ================= TIMELINE ================= --}}
    {{-- <div style="margin-bottom:2px;"> --}}
    <div style="font-size:11px; font-weight:bold;">Visit Schedule</div>

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
            $commitments = $task->commitments->filter(
                fn($c) => \Carbon\Carbon::parse($c->commitment_date)->toDateString() === $date,
            );
            $deliverables = $task->deliverables->filter(
                fn($d) => \Carbon\Carbon::parse($d->deliverable_date)->toDateString() === $date,
            );
        @endphp

        {{-- 🔥 SKIP EMPTY DATES --}}
        @if ($contents->isEmpty() && $commitments->isEmpty() && $deliverables->isEmpty())
            @continue
        @endif

        <div style="border:1px solid #d6e0ff; background-color:#f9fbff; margin-bottom:6px; padding:4px;">

            {{-- DATE TITLE --}}
            <div
                style="font-size:11px; font-weight:bold; color:#1f4fd8; border-left:3px solid #2c7be5; border-bottom:1px solid #e1e8ff; padding:5px 5px; margin-bottom:2px;">
                {{ \Carbon\Carbon::parse($date)->format('d M Y') }}
            </div>

            {{-- MEETING NOTES --}}
            @if ($contents->count())
                <p style="font-size:10.5px; font-weight:bold; color:#333; padding:3px 3px; margin:1px 0 2px 0;">
                    Details</p>
                <table cellpadding="3" cellspacing="0" border="0"
                    style="width:100%; border-collapse:collapse; margin:0 0 4px 0;">
                    @foreach ($contents as $content)
                        <tr>
                            <td
                                style="background-color:#ffffff; border:1px solid #e1e8ff; padding:3px 5px; vertical-align:middle; font-size:10.5px;">
                                {!! $content->task_content !!}
                            </td>
                        </tr>
                    @endforeach
                </table>
            @endif

            {{-- COMMITMENTS --}}
            @if ($commitments->count())
                <p style="font-size:10.5px; font-weight:bold; color:#333; padding:3px 3px; margin:3px 0 2px 0;">
                    Actionables</p>
                <table cellpadding="3" cellspacing="0" border="0"
                    style="width:100%; border-collapse:collapse; margin:0 0 4px 0;">
                    @php
                        // Common styles for the left and right cells
                        $tdLeftStyle =
                            'background-color:#ffffff; width:70%; padding:3px 5px; border-bottom:1px solid #e1e8ff; vertical-align:middle; font-size:10.5px;';
                        $tdRightStyle =
                            'background-color:#ffffff; width:30%; padding:3px 5px; border-bottom:1px solid #e1e8ff; vertical-align:middle; font-size:8.5px; color:#666; text-align:right; white-space:nowrap;';
                    @endphp

                    @foreach ($commitments as $c)
                        <tr>
                            <td style="{{ $tdLeftStyle }}">
                                {{ $c->commitment }}
                            </td>
                            <td style="{{ $tdRightStyle }}">
                                Due: {{ $c->due_date?->format('d M Y') ?? '-' }}
                                @if ($c->userTask?->status_manager)
                                    &nbsp;<span
                                        style="display:inline-block; padding:1px 4px; font-size:9px; font-weight:bold; color:#fff; border-radius:2px; background-color: {{ $c->userTask->status_manager->color_name }};">
                                        {{ $c->userTask->status_manager->name }}
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @endforeach

                </table>
            @endif

            {{-- DELIVERABLES --}}
            @if ($deliverables->count())
                <p style="font-size:10.5px; font-weight:bold; color:#333; padding:3px 3px; margin:3px 0 2px 0;">
                    Deliverables</p>
                <table cellpadding="3" cellspacing="0" border="0"
                    style="width:100%; border-collapse:collapse; margin:0 0 4px 0;">
                    @php
                        // Common styles for Deliverables table cells
                        $tdLeftStyleDeliverable =
                            'background-color:#ffffff; width:70%; padding:3px 5px; border-bottom:1px solid #e1e8ff; vertical-align:middle; font-size:10.5px;';
                        $tdRightStyleDeliverable =
                            'background-color:#ffffff; width:30%; padding:3px 5px; border-bottom:1px solid #e1e8ff; vertical-align:middle; font-size:8.5px; color:#666; text-align:right; white-space:nowrap;';
                    @endphp

                    @foreach ($deliverables as $d)
                        <tr>
                            <td style="{{ $tdLeftStyleDeliverable }}">
                                {{ $d->deliverable }}
                            </td>
                            <td style="{{ $tdRightStyleDeliverable }}">
                                Due: {{ $d->expected_date?->format('d M Y') ?? '-' }}
                                @if ($d->userTask?->status_manager)
                                    &nbsp;<span
                                        style="display:inline-block; padding:1px 4px; font-size:9px; font-weight:bold; color:#fff; border-radius:2px; background-color: {{ $d->userTask->status_manager->color_name }};">
                                        {{ $d->userTask->status_manager->name }}
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @endforeach

                </table>
            @endif

        </div>
    @endforeach
    {{-- </div> --}}

</body>

</html>
