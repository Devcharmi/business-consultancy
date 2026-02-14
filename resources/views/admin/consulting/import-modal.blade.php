<!-- Import Modal -->
<div class="modal fade" id="importConsultingModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Import Consulting Data</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <!-- Success Message -->
                <div id="importSuccess" class="alert alert-success d-none"></div>

                <!-- Import Form -->
                <form id="importConsultingForm" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Upload Excel File</label>
                        <input type="file" name="file" class="form-control" required>
                    </div>
                </form>

                <!-- Loader -->
                <div id="importLoader" class="text-center mt-3 d-none">
                    <div class="spinner-border text-primary"></div>
                    <p class="mt-2">Importing... Please wait</p>
                </div>

                <!-- Error Container -->
                <div id="importErrorContainer" class="mt-3 d-none">
                    <div class="alert alert-danger">
                        <strong>Import Errors:</strong>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead class="table-danger">
                                <tr>
                                    <th style="width:100px;">Row</th>
                                    <th>Error Message</th>
                                </tr>
                            </thead>
                            <tbody id="importErrorTableBody"></tbody>
                        </table>
                    </div>
                </div>

            </div>

            <div class="modal-footer d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Close
                </button>

                <button type="submit" form="importConsultingForm" class="btn btn-success" id="importSubmitBtn"
                    data-url="{{ route('consulting.import') }}">
                    Import
                </button>
            </div>

        </div>
    </div>
</div>
