@extends('admin.layouts.app')
@section('content')
    <div class="row mt-4">
        <div class="col-xl-12">
            <div class="card custom-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="card-title">
                        Lead Type Manager
                    </div>
                    {{-- @if (canAccess('status manager.create')) --}}
                    <a href="#" data-url="{{ route('lead-type.show', ['lead_type' => 'new']) }}"
                        class="btn btn-success mt-10 d-block text-center open-modal {{ canAccess('lead-type.create') ? '' : 'disabled' }}">+
                        Add Lead Type</a>
                    {{-- @endif --}}
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="lead_table" data-url="{{ route('lead-type.index') }}"
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
        var edit_path = "{{ route('lead-type.show', ['lead_type' => ':lead_type']) }}";
        var delete_path = "{{ route('lead-type.destroy', ['lead_type' => ':lead_type']) }}";
        window.canEditTask = @json(canAccess('lead-type.edit'));
        window.canDeleteTask = @json(canAccess('lead-type.delete'));
    </script>
    <script src="{{ asset('admin/assets/js/custom/lead_type.js') }}"></script>
@endsection
