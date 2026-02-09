@extends('admin.layouts.app')

@section('content')
    <div class="row mt-4">
        <div class="col-xl-12">
            <div class="card custom-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="card-title">
                        Objective Report
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
                        <table class="table table-bordered text-nowrap w-100" id="objectiveReportTable"
                            data-url="{{ route('reports.objectives') }}">
                            <thead>
                                <tr>
                                    <th>Client</th>
                                    <th>Objective</th>
                                    <th>No. of Consulting</th>
                                    <th>No. of Meetings</th>
                                    <th>Created By</th>
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
    </script>

    <script src="{{ asset('admin/assets/js/custom/reports/objective_reports.js') }}"></script>
@endsection
