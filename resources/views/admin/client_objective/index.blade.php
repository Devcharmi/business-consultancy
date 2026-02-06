@extends('admin.layouts.app')
@section('content')
 
    <div class="row mt-4">
        <div class="col-xl-12">
            <div class="card custom-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="card-title">
                        Client Objective Manager
                    </div>
                    {{-- @if (canAccess('status manager.create')) --}}
                    <a href="#" data-url="{{ route('client-objective-manager.show', ['client_objective_manager' => 'new']) }}"
                        class="btn btn-success mt-10 d-block text-center open-modal {{ canAccess('client-objective.create') ? '' : 'disabled' }}">+
                        Add Client Objective</a>
                    {{-- @endif --}}
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="client_objective_table" data-url="{{ route('client-objective-manager.index') }}"
                            class="table table-bordered text-nowrap w-100 table-list">
                            <thead>
                                <tr>
                                    <th>Sr No.</th>
                                    <th>Client Name</th>
                                    <th>Objective</th>
                                    <th class="text-center no-export">Action</th>
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
        var edit_path = "{{ route('client-objective-manager.show', ['client_objective_manager' => ':client_objective_manager']) }}";
        var delete_path = "{{ route('client-objective-manager.destroy', ['client_objective_manager' => ':client_objective_manager']) }}";
        var objective_details_path = "{{ route('client-objective.details', ['id' => ':id']) }}";
        window.canEditTask = @json(canAccess('client-objective.edit'));
        window.canDeleteTask = @json(canAccess('client-objective.delete'));
    </script>
    <script src="{{ asset('admin/assets/js/custom/client_objective_manager.js') }}"></script>
@endsection
