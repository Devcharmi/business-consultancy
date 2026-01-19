@extends('admin.layouts.app')
@section('content')
    <!-- Page Header -->
    <div class="d-flex align-items-center justify-content-between page-header-breadcrumb flex-wrap gap-2">
        {{-- <div>
            <nav>
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Status</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Status</li>
                </ol>
            </nav>
            <h1 class="page-title fw-medium fs-18 mb-0">Status</h1>
        </div> --}}
    </div>
    <!-- Page Header Close -->
    <div class="row">
        <div class="col-xl-12">
            <div class="card custom-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="card-title">
                        Expertise Manager
                    </div>
                    {{-- @if (canAccess('status manager.create')) --}}
                    <a href="#" data-url="{{ route('expertise-manager.show', ['expertise_manager' => 'new']) }}"
                        class="btn btn-success mt-10 d-block text-center open-modal {{ canAccess('expertise.create') ? '' : 'disabled' }}">+
                        Add Status</a>
                    {{-- @endif --}}
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="expertise_table" data-url="{{ route('expertise-manager.index') }}"
                            class="table table-bordered text-nowrap w-100 table-list">
                            <thead>
                                <tr>
                                    <th>Sr No.</th>
                                    <th>Name</th>
                                    <th>Color</th>
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
        var edit_path = "{{ route('expertise-manager.show', ['expertise_manager' => ':expertise_manager']) }}";
        var delete_path = "{{ route('expertise-manager.destroy', ['expertise_manager' => ':expertise_manager']) }}";
        window.canEditTask = @json(canAccess('expertise.edit'));
        window.canDeleteTask = @json(canAccess('expertise.delete'));
    </script>
    <script src="{{ asset('admin/assets/js/custom/expertise_manager.js') }}"></script>
@endsection
