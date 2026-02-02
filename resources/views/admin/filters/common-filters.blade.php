{{-- ================= Date Range ================= --}}
<div class="row mb-3">
    <div class="col-md-12">
        @include('admin.filters.daterangefilter')
    </div>
</div>

{{-- ================= Main Filters ================= --}}
<div class="row mb-3">

    {{-- Staff (Admin only) --}}
    @if (auth()->user()->hasRole(['Super Admin', 'Admin']))
        <div class="col-md-3">
            <label class="form-label">Staff</label>
            <select id="filterStaff" class="form-control select2 applyFilters">
                <option value="">All Staff</option>
                @foreach ($staffList as $staff)
                    <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                @endforeach
            </select>
        </div>
    @endif

    {{-- Client --}}
    <div class="col-md-3">
        <label class="form-label">Client</label>
        <select id="filterClient" class="form-control select2 applyFilters">
            <option value="">All Clients</option>
            @foreach ($clients as $client)
                <option value="{{ $client->id }}">{{ $client->client_name }}</option>
            @endforeach
        </select>
    </div>

    {{-- Objective --}}
    <div class="col-md-3">
        <label class="form-label">Objective</label>
        <select id="filterObjective" class="form-control select2 applyFilters">
            <option value="">All Objectives</option>
            @foreach ($objectives as $objective)
                <option value="{{ $objective->id }}">{{ $objective->title }}</option>
            @endforeach
        </select>
    </div>

    {{-- Status --}}
    <div class="col-md-3">
        <label class="form-label">Status</label>
        <select id="filterStatus" class="form-control select2 applyFilters">
            <option value="">All Status</option>
            @foreach ($statuses as $status)
                <option value="{{ $status->id }}">{{ $status->name }}</option>
            @endforeach
        </select>
    </div>
</div>

{{-- ================= Secondary Filters ================= --}}
<div class="row mb-3">

    {{-- Expertise --}}
    <div class="col-md-3">
        <label class="form-label">Expertise</label>
        <select id="filterExpertise" class="form-control select2 applyFilters">
            <option value="">All Expertise</option>
            @foreach ($expertiseManagers as $expertise)
                <option value="{{ $expertise->id }}">{{ $expertise->name }}</option>
            @endforeach
        </select>
    </div>

    {{-- Focus Area --}}
    <div class="col-md-3">
        <label class="form-label">Focus Area</label>
        <select id="filterFocusArea" class="form-control select2 applyFilters">
            <option value="">All Focus Areas</option>
            @foreach ($focusAreas as $focus)
                <option value="{{ $focus->id }}">{{ $focus->name }}</option>
            @endforeach
        </select>
    </div>

</div>
