@extends('admin.layouts.app')
@section('content')
    <div class="row mt-4">
        <div class="col-xl-12">
            <div class="card custom-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="card-title">
                        Consulting Manager
                    </div>

                    <div class="d-flex gap-2">
                        {{-- Import / Export Dropdown --}}
                        <div class="dropdown">
                            <button class="btn btn-outline-primary btn-sm dropdown-toggle" type="button"
                                id="importExportDropdown" data-bs-toggle="dropdown" aria-expanded="false"
                                title=" Import / Export">
                                <i class="ri-upload-2-line"></i>
                            </button>

                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="importExportDropdown">
                                {{-- Import --}}
                                <li>
                                    <a class="dropdown-item" href="#" data-bs-toggle="modal"
                                        data-bs-target="#importConsultingModal">
                                        <i class="ri-upload-line me-2"></i> Import Consulting
                                    </a>
                                </li>

                                {{-- Download Sample --}}
                                <li>
                                    <a class="dropdown-item" href="{{ route('consulting.sample.download') }}">
                                        <i class="ri-download-2-line me-2"></i> Download Sample File
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <!-- Open Filter Modal -->
                        <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#filterModal"
                            title="Filters">
                            <i class="ri-filter-3-line"></i>
                        </button>

                        <!-- Reset Filters -->
                        <button class="btn btn-outline-danger btn-sm" id="resetFilters" title="Reset">
                            <i class="ri-refresh-line"></i>
                        </button>

                        <a href="#" data-url="{{ route('consulting.show', ['consulting' => 'new']) }}"
                            class="btn btn-success mt-10 d-block text-center open-modal {{ canAccess('consulting.create') ? '' : 'disabled' }}">+
                            Add Consulting</a>
                    </div>

                </div>
                <div class="card-body">

                    @include('admin.filters.common-filters-modal')
                    {{-- @include('admin.filters.common-filters') --}}

                    <div class="table-responsive">
                        <table id="consulting_table" data-url="{{ route('consulting.index') }}"
                            class="table table-bordered text-nowrap w-100 table-list">
                            <thead>
                                <tr>
                                    <th class="text-center no-export">Action</th>
                                    <th>Client Name</th>
                                    <th>Objective</th>
                                    <th>Expertise</th>
                                    <th>Focus Area</th>
                                    <th>Date</th>
                                    <th>Start Time</th>
                                    <th>End Time</th>
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
    @include('admin.dashboard-task-modal')
    @include('admin.consulting.import-modal')
@endsection
@section('script')
    <script>
        var csrf_token = '{{ csrf_token() }}';
        var edit_path = "{{ route('consulting.show', ['consulting' => ':consulting']) }}";
        var delete_path = "{{ route('consulting.destroy', ['consulting' => ':consulting']) }}";
        window.canEditTask = @json(canAccess('consulting.edit'));
        window.canDeleteTask = @json(canAccess('consulting.delete'));

        $(".select2").select2({
            placeholder: "Select...",
            width: "100%",
            dropdownParent: $("#filterModal"),
            // allowClear: true,
            // closeOnSelect: false, // keep dropdown open for multiple selections
        });
    </script>
    @if (session()->has('import_errors') || session()->has('success'))
        <script>
            $(document).ready(function() {
                var importModal = new bootstrap.Modal($('#importConsultingModal')[0]);
                importModal.show();
            });
        </script>
    @endif

    <script src="{{ asset('admin/assets/js/custom/consulting.js') }}"></script>
@endsection
