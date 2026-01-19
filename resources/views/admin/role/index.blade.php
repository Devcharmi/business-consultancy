@extends('admin.layouts.app')
@section('styles')
    <style>
        /* #rolePermissionModal .table th,
        #rolePermissionModal .table td {
            vertical-align: middle !important;
        }
        #rolePermissionModal .table td input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }
        #rolePermissionModal .fw-semibold {
            font-size: 15px;
        } */
    </style>
@endsection
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
    <!-- Page Header Close -->
    <div class="row">
        <div class="col-xl-12">
            <div class="card custom-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="card-title">
                        Role Manager
                    </div>
                    <a href="#" data-url="{{ route('role.show', ['role' => 'new']) }}"
                        class="btn btn-success mt-10 d-block text-center open-modal {{ canAccess('role.create') ? '' : 'disabled' }}">+ Add Role</a>
                </div>
                <div class="card-body">
                    {{-- <form action="{{ route('role.store') }}" method="post" id="role_form">
                        @csrf
                        <div class="row mb-3 align-items-center">
                            <div class="col-auto">
                                <label for="name" class="col-form-label required">Name</label>
                            </div>
                            <div class="col-auto">
                                <input type="text" name="name" id="name" class="form-control" required>
                            </div>
                            <div class="col-auto">
                                <button type="button" id="role_form_button" data-url="{{ route('role.store') }}"
                                    class="btn btn-primary">Save</button>
                            </div>
                        </div>
                    </form> --}}
                    <div class="table-responsive">
                        <table id="role_table" data-url="{{ route('role.index') }}"
                            class="table table-bordered text-nowrap w-100 table-list">
                            <thead>
                                <tr>
                                    <th>Sr No.</th>
                                    <th>Name</th>
                                    {{-- <th>Guard Name</th> --}}
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
@endsection
@section('script')
    <script>
        var csrf_token = '{{ csrf_token() }}';
        var permission_path = "{{ route('add-role-permisiion', ['role' => ':role']) }}";
        var edit_path = "{{ route('role.show', ['role' => ':role']) }}";
        var delete_path = "{{ route('role.destroy', ['role' => ':role']) }}";
        window.canEditTask = @json(canAccess('role.edit'));
        window.canDeleteTask = @json(canAccess('role.delete'));
    </script>
    <script src="{{ asset('admin/assets/js/custom/role.js') }}"></script>
@endsection
