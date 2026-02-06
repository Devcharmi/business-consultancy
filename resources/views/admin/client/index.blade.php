@extends('admin.layouts.app')

@section('content')
  
    <div class="row mt-4">
        <div class="col-xl-12">
            <div class="card custom-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Clients</h4>
                    <a href="{{ route('clients.show', 'new') }}"
                        class="btn btn-success {{ canAccess('client.create') ? '' : 'disabled' }}">+ Add Client</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-list" id="client_table"
                            data-url="{{ route('clients.index') }}">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Status</th>
                                    <th class="text-center no-export">Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
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
