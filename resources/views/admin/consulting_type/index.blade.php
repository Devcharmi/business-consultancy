@extends('admin.layouts.app')
@section('content')
    <div class="row mt-4">
        <div class="col-xl-12">
            <div class="card custom-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="card-title">
                        Consulting Type Manager
                    </div>
                    {{-- @if (canAccess('status manager.create')) --}}
                    <a href="#" data-url="{{ route('consulting-type.show', ['consulting_type' => 'new']) }}"
                        class="btn btn-success mt-10 d-block text-center open-modal {{ canAccess('consulting_type.create') ? '' : 'disabled' }}">+
                        Add Consulting Type</a>
                    {{-- @endif --}}
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="consulting_type_table" data-url="{{ route('consulting-type.index') }}"
                            class="table table-bordered text-nowrap w-100 table-list">
                            <thead>
                                <tr>
                                    <th>Sr No.</th>
                                    <th>Name</th>
                                    <th>Status</th>
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
        var edit_path = "{{ route('consulting-type.show', ['consulting_type' => ':consulting_type']) }}";
        var delete_path = "{{ route('consulting-type.destroy', ['consulting_type' => ':consulting_type']) }}";
        window.canEditTask = @json(canAccess('consulting_type.edit'));
        window.canDeleteTask = @json(canAccess('consulting_type.delete'));
    </script>
    <script src="{{ asset('admin/assets/js/custom/consulting_type.js') }}"></script>
@endsection
