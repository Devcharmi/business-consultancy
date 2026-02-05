var objective_report_table = $("#objectiveReportTable").DataTable({
    order: [[0, "desc"]],
    autoWidth: false,
    processing: true,
    serverSide: true,
    serverMethod: "GET",
    dom: REPORT_TABLE_DOM,
    buttons: getReportButtons("Objective_Report"),
    lengthMenu: [
        [25, 100, 200, 250],
        [25, 100, 200, 250],
    ],
    ajax: {
        url: $("#objectiveReportTable").data("url"),
        data: function (d) {
            d.dateRange = $("#dateRange").val();
            d.filterClient = $("#filterClient").val();
            d.filterCreatedBy = $("#filterCreatedBy").val();
            d.filterObjective = $("#filterObjective").val();
        },
    },
    columns: [
        { data: "client" },
        { data: "objective" },
        { data: "consultings", className: "text-center" },
        { data: "meetings", className: "text-center" },
        { data: "created_by" },
    ],
    language: {
        searchPlaceholder: "Search...",
        sSearch: "",
        lengthMenu: "_MENU_&nbsp; items/page",
    },
});

$(document).on("change", ".applyFilters", function () {
    objective_report_table.draw();
});

$(document).on("click", "#resetFilters", function () {
    $(".applyFilters").val("").trigger("change");
    objective_report_table.draw();
});
