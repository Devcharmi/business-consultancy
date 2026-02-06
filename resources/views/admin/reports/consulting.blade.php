@extends('admin.layouts.app')

@section('content')
    <div class="row mt-4">
        <div class="col-xl-12">
            <div class="card custom-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="card-title">
                        Consulting Utilization Report
                    </div>
                </div>

                <div class="card-body">

                    @include('admin.filters.common-filters')

                    <table
                        class="table table-bordered table-striped w-100"
                        id="consultingReportTable"
                        data-url="{{ route('reports.consultings') }}"
                    >
                        <thead>
                            <tr>
                                <th>Client</th>
                                <th>Objective</th>
                                <th>Expertise</th>
                                <th>Total Consultings</th>
                                <th>Last Consulting Date</th>
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

    <script src="{{ asset('admin/assets/js/custom/reports/consulting_reports.js') }}"></script>
@endsection
