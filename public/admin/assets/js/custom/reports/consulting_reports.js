var consulting_report_table = $("#consultingReportTable").DataTable({
    order: [[0, "desc"]],
    autoWidth: false,
    processing: true,
    serverSide: true,
    serverMethod: "GET",
    dom: REPORT_TABLE_DOM,
    buttons: getReportButtons("Consulting_Report"),
    lengthMenu: [
        [25, 100, 200, 250],
        [25, 100, 200, 250],
    ],
    ajax: {
        url: $("#consultingReportTable").data("url"),
        data: function (d) {
            d.dateRange = $("#dateRange").val();
            d.filterClient = $("#filterClient").val();
            d.filterObjective = $("#filterObjective").val();
            d.filterExpertise = $("#filterExpertise").val();
        },
    },
    columns: [
        { data: "client" },
        { data: "objective" },
        {
            data: "expertise",
            render: function (data) {
                if (!data || !data.name) return "-";

                return `
                <span
                    class="badge px-3 py-1"
                    style="
                        background-color: ${data.color_name} !important;
                        color: #fff;
                        font-size: 13px;
                    "
                >
                    ${data.name}
                </span>
            `;
            },
        },
        { data: "total", className: "text-center" },
        { data: "last_date" },
    ],
    language: {
        searchPlaceholder: "Search...",
        sSearch: "",
        lengthMenu: "_MENU_&nbsp; items/page",
    },
});

// APPLY FILTERS (Modal Apply Button)
$(document).on("click", "#applyFiltersBtn", function () {
    if (typeof consulting_report_table !== "undefined") {
        consulting_report_table.draw();
    }

    // Close modal
    $("#filterModal").modal("hide");
});

// RESET FILTERS
$(document).on("click", "#resetFilters", function () {
    // Clear all dropdowns
    $(".applyFilters").val("").trigger("change");

    if (typeof consulting_report_table !== "undefined") {
        consulting_report_table.draw();
    }
});

// $(document).on("change", ".applyFilters", function () {
//     consulting_report_table.draw();
// });

// $(document).on("click", "#resetFilters", function () {
//     $(".applyFilters").val("").trigger("change");
//     consulting_report_table.draw();
// });
