var user_task_report_table = $("#userTaskReportTable").DataTable({
    order: [[3, "desc"]], // order by start_date desc
    processing: true,
    serverSide: true,
    serverMethod: "GET",
    autoWidth: false,
    dom: REPORT_TABLE_DOM,
    buttons: getReportButtons("User_Task_Report"),
    lengthMenu: [
        [25, 100, 200],
        [25, 100, 200],
    ],
    ajax: {
        url: $("#userTaskReportTable").data("url"),
        data: function (d) {
            d.dateRange = $("#dateRange").val();
            d.filterStaff = $("#filterStaff").val();
            d.filterClient = $("#filterClient").val();
            d.filterStatus = $("#filterStatus").val();
            d.filterPriority = $("#filterPriority").val();
            d.filterEntity = $("#filterEntity").val();
            d.filterTaskType = $("#filterTaskType").val();
        },
    },
    columns: [
        { data: "task_name" },
        { data: "entity" },
        { data: "task_type" },
        { data: "start_date" },
        { data: "due_date" },
        {
            data: "priority_name",
            render: function (data, type, row) {
                if (!data) return "-";

                let color = row.priority_color ? row.priority_color : "#6c757d";

                return `<span class="badge" style="background-color:${color}; color:#fff;">
                    ${data}
                </span>`;
            },
        },
        {
            data: "status_name",
            render: function (data, type, row) {
                if (!data) return "-";

                let color = row.status_color ? row.status_color : "#6c757d";

                return `<span class="badge" style="background-color:${color}; color:#fff;">
                    ${data}
                </span>`;
            },
        },
        { data: "assigned_to" },
        { data: "overdue", orderable: false, searchable: false },
    ],
    language: {
        searchPlaceholder: "Search...",
        sSearch: "",
        lengthMenu: "_MENU_&nbsp; items/page",
    },
});

// APPLY FILTERS
$(document).on("click", "#applyFiltersBtn", function () {
    if (typeof user_task_report_table !== "undefined") {
        user_task_report_table.draw();
    }

    $("#filterModal").modal("hide");
});

// RESET FILTERS
$(document).on("click", "#resetFilters", function () {
    $(".applyFilters").val("").trigger("change");

    if (typeof user_task_report_table !== "undefined") {
        user_task_report_table.draw();
    }
});
