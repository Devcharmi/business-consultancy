{{-- LEAD STATUS WARNING --}}
@if (in_array($lead->status, ['converted', 'lost']))
    <div class="alert alert-warning">
        This lead is already {{ ucfirst($lead->status) }}.
    </div>

    <script>
        $('#followUpForm :input').prop('disabled', true);
    </script>
@endif

{{-- FOLLOW UPS --}}
@if ($followUps->isEmpty())
    <p class="text-center text-muted">No follow ups found.</p>
@else
    <div class="list-group">
        @foreach ($followUps as $f)
            <div class="list-group-item">

                <div class="d-flex justify-content-between align-items-center">
                    <strong>
                        {{ optional($f->next_follow_up_at)->format('d M Y, h:i A') }}
                    </strong>

                    <div class="d-flex align-items-center gap-2">
                        {{-- STATUS BADGE --}}
                        <span
                            class="badge bg-{{ $f->status === 'completed' ? 'success' : 'warning' }}">
                            {{ ucfirst($f->status) }}
                        </span>

                        {{-- MARK COMPLETED BUTTON --}}
                        @if ($f->status === 'pending' && !in_array($lead->status, ['converted', 'lost']))
                            <button
                                class="btn btn-sm btn-outline-success mark-followup-completed"
                                data-id="{{ $f->id }}" title="Mark Completed">
                                <i class="fas fa-check"></i>
                            </button>
                        @endif
                    </div>
                </div>

                <div class="mt-1 text-muted">
                    {{ $f->remark }}
                </div>

                @if ($f->completed_at)
                    <div class="text-success small mt-1">
                        Completed at:
                        {{ $f->completed_at->format('d M Y, h:i A') }}
                    </div>
                @endif

            </div>
        @endforeach
    </div>
@endif
