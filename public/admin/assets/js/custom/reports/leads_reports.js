var lead_report_table = $("#leadReportTable").DataTable({
    order: [[0, "desc"]],
    processing: true,
    serverSide: true,
    serverMethod: "GET",
    autoWidth: false,
    dom: REPORT_TABLE_DOM,
    buttons: getReportButtons("Lead_Report"),
    lengthMenu: [
        [25, 100, 200],
        [25, 100, 200],
    ],
    ajax: {
        url: $("#leadReportTable").data("url"),
        data: function (d) {
            d.dateRange = $("#dateRange").val();
            d.filterClient = $("#filterClient").val();
            d.filterCreatedBy = $("#filterCreatedBy").val();
            d.filterStatus = $("#filterStatus").val();
        },
    },
    columns: [
        { data: "client" },
        { data: "email" },
        { data: "phone" },
        { data: "status" },
        { data: "assigned_to" },
        { data: "followups", className: "text-center" },
        { data: "pending", className: "text-center" },
        { data: "next_followup" },
    ],
    language: {
        searchPlaceholder: "Search...",
        sSearch: "",
        lengthMenu: "_MENU_ items/page",
    },
});

$(document).on("change", ".applyFilters", function () {
    lead_report_table.draw();
});

$(document).on("click", "#resetFilters", function () {
    $(".applyFilters").val("").trigger("change");
    lead_report_table.draw();
});
