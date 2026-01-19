@extends('admin.layouts.app')

@section('content')
    <!-- Page Header -->
    <div class="d-flex align-items-center justify-content-between page-header-breadcrumb flex-wrap gap-2">
        {{-- <div>
            <nav>
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Client</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Client</li>
                </ol>
            </nav>
            <h1 class="page-title fw-medium fs-18 mb-0">Client</h1>
        </div> --}}
    </div>
    <!-- Page Header Close -->

    <div class="row">
        <div class="col-xl-12">
            <div class="card custom-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Clients</h4>
                    <a href="{{ route('clients.show', 'new') }}"
                        class="btn btn-success {{ canAccess('client.create') ? '' : 'disabled' }}">+ Add Client</a>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-list" id="client_table" data-url="{{ route('clients.index') }}">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
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
@endsection

@section('script')
    <script>
        var csrf_token = '{{ csrf_token() }}';
        var index_path = "{{ route('clients.index') }}";
        var edit_path = "{{ route('clients.show', ':id') }}";
        var delete_path = "{{ route('clients.destroy', ':id') }}";
        window.canEditTask = @json(canAccess('client.edit'));
        window.canDeleteTask = @json(canAccess('client.delete'));
    </script>
    <script src="{{ asset('admin/assets/js/custom/client.js') }}"></script>
@endsection
