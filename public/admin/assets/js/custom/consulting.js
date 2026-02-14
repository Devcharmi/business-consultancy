var consulting_table = $(".table-list").DataTable({
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
        url: $("#consulting_table").attr("data-url"),
        data: function (d) {
            d.dateRange = $("#dateRange").val();
            d.filterClient = $("#filterClient").val();
            d.filterObjective = $("#filterObjective").val();
            d.filterExpertise = $("#filterExpertise").val();
            d.filterFocusArea = $("#filterFocusArea").val();
        },
    },
    columns: [
        // 1️⃣ Sr No
        // {
        //     data: "id",
        //     render: function (data, type, row, meta) {
        //         return meta.row + meta.settings._iDisplayStart + 1;
        //     },
        // },
        // 7️⃣ Action
        {
            data: "id",
            orderable: false,
            searchable: false,
            className: "text-center",
            render: function (id, type, row) {
                let edit_path_set = edit_path.replace(":consulting", id);
                let delete_path_set = delete_path.replace(":consulting", id);

                let editDisabled = window.canEditTask
                    ? ""
                    : "style='pointer-events:none;opacity:0.4;' disabled";
                let deleteDisabled = window.canDeleteTask
                    ? ""
                    : "style='pointer-events:none;opacity:0.4;' disabled";

                return `
                 <button class="btn btn-xs btn-outline-success open-meeting-modal"
                                    data-consulting-id="${id}"
                                    data-client-objective-id="${row.client_objective_id}"
                                    data-client-name="${row.client_objective.client.client_name}"
                                    data-objective-name="${row.client_objective.objective_manager.name}"
                                    title="CVR">CVR
                                </button>
                   <a href="javascript:void(0);" data-url="${edit_path_set}"
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
        // 2️⃣ Client Name
        // {
        //     data: "clinet",
        //     render: function (data, type, row) {
        //         return data && data.client ? data.client.client_name : "-";
        //     },
        // },
        {
            data: "client_objective",
            render: function (data, type, row) {
                return data && data.client ? data.client.client_name : "-";
            },
        },

        // 3️⃣ Objective
        // {
        //     data: "objective_manager",
        //     render: function (data, type, row) {
        //         return data && data.objective_manager
        //             ? data.objective_manager.name
        //             : "-";
        //     },
        // },
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

        // 5️⃣ Focus Area
        {
            data: "focus_area_manager",
            render: function (data) {
                return data ? data.name : "-";
            },
        },

        // 6️⃣ Date
        {
            data: "consulting_date",
            render: function (data) {
                return data ? moment(data).format("DD-MM-YYYY") : "-";
            },
        },
        // 7️⃣ Start Time
        {
            data: "start_time",
            render: function (data) {
                return data ? moment(data, "HH:mm:ss").format("hh:mm A") : "-";
            },
        },
        // 8️⃣ End Time
        {
            data: "end_time",
            render: function (data) {
                return data ? moment(data, "HH:mm:ss").format("hh:mm A") : "-";
            },
        },
    ],
    language: {
        searchPlaceholder: "Search...",
        sSearch: "",
        lengthMenu: "_MENU_&nbsp; items/page",
    },
});

// APPLY FILTERS (Modal Apply Button)
$(document).on("click", "#applyFiltersBtn", function () {
    if (typeof consulting_table !== "undefined") {
        consulting_table.draw();
    }

    // Close modal
    $("#filterModal").modal("hide");
});

// RESET FILTERS
$(document).on("click", "#resetFilters", function () {
    // Clear all dropdowns
    $(".applyFilters").val("").trigger("change");

    if (typeof consulting_table !== "undefined") {
        consulting_table.draw();
    }
});

// $(document).on("change", ".applyFilters", function () {
//     consulting_table.draw();
// });

// // Reset Filters Button
// $(document).on("click", "#resetFilters", function () {
//     // Clear all filter dropdowns
//     $(".applyFilters").val("").trigger("change");

//     // ✅ If you're using DataTables with AJAX filtering:
//     if (typeof consulting_table !== "undefined") {
//         consulting_table.draw();
//     }
// });

$(document).on("click", ".open-modal", function () {
    $.ajax({
        url: $(this).attr("data-url"),
        type: "GET",
        dataType: "json",
        success: function (data) {
            // alert(data.html);
            $("#modal_show_html").html(data.html);
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
            consulting_table.draw();
            $("#consultingModal").modal("hide");
            showToastr("success", result.message);
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
                    consulting_table.draw();
                    showToastr("success", result.message);
                },
                error: function (result) {
                    showToastr("error", result.responseJSON.message);
                },
            });
        }
    });
});

$(document).ready(function () {
    $("#importConsultingForm").on("submit", function (e) {
        e.preventDefault();

        let formData = new FormData(this);
        let $btn = $("#importSubmitBtn");

        // Disable button & show spinner
        $btn.prop("disabled", true).html(
            `<span class="spinner-border spinner-border-sm me-2"></span> Importing...`,
        );

        // Reset UI
        $("#importLoader").removeClass("d-none");
        $("#importSuccess").addClass("d-none").text("");
        $("#importErrorContainer").addClass("d-none");
        $("#importErrorTableBody").html("");

        $.ajax({
            url: $btn.data("url"),
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                $("#importLoader").addClass("d-none");
                $btn.prop("disabled", false).html("Import");

                // Show errors if any
                if (response.errors && response.errors.length > 0) {
                    $("#importErrorContainer").removeClass("d-none");
                    $.each(response.errors, function (index, error) {
                        $("#importErrorTableBody").append(`
                            <tr>
                                <td>${error.row}</td>
                                <td>${error.message}</td>
                            </tr>
                        `);
                    });
                } else {
                    $("#importErrorContainer").addClass("d-none");
                }

                // Show success message if rows imported
                if (response.success && response.importedCount > 0) {
                    $("#importSuccess")
                        .removeClass("d-none")
                        .text(response.message);
                    showToastr("success", response.message);
                    consulting_table.draw(); // refresh table immediately
                } else if (
                    !response.success &&
                    (!response.errors || response.errors.length === 0)
                ) {
                    showToastr("error", response.message || "Import failed.");
                } else {
                    $("#importSuccess").addClass("d-none");
                }
            },
            error: function (xhr) {
                $("#importLoader").addClass("d-none");
                $btn.prop("disabled", false).html("Import");

                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    showToastr(
                        "error",
                        Object.values(xhr.responseJSON.errors)[0],
                    );
                } else {
                    showToastr("error", "Something went wrong.");
                }
            },
        });
    });
});

// Refresh DataTable when import modal is closed
$("#importConsultingModal").on("hidden.bs.modal", function () {
    if (typeof consulting_table !== "undefined") {
        consulting_table.draw(); // redraw table to fetch new data
    }

    // Optional: reset form & UI
    $("#importConsultingForm")[0].reset();
    $("#importSuccess").addClass("d-none").text("");
    $("#importErrorContainer").addClass("d-none");
    $("#importErrorTableBody").html("");
});

$(document).ready(function () {
    // $("#importConsultingForm").on("submit", function (e) {
    //     e.preventDefault();
    //     let formData = new FormData(this);
    //     let $btn = $("#importSubmitBtn");
    //     // Disable button & show loader
    //     $btn.prop("disabled", true).html(
    //         `<span class="spinner-border spinner-border-sm me-2"></span> Importing...`,
    //     );
    //     $("#importLoader").show();
    //     $("#importSuccess").hide();
    //     $("#importErrorContainer").hide();
    //     $("#importErrorTableBody").html("");
    //     $.ajax({
    //         url: $btn.data("url"),
    //         type: "POST",
    //         data: formData,
    //         processData: false,
    //         contentType: false,
    //         success: function (response) {
    //             $("#importLoader").hide();
    //             $btn.prop("disabled", false).html("Import");
    //             // Clear previous messages
    //             $("#importSuccess").hide();
    //             $("#importErrorContainer").hide();
    //             $("#importErrorTableBody").html("");
    //             // Show error table if there are errors
    //             if (response.errors && response.errors.length > 0) {
    //                 $("#importErrorContainer").show();
    //                 $.each(response.errors, function (index, error) {
    //                     $("#importErrorTableBody").append(`
    //                         <tr>
    //                             <td>${error.row}</td>
    //                             <td>${error.message}</td>
    //                         </tr>
    //                     `);
    //                 });
    //             }
    //             // Show success message if any rows imported
    //             if (response.success && response.importedCount > 0) {
    //                 $("#importSuccess")
    //                     .text(response.message)
    //                     .removeClass("d-none");
    //             }
    //             // Optional: show toast for overall result
    //             if (response.success && response.importedCount > 0) {
    //                 showToastr("success", response.message);
    //             } else if (
    //                 !response.success &&
    //                 response.errors &&
    //                 response.errors.length > 0
    //             ) {
    //                 showToastr(
    //                     "error",
    //                     "Some rows failed to import. Check the table for details.",
    //                 );
    //             }
    //         },
    //         error: function (xhr) {
    //             $("#importLoader").hide();
    //             $btn.prop("disabled", false).html("Import");
    //             if (xhr.responseJSON && xhr.responseJSON.errors) {
    //                 let firstError = Object.values(xhr.responseJSON.errors)[0];
    //                 showToastr("error", firstError);
    //             } else {
    //                 showToastr("error", "Something went wrong.");
    //             }
    //         },
    //     });
    // });
    // $("#importConsultingForm").on("submit", function (e) {
    //     e.preventDefault();
    //     let formData = new FormData(this);
    //     let $btn = $("#importSubmitBtn");
    //     // Disable button
    //     $btn.prop("disabled", true).html(
    //         `<span class="spinner-border spinner-border-sm me-2"></span> Importing...`,
    //     );
    //     // Reset UI
    //     $("#importLoader").show();
    //     $("#importSuccess").hide();
    //     $("#importErrorContainer").hide();
    //     $("#importErrorTableBody").html("");
    //     $.ajax({
    //         url: $("#importSubmitBtn").data("url"),
    //         type: "POST",
    //         data: formData,
    //         processData: false,
    //         contentType: false,
    //         success: function (response) {
    //             $("#importLoader").hide();
    //             $btn.prop("disabled", false).html("Import");
    //             // Always reset
    //             $("#importSuccess").hide();
    //             $("#importErrorContainer").hide();
    //             $("#importErrorTableBody").html("");
    //             // Show errors if any
    //             if (response.errors && response.errors.length > 0) {
    //                 $("#importErrorContainer").show();
    //                 $.each(response.errors, function (index, error) {
    //                     $("#importErrorTableBody").append(`
    //             <tr>
    //                 <td>${error.row}</td>
    //                 <td>${error.message}</td>
    //             </tr>
    //         `);
    //                 });
    //             }
    //             // Show success only if some rows were imported
    //             if (response.success && response.importedCount > 0) {
    //                 $("#importSuccess")
    //                     .text(response.message)
    //                     .removeClass("d-none");
    //                 showToastr("success", response.message);
    //             } else if (response.errors && response.errors.length > 0) {
    //                 showToastr(
    //                     "error",
    //                     response.message || "Some rows failed to import.",
    //                 );
    //             } else {
    //                 showToastr("error", response.message || "Import failed.");
    //             }
    //         },
    //         error: function (xhr) {
    //             $("#importLoader").hide();
    //             // Enable button again
    //             $btn.prop("disabled", false).html("Import");
    //             if (xhr.responseJSON.errors) {
    //                 showToastr(
    //                     "error",
    //                     Object.values(xhr.responseJSON.errors)[0],
    //                 );
    //             } else {
    //                 showToastr("error", "Something went wrong.");
    //             }
    //         },
    //     });
    // });
});
