var task_table = $(".table-list").DataTable({
    order: [[0, "desc"]],
    autoWidth: false,
    processing: true,
    serverSide: true,
    serverMethod: "GET",
    dom: REPORT_TABLE_DOM,
    buttons: getReportButtons("Meetings_Report"),
    lengthMenu: [
        [25, 100, 200, 250],
        [25, 100, 200, 250],
    ],
    ajax: {
        url: $("#task_table").attr("data-url"),
        data: function (d) {
            d.dateRange = $("#dateRange").val();
            d.filterClient = $("#filterClient").val();
            d.filterObjective = $("#filterObjective").val();
            d.filterExpertise = $("#filterExpertise").val();
            d.filterCreatedBy = $("#filterCreatedBy").val();
            d.filterStatus = $("#filterStatus").val();
        },
    },
    columns: [
        // 1️⃣ Sr No
        {
            data: "id",
            render: function (data, type, row, meta) {
                return meta.row + meta.settings._iDisplayStart + 1;
            },
        },

        // 6️⃣ Date
        {
            data: "task_start_date",
            render: function (data) {
                return data ? moment(data).format("DD-MM-YYYY") : "-";
            },
        },

        // 2️⃣ title
        {
            data: "title",
            render: function (data) {
                return data ? data : "-";
            },
        },

        // 2️⃣ Client Name
        {
            data: "client_objective",
            render: function (data, type, row) {
                return data && data.client ? data.client.client_name : "-";
            },
        },

        // 3️⃣ Objective
        {
            data: "client_objective",
            render: function (data, type, row) {
                return data && data.objective_manager
                    ? data.objective_manager.name
                    : "-";
            },
        },

        // 4️⃣ Expertise
        {
            data: "expertise_manager",
            render: function (data) {
                if (!data) return "-";

                return `
            <span
                class="badge"
                style="
                    background-color: ${data.color ?? data.color_name ?? "#6c757d"};
                    color: #fff;
                    font-size: 11px;
                    padding: 4px 8px;
                    border-radius: 4px;
                "
            >
                ${data.name}
            </span>
        `;
            },
        },

        // 6️⃣ Due Date
        {
            data: "task_due_date",
            render: function (data) {
                return data ? moment(data).format("DD-MM-YYYY") : "-";
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
        // 7️⃣ Action
        {
            data: "id",
            orderable: false,
            searchable: false,
            className: "text-center",
            render: function (id, type, row) {
                let pdf_path_set = pdf_path.replace(":task", id);
                let edit_path_set = edit_path.replace(":task", id);
                let delete_path_set = delete_path.replace(":task", id);

                let editDisabled = window.canEditTask
                    ? ""
                    : "style='pointer-events:none;opacity:0.4;' disabled";
                let deleteDisabled = window.canDeleteTask
                    ? ""
                    : "style='pointer-events:none;opacity:0.4;' disabled";

                return `
                   <a href="${pdf_path_set}" title="Download Pdf" target="_blank">
                    <i class="fas fa-file-pdf p-1 text-secondary"></i>
                    </a>
                    <a href="${edit_path_set}"
                       class="open-modal" title="Edit" ${editDisabled}>
                        <i class="fas fa-pen p-1 text-primary"></i>
                    </a>
                    <a href="javascript:void(0);" data-url="${delete_path_set}"
                       class="delete-data" title="Delete" ${deleteDisabled}>
                        <i class="fas fa-trash p-1 text-danger"></i>
                    </a>
                `;
            },
        },
    ],
    language: {
        searchPlaceholder: "Search...",
        sSearch: "",
        lengthMenu: "_MENU_&nbsp; items/page",
    },
});

$(document).on("change", ".applyFilters", function () {
    task_table.draw();
});

// Reset Filters Button
$(document).on("click", "#resetFilters", function () {
    // Clear all filter dropdowns
    $(".applyFilters").val("").trigger("change");

    // ✅ If you're using DataTables with AJAX filtering:
    if (typeof task_table !== "undefined") {
        task_table.draw();
    }
});

$("#task_form").on("submit", function (e) {
    e.preventDefault();
    updateTextareasFromEditors();

    $("#commitments_input").val(JSON.stringify(commitments));
    $("#commitments_delete_input").val(JSON.stringify(commitmentsToDelete));

    $("#deliverables_input").val(JSON.stringify(deliverables));
    $("#deliverables_delete_input").val(JSON.stringify(deliverablesToDelete));

    let form = $(this);

    let url = form.attr("action");
    // alert(url);
    // console.log("Commitments:", commitments);
    // console.log("Deliverables:", deliverables);

    let submitBtn = form.find("button[type='submit']");
    submitBtn.prop("disabled", true);

    form.ajaxSubmit({
        url: url,
        type: "POST", // ✅ ALWAYS POST
        dataType: "json",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (res) {
            submitBtn.prop("disabled", false);
            if (res.success) {
                showToastr("success", res.message);
                setTimeout(() => (window.location.href = index_path), 1500);
            } else {
                showToastr("error", res.message);
            }
        },
        error: function (result) {
            submitBtn.prop("disabled", false);
            $("[id$='_error']").empty();
            showToastr("error", result.responseJSON.message);
            $.each(result.responseJSON.errors, function (k, v) {
                $("#" + k + "_error").text(v);
            });
        },
    });
});

$(document).on("click", ".delete-data", function () {
    event.preventDefault();
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
                    showToastr("success", result.message);
                },
                error: function (result) {
                    showToastr("error", result.responseJSON.message);
                },
            });
        }
    });
});
