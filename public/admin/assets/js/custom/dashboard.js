$(document).ready(function () {
    $(document).on("click", ".calendar-add-btn", function () {
        const date = $(this).data("date");
        const url = $(this).data("url");

        $("#sub_modal_show_html").html(
            '<div class="text-center p-5"><i class="bi bi-hourglass-split fs-1"></i><p>Loading...</p></div>',
        );

        $.ajax({
            url: url,
            method: "GET",
            dataType: "json",
            success: function (response) {
                $("#sub_modal_show_html").html(response.html);

                const datetimeInput = $("#consulting_datetime");
                if (datetimeInput.length) {
                    datetimeInput.val(date + "T09:00");
                }

                if ($.fn.select2) {
                    $(".select2").select2({
                        placeholder: "Select...",
                        width: "100%",
                        dropdownParent: $("#consultingModal"),
                        allowClear: true,
                    });
                }

                $("#consultingModal").modal("show");
            },
            error: function () {
                showToastr("error", "Error loading form");
            },
        });
    });

    // $(document).on("submit", "#consulting_form", function (e) {
    //     e.preventDefault();

    //     let form = $("#consulting_form");
    //     let url = form.attr("action");
    //     let method = form.find('input[name="_method"]').length ? "PUT" : "POST";

    //     $("[id$='_error']").empty();

    //     let formData = new FormData(form[0]);

    //     formData.append("_token", csrf_token);

    //     $.ajax({
    //         url: url,
    //         type: method,
    //         data: formData,
    //         processData: false,
    //         contentType: false,
    //         dataType: "json",
    //         success: function (result) {
    //             if (result.success) {
    //                 $("#consultingModal").modal("hide");

    //                 let message = result.message;
    //                 if (result.task_id) {
    //                     message +=
    //                         " (Task ID: " +
    //                         result.task_id +
    //                         " created/updated)";
    //                 }
    //                 showToastr("success", message);

    //                 setTimeout(function () {
    //                     window.location.reload();
    //                 }, 1500);
    //             }
    //         },
    //         error: function (xhr) {
    //             if (xhr.status === 422) {
    //                 const errors = xhr.responseJSON.errors;
    //                 $.each(errors, function (k, v) {
    //                     var id_arr = k.split(".");
    //                     $("#consulting_form")
    //                         .find("#" + id_arr[0] + "_error")
    //                         .text(v);
    //                 });
    //                 showToastr("error", "Please fix the validation errors");
    //             } else {
    //                 showToastr(
    //                     "error",
    //                     xhr.responseJSON.message || "Something went wrong!",
    //                 );
    //             }
    //         },
    //     });

    //     return false;
    // });

    $(document).on("click", "#consulting_form_button", function () {
        let form = $("#consulting_form");
        let url = $(this).attr("data-url");
        let method = form.find("input[name='_method']").length ? "PUT" : "POST"; // Detect if it's an update
        $("#consulting_form").ajaxSubmit({
            url: url,
            type: method,
            dataType: "json",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            beforeSubmit: function () {},
            success: function (result) {
                $("#consultingModal").modal("hide");
                showToastr("success", result.message);
                setTimeout(function () {
                    window.location.reload();
                }, 1500);
            },
            error: function (result) {
                $("[id$='_error']").empty();
                showToastr("error", result.responseJSON.message);
                $.each(result.responseJSON.errors, function (k, v) {
                    var id_arr = k.split(".");
                    $("body")
                        .find("#consulting_form")
                        .find("#" + id_arr[0] + "_error")
                        .text(v);
                });
            },
        });
        return false;
    });

    $(document).on("hidden.bs.modal", "#consultingModal", function () {
        $("[id$='_error']").empty();
    });

    $(document).on("click", ".calendar-edit-btn", function () {
        $.ajax({
            url: $(this).attr("data-url"),
            type: "GET",
            dataType: "json",
            success: function (data) {
                // alert(data.html);
                $("#sub_modal_show_html").html(data.html);
                $("#consultingModal").modal("show");
                $(".select2").select2({
                    placeholder: "Select...",
                    width: "100%",
                    dropdownParent: $("#consultingModal"),
                    // allowClear: true,
                    // closeOnSelect: false, // keep dropdown open for multiple selections
                });
            },
        });
        return false;
    });

    $(document).on("click", ".calendar-delete-btn", function () {
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
                        showToastr("success", result.message);
                        setTimeout(function () {
                            window.location.reload();
                        }, 1500);
                    },
                    error: function (result) {
                        showToastr("error", result.responseJSON.message);
                    },
                });
            }
        });
    });
});

let taskModalTable = null;
let currentClientObjectiveId = null;

function loadTaskModalTable(clientObjectiveId) {
    currentClientObjectiveId = clientObjectiveId;

    if (taskModalTable) {
        taskModalTable.ajax.reload(null, true);
        return;
    }

    taskModalTable = $("#task_modal_table").DataTable({
        order: [[0, "desc"]],
        autoWidth: false,
        processing: true,
        serverSide: true,
        serverMethod: "GET",
        pageLength: 25,
        lengthMenu: [
            [25, 100, 200, 250],
            [25, 100, 200, 250],
        ],

        ajax: {
            url: $("#task_modal_table").attr("data-url"),
            data: function (d) {
                d.client_objective_id = currentClientObjectiveId;
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

            // 2️⃣ title
            {
                data: "title",
                render: function (data) {
                    return data ? data : "-";
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

            // 3️⃣ Due Date
            {
                data: "task_due_date",
                render: function (data) {
                    return data ? moment(data).format("DD-MM-YYYY HH:mm") : "-";
                },
            },

            // 4️⃣ Status
            {
                data: "status_manager",
                render: function (v, t, o) {
                    if (o.status_manager) {
                        let name = o.status_manager.name;
                        let color = o.status_manager.color_name || "gray";

                        return `
                            <span style="
                                background:${color};
                                color:#fff;
                                padding:2px 6px;
                                border-radius:4px;
                                font-size:11px;
                            ">
                                ${name}
                            </span>`;
                    }
                    return "N/A";
                },
            },

            // 5️⃣ Action
            {
                data: "id",
                orderable: false,
                searchable: false,
                className: "text-center",
                render: function (id) {
                    let pdf_path_set = pdf_path.replace(":task", id);
                    let edit_path_set = edit_path.replace(":task", id);
                    let delete_path_set = delete_path.replace(":task", id);

                    let editDisabled = window.canEditTask
                        ? ""
                        : "style='pointer-events:none;opacity:0.4;'";

                    let deleteDisabled = window.canDeleteTask
                        ? ""
                        : "style='pointer-events:none;opacity:0.4;'";

                    return `
                        <a href="${pdf_path_set}" target="_blank" title="PDF">
                            <i class="fas fa-file-pdf p-1 text-secondary"></i>
                        </a>

                        <a href="${edit_path_set}"
                           class="open-modal"
                           title="Edit"
                           ${editDisabled}>
                            <i class="fas fa-pen p-1 text-primary"></i>
                        </a>

                        <a href="javascript:void(0);"
                           class="delete-data"
                           data-url="${delete_path_set}"
                           title="Delete"
                           ${deleteDisabled}>
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
}

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
                    taskModalTable.draw();
                    showToastr("success", result.message);
                },
                error: function (result) {
                    showToastr("error", result.responseJSON.message);
                },
            });
        }
    });
});

$(document).on("click", ".open-meeting-modal", function (e) {
    e.preventDefault();

    currentClientObjectiveId = $(this).data("client-objective-id");
    const clientName = $(this).data("client-name");
    const objectiveName = $(this).data("objective-name");

    $("#taskModalTitle").html(`
        Meetings
        <span class="text-muted fw-normal">
            — ${clientName}${objectiveName ? " / " + objectiveName : ""}
        </span>
    `);

    const $addBtn = $("#addMeetingBtn");

    // store base url only once
    if (!$addBtn.data("base-url")) {
        $addBtn.data("base-url", $addBtn.attr("href"));
    }

    // append client_objective_id
    $addBtn.attr(
        "href",
        $addBtn.data("base-url") +
            "?client_objective_id=" +
            currentClientObjectiveId,
    );

    const modal = new bootstrap.Modal("#taskModal", {
        backdrop: "static",
        keyboard: false,
    });
    modal.show();

    // ✅ this is all you need
    loadTaskModalTable(currentClientObjectiveId);
});

// $(document).on("click", ".calendar-dot", function (e) {
//     e.stopPropagation();
// });

$(document).on("click", ".calendar-day", function () {
    const date = $(this).data("date");
    if (!date) return;

    $.ajax({
        url: routeDayConsultings,
        type: "GET",
        data: { date },
        success: function (html) {
            $("#modal_show_html").html(html);
            $("#dayConsultingModal").modal("show");
        },
        error: function () {
            $("#dayConsultingBody").html(
                '<div class="text-danger text-center py-4">Failed to load consultings</div>',
            );
        },
    });
});

$(document).ready(function () {
    let activeTabId = localStorage.getItem("dashboardActiveTab");

    if (activeTabId) {
        let tabBtn = document.getElementById(activeTabId);

        if (tabBtn) {
            new bootstrap.Tab(tabBtn).show();
        }

        localStorage.removeItem("dashboardActiveTab");
    }
});

$(document).on("click", ".mark-completed", function () {
    let activeTabId = $(".nav-tabs .nav-link.active").attr("id");

    $.post(
        routeUpdateStatue,
        {
            _token: csrf_token,
            type: $(this).data("type"), // followup | task
            id: $(this).data("id"),
            status: "completed",
        },
        function () {
            // remember active tab
            localStorage.setItem("dashboardActiveTab", activeTabId);

            // reload full page
            location.reload();
        },
    );
});

// Reset Filters Button
$(document).on("click", "#resetFilters", function () {
    // Clear all filter dropdowns
    $(".applyFilters").val("").trigger("change");
});
