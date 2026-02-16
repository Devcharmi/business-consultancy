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
        {
            data: "status",
            render: function (data, type, row) {
                if (!data) return "-";

                let color = "secondary";

                if (data.toLowerCase() === "active") {
                    color = "success"; // green
                }

                if (data.toLowerCase() === "inactive") {
                    color = "danger"; // red
                }

                return `<span class="badge bg-${color}">
                    ${data}
                </span>`;
            },
        },
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

// APPLY FILTERS (Modal Apply Button)
$(document).on("click", "#applyFiltersBtn", function () {
    if (typeof client_report_table !== "undefined") {
        client_report_table.draw();
    }

    // Close modal
    $("#filterModal").modal("hide");
});

// RESET FILTERS
$(document).on("click", "#resetFilters", function () {
    // Clear all dropdowns
    $(".applyFilters").val("").trigger("change");

    if (typeof client_report_table !== "undefined") {
        client_report_table.draw();
    }
});

// $(document).on("change", ".applyFilters", function () {
//     client_report_table.draw();
// });

// // Reset Filters Button
// $(document).on("click", "#resetFilters", function () {
//     // Clear all filter dropdowns
//     $(".applyFilters").val("").trigger("change");

//     // ✅ If you're using DataTables with AJAX filtering:
//     if (typeof client_report_table !== "undefined") {
//         client_report_table.draw();
//     }
// });
