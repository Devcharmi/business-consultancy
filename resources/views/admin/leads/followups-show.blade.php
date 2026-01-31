<div class="modal fade" id="followUpModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Lead Follow Ups</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                {{-- ADD FOLLOW UP FORM --}}
                <form id="followUpForm" data-url="{{ route('admin.leads.followups.store') }}">
                    @csrf

                    <input type="hidden" name="lead_id" id="followUpLeadId">

                    <div class="row g-2 align-items-end mb-3">

                        {{-- NEXT FOLLOW UP DATE --}}
                        <div class="col-md-3">
                            <label class="form-label">Next Follow Up Date</label>
                            <input type="datetime-local" name="next_follow_up_at" class="form-control">
                            <small class="text-danger"
                                id="next_follow_up_at_error">{{ $errors->first('next_follow_up_at') }}</small>
                        </div>

                        {{-- REMARK --}}
                        <div class="col-md-5">
                            <label class="form-label">Remark</label>
                            <input type="text" name="remark" class="form-control"
                                placeholder="Call summary / discussion note">
                            <small class="text-danger" id="remark_error">{{ $errors->first('remark') }}</small>
                        </div>

                        {{-- SUBMIT --}}
                        <div class="col-md-2">
                            <button class="btn btn-primary w-100">
                                Add Follow Up
                            </button>
                        </div>

                    </div>
                </form>

                <hr>

                {{-- FOLLOW UPS LIST --}}
                <div id="followUpList">
                    <div class="text-center py-4 text-muted">
                        <i class="fas fa-spinner fa-spin me-2"></i>
                        Loading follow ups...
                    </div>
                </div>

            </div>

            {{-- FOOTER --}}
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Close
                </button>
            </div>

        </div>
    </div>
</div>
