@extends('admin.layouts.app')
@section('styles')
    <style>
        /* #userPermissionModal .table th,
            #userPermissionModal .table td {
                vertical-align: middle !important;
            }
            #userPermissionModal .table td input[type="checkbox"] {
                width: 18px;
                height: 18px;
                cursor: pointer;
            }
            #userPermissionModal .fw-semibold {
                font-size: 15px;
            } */
    </style>
@endsection
@section('content')
    <div class="row mt-4">
        <div class="col-xl-12">
            <div class="card custom-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="card-title">
                        User Manager
                    </div>
                    <a href="{{ route('user-manager.show', ['user_manager' => 'new']) }}"
                        class="btn btn-success mt-10 d-block text-center {{ canAccess('user.create') ? '' : 'disabled' }}">+
                        Add User</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="user_table" data-url="{{ route('user-manager.index') }}"
                            class="table table-bordered text-nowrap w-100 table-list">
                            <thead>
                                <tr>
                                    <th>Sr No.</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Role</th>
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
        var edit_path = "{{ route('user-manager.show', ['user_manager' => ':user_manager']) }}";
        var delete_path = "{{ route('user-manager.destroy', ['user_manager' => ':user_manager']) }}";
        var user_permission_modal_path = "{{ route('user.permission.modal', ['user' => ':user']) }}";
        window.canEditTask = @json(canAccess('user.edit'));
        window.canDeleteTask = @json(canAccess('user.delete'));
    </script>
    <script src="{{ asset('admin/assets/js/custom/user_manager.js') }}"></script>
    <script src="{{ asset('admin/assets/js/custom/user_permission.js') }}"></script>
@endsection
