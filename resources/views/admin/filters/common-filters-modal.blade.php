@php
    $currentRoute = Route::currentRouteName();
    $enabledFilters = $filterRouteConfig[$currentRoute] ?? [];

    function showFilter($key, $enabledFilters)
    {
        return in_array($key, $enabledFilters);
    }
@endphp

@if (!empty($enabledFilters))
    <div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">

                {{-- Modal Header --}}
                <div class="modal-header">
                    <h5 class="modal-title" id="filterModalLabel">
                        <i class="ri-filter-3-line me-1"></i> Apply Filters
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                {{-- Modal Body --}}
                <div class="modal-body">

                    <div class="row g-3">

                        {{-- Date Range --}}
                        @if (showFilter('daterange', $enabledFilters))
                            <div class="col-md-6">
                                <label for="dateRange" class="form-label">Date Range </label>
                                <input type="text" id="dateRange" class="form-control date-range applyFilters">
                            </div>
                        @endif
                        {{-- Client --}}
                        @if (showFilter('client', $enabledFilters))
                            <div class="col-md-6">
                                <label class="form-label">Client</label>
                                <select id="filterClient" class="form-control select2 applyFilters">
                                    <option value="">All Clients</option>
                                    @foreach ($clients as $client)
                                        <option value="{{ $client->id }}">{{ $client->client_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        {{-- Objective --}}
                        @if (showFilter('objective', $enabledFilters))
                            <div class="col-md-6">
                                <label class="form-label">Objective</label>
                                <select id="filterObjective" class="form-control select2 applyFilters">
                                    <option value="">All Objectives</option>
                                    @foreach ($objectives as $objective)
                                        <option value="{{ $objective->id }}">{{ $objective->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        {{-- Expertise --}}
                        @if (showFilter('expertise', $enabledFilters))
                            <div class="col-md-6">
                                <label class="form-label">Expertise</label>
                                <select id="filterExpertise" class="form-control select2 applyFilters">
                                    <option value="">All Expertise</option>
                                    @foreach ($expertiseManagers as $expertise)
                                        <option value="{{ $expertise->id }}">{{ $expertise->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        {{-- Focus Area --}}
                        @if (showFilter('focus_area', $enabledFilters))
                            <div class="col-md-6">
                                <label class="form-label">Focus Area</label>
                                <select id="filterFocusArea" class="form-control select2 applyFilters">
                                    <option value="">All Focus Areas</option>
                                    @foreach ($focusAreas as $focus)
                                        <option value="{{ $focus->id }}">{{ $focus->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        {{-- Priority --}}
                        @if (showFilter('priority', $enabledFilters))
                            <div class="col-md-6">
                                <label class="form-label">Priority</label>
                                <select id="filterPriority" class="form-control select2 applyFilters">
                                    <option value="">All Priority</option>
                                    @foreach ($priorities as $priority)
                                        <option value="{{ $priority->id }}">{{ $priority->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        {{-- Status --}}
                        @if (showFilter('status', $enabledFilters))
                            <div class="col-md-6">
                                <label class="form-label">Status</label>
                                <select id="filterStatus" class="form-control select2 applyFilters">
                                    <option value="">All Status</option>
                                    @foreach ($statuses as $status)
                                        <option value="{{ $status->id }}">{{ $status->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        {{-- Created By --}}
                        @if (auth()->user()->hasRole(['Super Admin', 'Admin']) && showFilter('created_by', $enabledFilters))
                            <div class="col-md-6">
                                <label class="form-label">Created By</label>
                                <select id="filterCreatedBy" class="form-control select2 applyFilters">
                                    <option value="">All Users</option>
                                    @foreach ($createdByUsers as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        {{-- Staff --}}
                        @if (auth()->user()->hasRole(['Super Admin', 'Admin']) && showFilter('staff', $enabledFilters))
                            <div class="col-md-6">
                                <label class="form-label">Assign To</label>
                                <select id="filterStaff" class="form-control select2 applyFilters">
                                    <option value="">All Staff</option>
                                    @foreach ($staffList as $staff)
                                        <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                    </div>
                </div>

                {{-- Modal Footer --}}
                <div class="modal-footer d-flex justify-content-end">

                    <div>
                        <!-- Close -->
                        {{-- <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                            <i class="ri-close-line me-1"></i> Close
                        </button> --}}

                        <!-- Reset Filters -->
                        <button type="button" class="btn btn-outline-danger" id="resetFilters">
                            <i class="ri-refresh-line me-1"></i> Reset
                        </button>

                        <!-- Apply Filters -->
                        <button type="button" class="btn btn-primary" id="applyFiltersBtn">
                            <i class="ri-check-line me-1"></i> Apply
                        </button>

                    </div>

                </div>


            </div>
        </div>
    </div>
@endif
