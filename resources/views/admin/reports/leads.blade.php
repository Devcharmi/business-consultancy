@extends('admin.layouts.app')

@section('content')
    <div class="row mt-4">
        <div class="col-xl-12">
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">Leads Report</div>
                </div>

                <div class="card-body">

                    @include('admin.filters.common-filters')

                    <div class="table-responsive">
                        <table class="table table-bordered text-nowrap w-100" id="leadReportTable"
                            data-url="{{ route('reports.leads') }}">
                            <thead>
                                <tr>
                                    <th>Client</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Status</th>
                                    <th>Created by</th>
                                    <th>Total Follow-ups</th>
                                    <th>Pending Follow-ups</th>
                                    <th>Next Follow-up</th>
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
    <script src="{{ asset('admin/assets/js/custom/reports/leads_reports.js') }}"></script>
@endsection
