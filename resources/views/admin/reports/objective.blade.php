@extends('admin.layouts.app')

@section('content')

    <div class="row mt-4">
        <div class="col-xl-12">
            <div class="card custom-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="card-title">
                        Objective Report
                    </div>
                </div>

                <div class="card-body">

                    {{-- 🔹 Common Filters --}}
                    @include('admin.filters.common-filters')

                    <table class="table table-bordered table-striped w-100" id="objectiveReportTable"
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
@endsection

@section('script')
    <script>
        const csrfToken = "{{ csrf_token() }}";
    </script>

    <script src="{{ asset('admin/assets/js/custom/reports/objective_reports.js') }}"></script>
@endsection
