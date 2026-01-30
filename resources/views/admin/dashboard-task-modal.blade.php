<div class="modal fade" id="taskModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header d-flex align-items-center">
                <h6 class="modal-title fw-semibold mb-0" id="taskModalTitle">
                    Meetings
                </h6>

                <div class="ms-auto d-flex align-items-center gap-2">
                    <a href="{{ route('task.show', 'new') }}" class="btn btn-success btn-sm open-task-modal"
                        id="addMeetingBtn">
                        <i class="fas fa-plus-circle me-1"></i> Add Meeting
                    </a>

                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
            </div>


            <div class="modal-body p-2">
                <div class="table-responsive">
                    <table id="task_modal_table" data-url="{{ route('task.index') }}"
                        class="table table-bordered table-sm text-nowrap w-100">
                        <thead>
                            <tr>
                                <th>Sr No.</th>
                                <th>Title</th>
                                <th>Expertise</th>
                                <th>Due Date</th>
                                <th>Status</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
