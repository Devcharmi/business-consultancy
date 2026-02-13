@extends('admin.layouts.app')
@section('content')
    <div class="row mt-4">
        <div class="col-xl-12">
            <div class="card custom-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="card-title">
                        CVR Manager
                    </div>
                    <div class="d-flex gap-2">
                        <!-- Open Filter Modal -->
                        <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#filterModal"
                            title="Filters">
                            <i class="ri-filter-3-line"></i>
                        </button>

                        <!-- Reset Filters -->
                        <button class="btn btn-outline-danger btn-sm" id="resetFilters" title="Reset">
                            <i class="ri-refresh-line"></i>
                        </button>
                        {{-- @if (canAccess('status manager.create')) --}}
                        <a href="{{ route('task.show', ['task' => 'new']) }}"
                            class="btn btn-success mt-10 d-block text-center open-modal {{ canAccess('task.create') ? '' : 'disabled' }}">+
                            Add CVR</a>
                        {{-- @endif --}}
                    </div>
                </div>
                <div class="card-body">

                    @include('admin.filters.common-filters-modal')
                    {{-- @include('admin.filters.common-filters') --}}

                    <div class="table-responsive">
                        <table id="task_table" data-url="{{ route('task.index') }}"
                            class="table table-bordered text-nowrap w-100 table-list">
                            <thead>
                                <tr>
                                    <th>Sr No.</th>
                                    <th>Date</th>
                                    <th>Title</th>
                                    <th>Client Name</th>
                                    <th>Objective</th>
                                    <th>Expertise</th>
                                    <th>Due Date</th>
                                    <th>Status</th>
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
        var edit_path = "{{ route('task.show', ['task' => ':task']) }}";
        var delete_path = "{{ route('task.destroy', ['task' => ':task']) }}";
        var pdf_path = "{{ route('task.pdf', ['task' => ':task']) }}";
        window.canEditTask = @json(canAccess('task.edit'));
        window.canDeleteTask = @json(canAccess('task.delete'));
         $(".select2").select2({
            placeholder: "Select...",
            width: "100%",
            dropdownParent: $("#filterModal"),
            // allowClear: true,
            // closeOnSelect: false, // keep dropdown open for multiple selections
        });
    </script>
    <script src="{{ asset('admin/assets/js/custom/task.js') }}"></script>
@endsection
