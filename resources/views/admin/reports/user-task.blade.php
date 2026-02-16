@extends('admin.layouts.app')

@section('content')
    <div class="row mt-4">
        <div class="col-xl-12">
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">Tasks Report</div>
                    <div class="d-flex gap-2">

                        <!-- Open Filter Modal -->
                        <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#filterModal"
                            title="Filters">
                            <i class="ri-filter-3-line"></i>
                        </button>

                        <!-- Reset Filters -->
                        <button class="btn btn-outline-danger btn-sm" id="resetFilters" title="Reset">
                            <i class="ri-refresh-line"></i>
                        </button>
                    </div>
                </div>

                <div class="card-body">

                    {{-- Common Filters Modal --}}
                    @include('admin.filters.common-filters-modal')

                    <div class="table-responsive">
                        <table class="table table-bordered text-nowrap w-100" id="userTaskReportTable"
                            data-url="{{ route('reports.tasks') }}">

                            <thead>
                                <tr>
                                    <th>Task Name</th>
                                    <th>Entity</th>
                                    <th>Type</th>
                                    <th>Start Date</th>
                                    <th>Due Date</th>
                                    <th>Priority</th>
                                    <th>Status</th>
                                    <th>Assigned To</th>
                                    <th>Overdue</th>
                                </tr>
                            </thead>

                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        const csrfToken = "{{ csrf_token() }}";

        $(".select2").select2({
            placeholder: "Select...",
            width: "100%",
            dropdownParent: $("#filterModal"),
        });
    </script>

    <script src="{{ asset('admin/assets/js/custom/reports/user_task_reports.js') }}"></script>
@endsection
