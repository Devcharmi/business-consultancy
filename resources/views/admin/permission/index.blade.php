@extends('admin.layouts.app')
@section('content')
    <div class="row mt-4">
        <div class="col-xl-12">
            <div class="card custom-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="card-title">
                        Permission
                    </div>
                    <a href="#" data-url="{{ route('permissions.show', ['permission' => 'new']) }}"
                        class="btn btn-success open-modal {{ canAccess('permissions.create') ? '' : 'disabled' }}">+ Add Permission</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered text-nowrap w-100 table-list" id="permission_table"
                            data-url={{ route('permissions.index') }}>
                            <thead>
                                <tr>
                                    <th>Sr No.</th>
                                    <th>Name</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <!-- end row -->
@endsection
@section('script')
    <script>
        var csrf_token = '{{ csrf_token() }}';
        var edit_path = "{{ route('permissions.show', ['permission' => ':permission']) }}";
        var delete_path = "{{ route('permissions.destroy', ['permission' => ':permission']) }}";
        window.canEditTask = @json(canAccess('permissions.edit'));
        window.canDeleteTask = @json(canAccess('permissions.delete'));
    </script>
    <script src="{{ asset('admin/assets/js/custom/permission.js') }}"></script>
@endsection
