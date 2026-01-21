<!-- Deliverable Modal -->
<div class="modal fade" id="deliverableModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">

            {{-- Header --}}
            <div class="modal-header">
                <h5 class="modal-title">Add Deliverable</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            {{-- Form --}}
            <form id="deliverable_form">
                @csrf

                <input type="hidden" id="deliverable_date">
                <input type="hidden" name="task_id" id="deliverable_task_id">

                <div class="modal-body">

                    <div class="mb-3">
                        <label class="required">Deliverable</label>
                        <input type="text" name="deliverable" class="form-control" placeholder="Enter deliverable">
                        <small class="text-danger" id="deliverable_error"></small>
                    </div>

                </div>

                {{-- Footer --}}
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-success">
                        Save Deliverable
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
