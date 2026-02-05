@extends('admin.layouts.app')

@section('content')
    <!-- Page Header -->
    <div class="d-flex align-items-center justify-content-between page-header-breadcrumb flex-wrap gap-2">
        {{-- <div>
            <nav>
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Status</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Status</li>
                </ol>
            </nav>
            <h1 class="page-title fw-medium fs-18 mb-0">Status</h1>
        </div> --}}
    </div>
    <!-- Page Header Close -->
    <div class="row">
        <div class="col-xl-12">
            <div class="card custom-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="card-title">
                        Clients Report
                    </div>
                </div>
                <div class="card-body">

                    @include('admin.filters.common-filters')

                    <table class="table table-bordered table-striped w-100" id="clientReportTable"
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
                                <th>No. of Meetings</th>
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

    <script src="{{ asset('admin/assets/js/custom/reports/client_reports.js') }}"></script>
@endsection
