var client_report_table = $("#clientReportTable").DataTable({
    order: [[0, "desc"]],
    autoWidth: false,
    processing: true,
    serverSide: true,
    serverMethod: "GET",
    dom: REPORT_TABLE_DOM,
    buttons: getReportButtons("Client_Report"),
    lengthMenu: [
        [25, 100, 200, 250],
        [25, 100, 200, 250],
    ],
    ajax: {
        url: $("#clientReportTable").attr("data-url"),
        data: function (d) {
            d.dateRange = $("#dateRange").val();
            d.filterClient = $("#filterClient").val();
            d.filterCreatedBy = $("#filterCreatedBy").val();
        },
    },
    columns: [
        { data: "client_name" },
        { data: "email" },
        { data: "phone" },
        { data: "status" },
        { data: "created_by" },
        // { data: "updated_by" },
        { data: "objectives", className: "text-center" },
        { data: "consultings", className: "text-center" },
        { data: "meetings", className: "text-center" },
    ],
    language: {
        searchPlaceholder: "Search...",
        sSearch: "",
        lengthMenu: "_MENU_&nbsp; items/page",
    },
});

$(document).on("change", ".applyFilters", function () {
    client_report_table.draw();
});

// Reset Filters Button
$(document).on("click", "#resetFilters", function () {
    // Clear all filter dropdowns
    $(".applyFilters").val("").trigger("change");

    // ✅ If you're using DataTables with AJAX filtering:
    if (typeof client_report_table !== "undefined") {
        client_report_table.draw();
    }
});
