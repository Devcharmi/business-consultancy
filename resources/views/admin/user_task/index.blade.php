@extends('admin.layouts.app')
@section('styles')
    <style>
        .bold-row {
            font-weight: bold !important;
            background-color: #fff8d6 !important;
            /* optional */
        }

        .avatar-circle {
            width: 32px;
            height: 32px;
            background-color: #6c78d1;
            border-radius: 50%;
            color: white;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
        }

        #taskTabs .nav-item {
            border: 1px solid #ddd;
            /* light gray border */
            border-radius: 5px;
            /* rounded corners */
            margin-right: 5px;
            /* space between tabs */
        }

        #taskTabs .nav-link {
            padding: 8px 15px;
            /* adjust padding */
            border: none;
            /* remove default border of nav-link */
        }

        #taskTabs .nav-item .nav-link.active {
            background-color: #007bff;
            /* active tab background */
            color: #fff;
            /* active tab text color */
        }

        #taskTabs .nav-item .nav-link:hover {
            background-color: #f1f1f1;
            color: #000;
        }
    </style>
    <style>
        #task_table {
            /* table-layout: fixed !important; */
            /* force equal column width handling */
            width: 100% !important;
        }

        /* Target Name column */
        #task_table th:nth-child(2),
        #task_table td:nth-child(2) {
            /* width: 500px !important; */
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        /* If you want ellipsis effect to be reliable */
        #task_table td:nth-child(2) div.text-ellipsis {
            display: block;
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
            /* max-width: 500px; */
        }

        /* Optional: add pointer and tooltip effect */
        #task_table td:nth-child(2) div.text-ellipsis {
            cursor: help;
        }

        #task_table tbody tr.bold-row td {
            font-weight: 700 !important;
        }

        .text-ellipsis {
            display: inline-block;
            max-width: 250px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            vertical-align: middle;
            cursor: pointer;
        }
    </style>
@endsection
@section('content')
    <!-- Page Header -->
    <div class="d-flex align-items-center justify-content-between page-header-breadcrumb flex-wrap gap-2">
        {{-- <div>
            <nav>
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Task</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Task</li>
                </ol>
            </nav>
            <h1 class="page-title fw-medium fs-18 mb-0">View Task</h1>
        </div> --}}
    </div>
    <!-- Page Header Close -->

    <div class="row">
        <div class="col-xl-12">
            <div class="card custom-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="card-title">Task Manager</div>
                    <a href="{{ route('user-task.show', ['user_task' => 'new']) }}"
                        class="btn btn-success {{ canAccess('user-task.create') ? '' : 'disabled' }}">+ Add
                        Task</a>
                </div>

                <div class="card-body">
                    
                    @include('admin.filters.common-filters')

                    <ul class="nav nav-tabs mb-3" id="taskTabs">
                        <li class="nav-item">
                            <a class="nav-link" data-status="all" href="#">
                                All (<span id="allCount"></span>)
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" data-status="today" href="#">
                                Today's Task (<span id="todayCount"></span>)
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-status="overdue" href="#">
                                Overdue (<span id="overdueCount"></span>)
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-status="pending" href="#">
                                Pending (<span id="pendingCount"></span>)
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-status="done" href="#">
                                Done (<span id="doneCount"></span>)
                            </a>
                        </li>
                    </ul>

                    <!-- Table -->
                    <div class="table-responsive">
                        <table id="task_table" data-url="{{ route('user-task.index') }}"
                            class="table table-bordered text-nowrap w-100 table-list">
                            <thead>
                                <tr>
                                    <th class="text-center no-export">Action</th>
                                    <th>Client</th>
                                    <th>Task</th>
                                    <th>Start Date</th>
                                    <th>Due Date</th>
                                    <th>Created by</th>
                                    <th>Assign To</th>
                                    <th>Status</th>
                                    <th>Priority</th>
                                    <th>Source</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
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
        // var userRoles = @json(auth()->user()->getRoleNames()); // array of role names
        var index_path = "{{ route('user-task.index') }}";
        var edit_path = "{{ route('user-task.show', ['user_task' => ':user_task']) }}";
        var delete_path = "{{ route('user-task.destroy', ['user_task' => ':user_task']) }}";

        window.canEditTask = @json(canAccess('user-task.edit'));
        window.canDeleteTask = @json(canAccess('user-task.delete'));
    </script>
    <script src="{{ asset('admin/assets/js/custom/user_task.js') }}"></script>
@endsection
