@extends('admin.layouts.app')
@section('content')
  
    <div class="row mt-4">
        <div class="col-xl-12">
            <div class="card custom-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="card-title">
                        Consulting Manager
                    </div>
                    {{-- @if (canAccess('status manager.create')) --}}
                    <a href="#" data-url="{{ route('consulting.show', ['consulting' => 'new']) }}"
                        class="btn btn-success mt-10 d-block text-center open-modal {{ canAccess('consulting.create') ? '' : 'disabled' }}">+
                        Add Consulting</a>
                    {{-- @endif --}}
                </div>
                <div class="card-body">

                    @include('admin.filters.common-filters')
                    
                    <div class="table-responsive">
                        <table id="consulting_table" data-url="{{ route('consulting.index') }}"
                            class="table table-bordered text-nowrap w-100 table-list">
                            <thead>
                                <tr>
                                    <th>Sr No.</th>
                                    <th>Date</th>
                                    <th>Client Name</th>
                                    <th>Objective</th>
                                    <th>Expertise</th>
                                    <th>Focus Area</th>
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
        var edit_path = "{{ route('consulting.show', ['consulting' => ':consulting']) }}";
        var delete_path = "{{ route('consulting.destroy', ['consulting' => ':consulting']) }}";
        window.canEditTask = @json(canAccess('consulting.edit'));
        window.canDeleteTask = @json(canAccess('consulting.delete'));
    </script>
    <script src="{{ asset('admin/assets/js/custom/consulting.js') }}"></script>
@endsection
