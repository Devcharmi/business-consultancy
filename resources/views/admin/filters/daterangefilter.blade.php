{{-- <div class="mb-2 d-flex align-items-center justify-content-end">
    <label for="dateRange" class="form-label me-2 mb-0 fw-bold">Select Date Range </label>
    <input type="text" id="dateRange" class="form-control date-range applyFilters me-3" style="width: 250px;">
    <button type="button" id="resetFilters" class="btn btn-secondary ms-1">
        <i class="fa fa-undo"></i> Reset Filters
    </button>
</div> --}}
<div class="row align-items-end mb-2">
    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
        <label for="form-label" class="form-label">Date Range</label>
        {{-- <input type="text" id="dateRange" class="form-control date-range applyFilters" autocomplete="off"> --}}
        <div class="d-flex align-items-center gap-2 flex-nowrap">
            <input type="text" id="dateRange" class="form-control date-range applyFilters flex-grow-1"
                autocomplete="off">

            <strong id="selectedRangeLabel" class="text-nowrap mb-0"></strong>
        </div>

    </div>

    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 d-flex justify-content-end mt-2 mt-md-0">
        <button type="button" id="resetFilters" class="btn btn-secondary">
            <i class="fa fa-undo me-1"></i> Reset Filters
        </button>
    </div>
</div>
