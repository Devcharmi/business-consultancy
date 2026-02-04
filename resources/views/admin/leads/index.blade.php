@extends('admin.layouts.app')

@section('title', 'Leads')

@section('content')
    <!-- Page Header -->
    <div class="d-flex align-items-center justify-content-between page-header-breadcrumb flex-wrap gap-2">
    </div>
    <div class="row">
        <div class="col-xl-12">
            <div class="card custom-card">

                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="card-title">
                        Leads Manager
                    </div>

                    <a href="{{ route('lead.show', ['lead' => 'new']) }}"
                        class="btn btn-success mt-10 d-block text-center {{ canAccess('leads.create') ? '' : 'disabled' }}">
                        + Add Lead
                    </a>
                </div>

                <div class="card-body">
                    <div class="table-responsive">

                        <table class="table table-bordered text-nowrap w-100" id="leads_table"
                            data-url="{{ route('lead.index') }}">
                            <thead>
                                <tr>
                                    <th class="text-center no-export">Action</th>
                                    <th>Date</th>
                                    <th>Name</th>
                                    <th>Phone</th>
                                    <th>Email</th>
                                    {{-- <th>Objective</th> --}}
                                    <th style="width: 140px">Status</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('admin.leads.followups-show')
@endsection

@section('script')
    <script>
        var csrf_token = "{{ csrf_token() }}";
        var updateLeadStatusUrl = "{{ route('admin.leads.update-status') }}";
        var updateFollowupStatus = "{{ route('admin.leads.followups.status', ':followUp') }}";
        var follow_up_list_url = "{{ route('admin.leads.followups.list', ':lead') }}";
        var edit_path = "{{ route('lead.show', ['lead' => ':id']) }}";
        var delete_path = "{{ route('lead.destroy', ['lead' => ':id']) }}";

        window.canEditTask = @json(canAccess('leads.edit'));
        window.canDeleteTask = @json(canAccess('leads.delete'));
    </script>

    <script src="{{ asset('admin/assets/js/custom/leads.js') }}"></script>
@endsection
