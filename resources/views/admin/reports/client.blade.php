@extends('admin.layouts.app')

@section('content')
    <div class="row mt-4">
        <div class="col-xl-12">
            <div class="card custom-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="card-title">
                        Clients Report
                    </div>
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

                    @include('admin.filters.common-filters-modal')
                    {{-- @include('admin.filters.common-filters') --}}

                    <div class="table-responsive">
                        <table class="table table-bordered text-nowrap w-100" id="clientReportTable"
                            data-url="{{ route('reports.clients') }}">
                            <thead>
                                <tr>
                                    <th>Client Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Status</th>
                                    <th>Created By</th>
                                    {{-- <th>Updated By</th> --}}
                                    <th>No. of Objectives</th>
                                    <th>No. of Consulting</th>
                                    <th>No. of CVR</th>
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
            // allowClear: true,
            // closeOnSelect: false, // keep dropdown open for multiple selections
        });
    </script>

    <script src="{{ asset('admin/assets/js/custom/reports/client_reports.js') }}"></script>
@endsection
