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
                                <div class="row align-items-center">
                                    <label class="col-4 col-form-label">Date Range</label>
                                    <div class="col-8">
                                        <input type="text" id="dateRange"
                                            class="form-control date-range applyFilters">
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Client --}}
                        @if (showFilter('client', $enabledFilters))
                            <div class="col-md-6">
                                <div class="row align-items-center">
                                    <label class="col-4 col-form-label">Client</label>
                                    <div class="col-8">
                                        <select id="filterClient" class="form-control select2 applyFilters">
                                            <option value="">All Clients</option>
                                            @foreach ($clients as $client)
                                                <option value="{{ $client->id }}">{{ $client->client_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Objective --}}
                        @if (showFilter('objective', $enabledFilters))
                            <div class="col-md-6">
                                <div class="row align-items-center">
                                    <label class="col-4 col-form-label">Objective</label>
                                    <div class="col-8">
                                        <select id="filterObjective" class="form-control select2 applyFilters">
                                            <option value="">All Objectives</option>
                                            @foreach ($objectives as $objective)
                                                <option value="{{ $objective->id }}">{{ $objective->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Expertise --}}
                        @if (showFilter('expertise', $enabledFilters))
                            <div class="col-md-6">
                                <div class="row align-items-center">
                                    <label class="col-4 col-form-label">Expertise</label>
                                    <div class="col-8">
                                        <select id="filterExpertise" class="form-control select2 applyFilters">
                                            <option value="">All Expertise</option>
                                            @foreach ($expertiseManagers as $expertise)
                                                <option value="{{ $expertise->id }}">{{ $expertise->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Focus Area --}}
                        @if (showFilter('focus_area', $enabledFilters))
                            <div class="col-md-6">
                                <div class="row align-items-center">
                                    <label class="col-4 col-form-label">Focus Area</label>
                                    <div class="col-8">
                                        <select id="filterFocusArea" class="form-control select2 applyFilters">
                                            <option value="">All Focus Areas</option>
                                            @foreach ($focusAreas as $focus)
                                                <option value="{{ $focus->id }}">{{ $focus->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Priority --}}
                        @if (showFilter('priority', $enabledFilters))
                            <div class="col-md-6">
                                <div class="row align-items-center">
                                    <label class="col-4 col-form-label">Priority</label>
                                    <div class="col-8">
                                        <select id="filterPriority" class="form-control select2 applyFilters">
                                            <option value="">All Priority</option>
                                            @foreach ($priorities as $priority)
                                                <option value="{{ $priority->id }}">{{ $priority->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Status --}}
                        @if (showFilter('status', $enabledFilters))
                            <div class="col-md-6">
                                <div class="row align-items-center">
                                    <label class="col-4 col-form-label">Status</label>
                                    <div class="col-8">
                                        <select id="filterStatus" class="form-control select2 applyFilters">
                                            <option value="">All Status</option>
                                            @foreach ($statuses as $status)
                                                <option value="{{ $status->id }}">{{ $status->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Created By --}}
                        @if (auth()->user()->hasRole(['Super Admin', 'Admin']) && showFilter('created_by', $enabledFilters))
                            <div class="col-md-6">
                                <div class="row align-items-center">
                                    <label class="col-4 col-form-label">Created By</label>
                                    <div class="col-8">
                                        <select id="filterCreatedBy" class="form-control select2 applyFilters">
                                            <option value="">All Users</option>
                                            @foreach ($createdByUsers as $user)
                                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Staff --}}
                        @if (auth()->user()->hasRole(['Super Admin', 'Admin']) && showFilter('staff', $enabledFilters))
                            <div class="col-md-6">
                                <div class="row align-items-center">
                                    <label class="col-4 col-form-label">Assign To</label>
                                    <div class="col-8">
                                        <select id="filterStaff" class="form-control select2 applyFilters">
                                            <option value="">All Staff</option>
                                            @foreach ($staffList as $staff)
                                                <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if (showFilter('entities', $enabledFilters))
                            <div class="col-md-6">
                                <div class="row align-items-center">
                                    <label class="col-4 col-form-label">Entity</label>
                                    <div class="col-8">
                                        <select id="filterEntity" class="form-select applyFilters">
                                            <option value="">All Entities</option>
                                            @foreach ($entities as $entity)
                                                <option value="{{ $entity['id'] }}">{{ $entity['name'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        @endif



                        @if (showFilter('types', $enabledFilters))
                            <div class="col-md-6">
                                <div class="row align-items-center">
                                    <label class="col-4 col-form-label">Task Type</label>
                                    <div class="col-8">
                                        <select id="filterTaskType" class="form-select applyFilters">
                                            <option value="">All Types</option>
                                            @foreach ($types as $type)
                                                <option value="{{ $type['id'] }}">{{ $type['name'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        @endif


                        @if (showFilter('sources', $enabledFilters))
                            <div class="col-md-6">
                                <div class="row align-items-center">
                                    <label class="col-4 col-form-label">Source</label>
                                    <div class="col-8">
                                        <select id="filterSource" class="form-select applyFilters">
                                            <option value="">All Sources</option>
                                            @foreach ($sources as $source)
                                                <option value="{{ $source['id'] }}">{{ $source['name'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
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
