var columns = [
    {
        data: "id",
        sClass: "text-center",
        mRender: function (v, t, o) {
            // Admin / Super Admin: Edit + Delete
            var edit_path_set = edit_path.replace(":user_task", o.id);
            var delete_path_set = delete_path.replace(":user_task", o.id);
            let editDisabled = window.canEditTask
                ? ""
                : "style='pointer-events:none;opacity:0.4;' disabled";
            let deleteDisabled = window.canDeleteTask
                ? ""
                : "style='pointer-events:none;opacity:0.4;' disabled";

            html = ` <a href="${edit_path_set}" title="Edit" ${editDisabled}>
                    <i class="fas fa-pen p-1 text-primary"></i>
                </a>
                <a href="javascript:void(0);" data-url="${delete_path_set}" class="delete-data" title="Delete" ${deleteDisabled}>
                    <i class="fas fa-trash p-1 text-danger"></i>
                </a>`;

            return html;
        },
    },
    {
        data: "clients.name",
        mRender: function (v, t, o) {
            return o.clients ? o.clients.client_name : "-";
        },
    },
    {
        data: "task_name",
        mRender: function (v, t, o) {
            let taskName = o.task_name;
            if (!taskName) return "-";

            let shortName =
                taskName.length > 25
                    ? taskName.substring(0, 25) + "..."
                    : taskName;

            return `
            <span title="${taskName}">
                ${shortName}
            </span>`;
        },
    },
    {
        data: "task_start_date",
        mRender: function (v, t, o) {
            return o.task_start_date
                ? moment(o.task_start_date).format("DD-MM-YYYY")
                : "-";
        },
    },
    {
        data: "task_due_date",
        mRender: function (v, t, o) {
            return o.task_due_date
                ? moment(o.task_due_date).format("DD-MM-YYYY")
                : "-";
        },
    },
    {
        data: "created_by.name",
        mRender: function (v, t, o) {
            if (!o.created_by || !o.created_by.name) {
                return `<div class="d-flex justify-content-center">-</div>`;
            }

            const fullName = o.created_by.name;
            const firstLetter = fullName.charAt(0).toUpperCase();

            return `
        <div class="d-flex align-items-center justify-content-center">
            <div class="avatar-circle me-2"
                data-bs-toggle="tooltip"
                data-bs-placement="top"
                title="${fullName}">
                ${firstLetter}
            </div>
        </div>`;
        },
    },
    {
        data: "staff.name",
        mRender: function (v, t, o) {
            if (!o.staff || !o.staff.name) {
                return `<div class="d-flex justify-content-center">-</div>`;
            }

            const fullName = o.staff.name;
            const firstLetter = fullName.charAt(0).toUpperCase();

            return `
        <div class="d-flex align-items-center justify-content-center">
            <div class="avatar-circle me-2"
                data-bs-toggle="tooltip"
                data-bs-placement="top"
                title="${fullName}">
                ${firstLetter}
            </div>
        </div>`;
        },
    },
    {
        data: "status_manager",
        mRender: function (v, t, o) {
            if (o.status_manager) {
                var name = o.status_manager.name;
                var color = o.status_manager.color_name || "gray"; // fallback color
                return `<span style="background-color:${color}; color:#fff; padding:2px 6px; border-radius:4px;">${name}</span>`;
            }
            return "N/A";
        },
    },
    {
        data: "priority_manager.name",
        mRender: function (v, t, o) {
            if (!o.priority_manager) return "-";
            return `<span class="badge" style="background-color:${o.priority_manager.color_name};">
                        ${o.priority_manager.name}
                    </span>`;
        },
    },
    {
        data: "source_type",
        mRender: function (v, t, o) {
            if (!o.source_type) return "-";
            return (
                o.source_type.charAt(0).toUpperCase() + o.source_type.slice(1)
            );
        },
    },
];

var task_table = $(".table-list").DataTable({
    order: [[0, "desc"]],
    autoWidth: false,
    processing: true,
    serverSide: true,
    serverMethod: "GET",
    lengthMenu: [
        [25, 100, 200, 250],
        [25, 100, 200, 250],
    ],
    dom: `
        <"row mb-2"
            <"col-md-6"B>
            <"col-md-6 text-end"f>
        >
        <"row"<"col-md-12"tr>>
        <"row mt-2"
            <"col-md-5"l>
            <"col-md-7"p>
        >
    `,
    ajax: {
        url: $("#task_table").attr("data-url"),
        data: function (d) {
            d.status = $("#taskTabs .nav-link.active").data("status"); // send tab status
            d.dateRange = $("#dateRange").val();
            d.filterClient = $("#filterClient").val();
            d.filterStaff = $("#filterStaff").val();
            d.filterCreatedBy = $("#filterCreatedBy").val();
            d.filterStatus = $("#filterStatus").val();
            d.filterPriority = $("#filterPriority").val();
        },
        dataSrc: function (json) {
            // console.log(json);
            // 🔹 Update tab counts (if available)
            if (json.counts) {
                $("#allCount").text(json.counts.all);
                $("#todayCount").text(json.counts.today);
                $("#overdueCount").text(json.counts.overdue);
                $("#pendingCount").text(json.counts.pending);
                $("#doneCount").text(json.counts.done);
            }

            if (json.unread_task_ids) {
                unreadTaskIds = json.unread_task_ids;
            }

            // Return table data
            return json.aaData;
        },
        error: function (xhr, status, error) {
            console.error("DataTables AJAX Error:", error);
        },
    },
    columns: columns,
    drawCallback: function () {
        // Initialize Bootstrap tooltips after table draw
        const tooltipTriggerList = [].slice.call(
            document.querySelectorAll('[data-bs-toggle="tooltip"]'),
        );
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    },
    language: {
        searchPlaceholder: "Search...",
        sSearch: "",
        lengthMenu: "_MENU_&nbsp; items/page",
    },
});

$(document).on("change", ".applyFilters", function () {
    task_table.draw();
});

// Trigger on page load
toggleTodayTab();

// Trigger on date range change
$(".date-range").on(
    "apply.daterangepicker cancel.daterangepicker keyup change",
    function () {
        toggleTodayTab();
    },
);

function toggleTodayTab() {
    let dateRange = $(".date-range").val() || ""; // prevents undefined
    let todayTab = $('a[data-status="today"]').closest("li");
    let allTab = $('a[data-status="all"]');

    if (dateRange.trim() !== "") {
        // Hide Today tab
        todayTab.hide();

        // Activate ALL tab
        $("#taskTabs .nav-link").removeClass("active");
        allTab.addClass("active");
    } else {
        // Show Today tab
        todayTab.show();

        // Activate Today tab
        $("#taskTabs .nav-link").removeClass("active");
        $('a[data-status="today"]').addClass("active");
    }
}

// Reset Filters Button
$(document).on("click", "#resetFilters", function () {
    // Clear all filter dropdowns
    $(".applyFilters").val("").trigger("change");

    // ✅ If you're using DataTables with AJAX filtering:
    if (typeof task_table !== "undefined") {
        task_table.draw();
    }
});

let isProjectSetFromWork = false;

$(document).on("click", "#taskTabs .nav-link", function (e) {
    e.preventDefault();

    // remove active from all tabs
    $("#taskTabs .nav-link").removeClass("active");
    $(this).addClass("active");

    // reload table with both status + segment applied
    task_table.draw();
});

$(document).on("click", "#task_form_button", function (e) {
    e.preventDefault(); // prevent default form submit
    let form = $("#task_form")[0]; // get DOM element
    let url = $(this).attr("data-url");
    updateTextareasFromEditors("description");

    let formData = new FormData(form); // automatically includes file inputs

    $.ajax({
        url: url,
        type: "POST",
        data: formData,
        dataType: "json",
        processData: false, // ✅ important!
        contentType: false, // ✅ important!
        cache: false, // optional but good practice
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        beforeSubmit: function () {},
        success: function (result) {
            showToastr("success", result.message);
            setTimeout(() => {
                location.href = index_path;
            }, 2000);
        },
        error: function (result) {
            $("[id$='_error']").empty();
            showToastr("error", result.responseJSON.message);
            $.each(result.responseJSON.errors, function (k, v) {
                var id_arr = k.split(".");
                $("#task_form")
                    .find("#" + id_arr[0] + "_error")
                    .text(v);
            });
        },
    });
    return false;
});

$(document).on("click", ".delete-data", function (e) {
    e.preventDefault();
    swal.fire({
        title: "Are you sure to delete this record?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
    }).then((result) => {
        if (result.isConfirmed) {
            var delete_url = $(this).attr("data-url");
            $.ajax({
                url: delete_url,
                type: "DELETE",
                data: {
                    _token: csrf_token,
                },
                success: function (result) {
                    $("[id$='_error']").empty();
                    task_table.draw();
                    // refreshTabCounts();
                    showToastr("success", result.message);
                },
                error: function (result) {
                    showToastr("error", result.responseJSON.message);
                },
            });
        }
    });
});
