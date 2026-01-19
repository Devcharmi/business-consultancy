{{-- LEAD STATUS WARNING --}}
@if (in_array($lead->status, ['converted', 'lost']))
    <div class="alert alert-warning">
        This lead is already {{ ucfirst($lead->status) }}.
    </div>

    {{-- DISABLE FORM VIA JS --}}
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
                <div class="d-flex justify-content-between">
                    <strong>
                        {{ optional($f->next_follow_up_at)->format('d M Y, h:i A') }}
                    </strong>

                    {{-- <span
                        class="badge bg-{{ $f->status === 'converted'
                            ? 'success'
                            : ($f->status === 'lost'
                                ? 'danger'
                                : ($f->status === 'contacted'
                                    ? 'info'
                                    : 'warning')) }}">
                        {{ ucfirst(str_replace('_', ' ', $f->status)) }}
                    </span> --}}
                </div>

                <div class="mt-1 text-muted">
                    {{ $f->remark }}
                </div>
            </div>
        @endforeach
    </div>
@endif
