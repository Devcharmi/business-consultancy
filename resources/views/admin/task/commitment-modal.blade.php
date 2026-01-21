<!-- Commitment Modal -->
<div class="modal fade" id="commitmentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">

            {{-- Header --}}
            <div class="modal-header">
                <h5 class="modal-title">Add Commitment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            {{-- Form --}}
            <form id="commitment_form">
                @csrf

                <input type="hidden" id="commitment_date">
                <input type="hidden" name="task_id" id="commitment_task_id">

                <div class="modal-body">

                    <div class="mb-3">
                        <label class="required">Date</label>
                        <input type="date" name="due_date" id="commitment_date" class="form-control">
                        <small class="text-danger" id="commitment_date_error"></small>
                    </div>

                    <div class="mb-3">
                        <label class="required">Commitment</label>
                        <input type="text" name="commitment" id="commitment" class="form-control" placeholder="Enter commitment">
                        <small class="text-danger" id="commitment_error"></small>
                    </div>

                </div>

                {{-- Footer --}}
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        Save Commitment
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
