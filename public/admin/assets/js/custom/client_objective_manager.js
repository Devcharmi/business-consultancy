var client_objective_table = $(".table-list").DataTable({
    order: [[0, "desc"]],
    autoWidth: false,
    processing: true,
    serverSide: true,
    serverMethod: "GET",
    lengthMenu: [
        [25, 100, 200, 250],
        [25, 100, 200, 250],
    ],
    ajax: {
        url: $("#client_objective_table").attr("data-url"),
    },
    columns: [
        {
            data: "id",
            mRender: function (v, t, o, meta) {
                return meta.row + meta.settings._iDisplayStart + 1;
            },
        },
        {
            data: "client",
            mRender: function (v, t, o) {
                return o["client"].client_name;
            },
        },
        {
            data: "objective_manager",
            mRender: function (v, t, o) {
                return o["objective_manager"].name;
            },
        },
        {
            data: "guard_name",
            sClass: "text-center",
            mRender: function (v, t, o) {
                var id = o["id"];

                var edit_path_set = edit_path;
                edit_path_set = edit_path_set.replace(
                    ":client_objective_manager",
                    id,
                );

                var delete_path_set = delete_path;
                delete_path_set = delete_path_set.replace(
                    ":client_objective_manager",
                    id,
                );

                let editDisabled = window.canEditTask
                    ? ""
                    : "style='pointer-events:none;opacity:0.4;' disabled";
                let deleteDisabled = window.canDeleteTask
                    ? ""
                    : "style='pointer-events:none;opacity:0.4;' disabled";

                var html = `<a href="javascript:void(0);" data-id="${id}" title="View Details" data-tool-tips="View Details" class="view-details">
                                   <i class="fas fa-eye p-1 text-info"></i>
                                </a>`;
                html += `<a href="javascript:void(0);" data-url="${edit_path_set}" title="Edit" data-tool-tips="Edit" class="open-modal" ${editDisabled}>
                                   <i class="fas fa-pen p-1 text-primary"></i>
                                </a>`;
                html += `<a href="javascript:void(0);" data-url="${delete_path_set}" title="Delete" data-tool-tips="Delete" class="delete-data" ${deleteDisabled}>
                    <i class="fas fa-trash p-1 text-danger"></i>
                                </a>`;
                return html;
            },
        },
    ],
    createdRow: function (row, data, dataIndex) {
        $(row).attr("data-id", data.id);
    },
    language: {
        searchPlaceholder: "Search...",
        sSearch: "",
        lengthMenu: "_MENU_&nbsp; items/page",
    },
});

// Handle view details (accordion)
// $(document).on("click", ".view-details", function () {
//     var id = $(this).data("id");
//     var row = $(this).closest("tr");
//     var nextRow = row.next("tr.details-row");
//     let expertiseId = $(this).data("expertise-id");

//     // If already expanded, collapse it
//     if (nextRow.length && nextRow.is(":visible")) {
//         nextRow.hide();
//         $(this).find("i").removeClass("fa-eye-slash").addClass("fa-eye");
//         return false;
//     }

//     // Hide any other open details
//     $("tr.details-row").hide();
//     $(".view-details i").removeClass("fa-eye-slash").addClass("fa-eye");

//     // If details already loaded, show them
//     if (nextRow.length) {
//         nextRow.show();
//         $(this).find("i").removeClass("fa-eye").addClass("fa-eye-slash");
//     } else {
//         // Load details via AJAX
//         var detailsUrl = objective_details_path.replace(":id", id);

//         $.ajax({
//             url: detailsUrl,
//             type: "GET",
//             data: {
//                 expertise_manager_id: expertiseId,
//             },
//             dataType: "json",
//             beforeSend: function () {
//                 $(this).prop("disabled", true);
//             },
//             success: function (data) {
//                 if (data.success) {
//                     // Add details row
//                     var detailsRow = $(
//                         '<tr class="details-row"><td colspan="4"></td></tr>',
//                     );
//                     detailsRow.find("td").html(data.html);
//                     row.after(detailsRow);
                  
//                     // Change icon
//                     $('.view-details[data-id="' + id + '"] i')
//                         .removeClass("fa-eye")
//                         .addClass("fa-eye-slash");
//                 }
//             },
//             error: function () {
//                 showToastr("error", "Error loading details");
//             },
//             complete: function () {
//                 $(this).prop("disabled", false);
//             },
//         });
//     }
//     return false;
// });
$(document).on("click", ".view-details", function () {
    let btn = $(this);
    let objectiveId = btn.data("id");
    let expertiseId = btn.data("expertise-id");
    let row = btn.closest("tr");
    let nextRow = row.next("tr.details-row");

    // Collapse
    if (nextRow.length && nextRow.is(":visible")) {
        nextRow.hide();
        btn.find("i").removeClass("fa-eye-slash").addClass("fa-eye");
        return false;
    }

    // Close others
    $("tr.details-row").hide();
    $(".view-details i").removeClass("fa-eye-slash").addClass("fa-eye");

    // Already loaded
    if (nextRow.length) {
        nextRow.show();
        btn.find("i").removeClass("fa-eye").addClass("fa-eye-slash");
        return false;
    }

    // First time load
    loadObjectiveDetails(objectiveId, expertiseId, row);
    return false;
});

$(document).on("click", ".expertise-tab-btn", function () {
    let btn = $(this);
    let expertiseId = btn.data("expertise-id");
    let objectiveId = btn.data("objective-id");

    let row = $('.view-details[data-id="' + objectiveId + '"]')
        .closest("tr");

    loadObjectiveDetails(objectiveId, expertiseId, row);
});

function loadObjectiveDetails(objectiveId, expertiseId, row) {
    let detailsUrl = objective_details_path.replace(":id", objectiveId);

    $.ajax({
        url: detailsUrl,
        type: "GET",
        data: {
            expertise_manager_id: expertiseId
        },
        dataType: "json",
        success: function (res) {
            if (!res.success) return;

            let nextRow = row.next("tr.details-row");

            if (!nextRow.length) {
                nextRow = $('<tr class="details-row"><td colspan="4"></td></tr>');
                row.after(nextRow);
            }

            nextRow.find("td").html(res.html).show();

            $('.view-details[data-id="' + objectiveId + '"] i')
                .removeClass("fa-eye")
                .addClass("fa-eye-slash");
        }
    });
}

$(document).on("click", ".open-modal", function () {
    $.ajax({
        url: $(this).attr("data-url"),
        type: "GET",
        dataType: "json",
        success: function (data) {
            $("#modal_show_html").html(data.html);
            $("#modalForm").modal("show");
        },
    });
    return false;
});

$(document).on("click", "#client_objective_form_button", function () {
    let form = $("#client_objective_form");
    let url = $(this).attr("data-url");
    let method = form.find("input[name='_method']").length ? "PUT" : "POST"; // Detect if it's an update
    $("#client_objective_form").ajaxSubmit({
        url: url,
        type: method,
        dataType: "json",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        beforeSubmit: function () {},
        success: function (result) {
            client_objective_table.draw();
            $("#modalForm").modal("hide");
            showToastr("success", result.message);
        },
        error: function (result) {
            $("[id$='_error']").empty();
            showToastr("error", result.responseJSON.message);
            $.each(result.responseJSON.errors, function (k, v) {
                var id_arr = k.split(".");
                $("body")
                    .find("#client_objective_form")
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
                    client_objective_table.draw();
                    showToastr("success", result.message);
                },
                error: function (result) {
                    showToastr("error", result.responseJSON.message);
                },
            });
        }
    });
});
