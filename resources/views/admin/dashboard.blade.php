@extends('admin.layouts.app')

@section('content')
    <div class="d-flex align-items-center justify-content-between page-header-breadcrumb flex-wrap gap-2 mb-3">
        <div>
            <nav>
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item active"><a href="javascript:void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item">Today's Tasks</li>
                </ol>
            </nav>
            <h1 class="page-title fw-medium fs-18 mb-0">Dashboard</h1>
        </div>
    </div>

    @include('admin.filters.daterangefilter')

    <div class="row">
        <div class="col-12">
            <div class="card custom-card">

                {{-- Tabs --}}
                <div class="card-header d-flex justify-content-between align-items-center">
                    <ul class="nav nav-tabs card-header-tabs">
                        @if (canAccess('consulting.allow'))
                            <li class="nav-item">
                                <button class="nav-link active" id="tab-task-statistics" data-bs-toggle="tab"
                                    data-bs-target="#task-statistics">
                                    Task Statistics
                                </button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link" id="tab-today-tasks" data-bs-toggle="tab"
                                    data-bs-target="#today-tasks-tab">
                                    Task & Followups
                                </button>
                            </li>
                        @else
                            <li class="nav-item">
                                <button class="nav-link" id="tab-today-tasks active" data-bs-toggle="tab"
                                    data-bs-target="#today-tasks-tab">
                                    Task & Followups
                                </button>
                            </li>
                        @endif

                    </ul>
                </div>

                {{-- <div class="card-body"> --}}
                <div class="tab-content">

                    @if (auth()->user()->hasRole(['Super Admin', 'Admin']))
                        {{-- ================= TAB 1 ================= --}}
                        <div class="tab-pane fade show active" id="task-statistics">
                            @include('admin.dashboard-calendar-tab')
                        </div>
                    @endif
                    {{-- ================= TAB 2 ================= --}}
                    <div class="tab-pane fade" id="today-tasks-tab">
                        @include('admin.dashboard-task-tab')
                    </div>

                </div>
                {{-- </div> --}}
            </div>
        </div>
    </div>
    @include('admin.dashboard-task-modal')
@endsection

@section('script')
    <script>
        var csrf_token = "{{ csrf_token() }}";
        var routeDayConsultings = "{{ route('dashboard.dayConsultings') }}";
        var routeUpdateStatue = "{{ route('dashboard.update-status') }}";

    </script>
    <script src="{{ asset('admin/assets/js/custom/dashboard.js') }}"></script>
@endsection
