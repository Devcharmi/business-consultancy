@extends('admin.layouts.app')
@section('content')
    <!-- Page Header -->
    <div class="d-flex align-items-center justify-content-between page-header-breadcrumb flex-wrap gap-2">
        {{-- <div>
            <nav>
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Role</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Role</li>
                </ol>
            </nav>
            <h1 class="page-title fw-medium fs-18 mb-0">Role</h1>
        </div> --}}
    </div>

    <div class="row">
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
