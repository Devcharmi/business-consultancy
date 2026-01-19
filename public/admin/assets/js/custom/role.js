var role_table = $(".table-list").DataTable({
    order: [[0, "desc"]],
    autoWidth: false,
    processing: true,
    serverSide: true,
    serverMethod: "GET",
    lengthMenu: [
        [10, 100, 200, 250],
        [10, 100, 200, 250],
    ],
    ajax: {
        url: $("#role_table").attr("data-url"),
    },
    columns: [
        {
            data: "id",
            mRender: function (v, t, o, meta) {
                return meta.row + meta.settings._iDisplayStart + 1;
            },
        },
        { data: "name" },
        {
            data: "guard_name",
            sClass: "text-center",
            mRender: function (v, t, o) {
                var id = o["id"];
                var permission_path_set = permission_path;
                permission_path_set = permission_path_set.replace(":role", id);

                var edit_path_set = edit_path;
                edit_path_set = edit_path_set.replace(":role", id);

                var delete_path_set = delete_path;
                delete_path_set = delete_path_set.replace(":role", id);

                let editDisabled = window.canEditTask
                    ? ""
                    : "style='pointer-events:none;opacity:0.4;' disabled";
                let deleteDisabled = window.canDeleteTask
                    ? ""
                    : "style='pointer-events:none;opacity:0.4;' disabled";

                var html = `<a href="javascript:void(0);" data-url="${permission_path_set}" title="Give Permission" data-tool-tips="Give Permission" class="btns-success permission-modal">
                                       <i class="fas fa-cog p-1"></i>
                                    </a>`;

                html += `<a href="javascript:void(0);" data-url="${edit_path_set}" title="Edit" data-tool-tips="Edit" class="open-modal"  ${editDisabled}>
                                       <i class="fas fa-pen p-1 text-primary"></i>
                                    </a>`;
                html += `<a href="javascript:void(0);" data-url="${delete_path_set}" title="Delete" data-tool-tips="Delete" class="delete-data"  ${deleteDisabled}>
                    <i class="fas fa-trash p-1 text-danger"></i>
                                    </a>`;
                return html;
            },
        },
    ],
    language: {
        searchPlaceholder: "Search...",
        sSearch: "",
        lengthMenu: "_MENU_&nbsp; items/page",
    },
});

$(document).on("click", ".permission-modal", function () {
    $.ajax({
        url: $(this).attr("data-url"),
        type: "GET",
        dataType: "json",
        success: function (data) {
            // alert(data.html);
            $("#modal_show_html").html(data.html);
            $("#rolePermissionModal").modal("show");
        },
    });
    return false;
});

$(document).on("click", ".open-modal", function () {
    $.ajax({
        url: $(this).attr("data-url"),
        type: "GET",
        dataType: "json",
        success: function (data) {
            // alert(data.html);
            $("#modal_show_html").html(data.html);
            $("#modalForm").modal("show");
        },
    });
    return false;
});

$(document).on("click", "#role_form_button", function () {
    let form = $("#role_form");
    let url = $(this).attr("data-url");
    let method = form.find("input[name='_method']").length ? "PUT" : "POST"; // Detect if it's an update
    $("#role_form").ajaxSubmit({
        url: url,
        type: method,
        dataType: "json",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        beforeSubmit: function () {},
        success: function (result) {
            role_table.draw();
            $("#modalForm").modal("hide");
            showToastr("success", result.message);
        },
        error: function (result) {
            $("[id$='_error']").empty();
            showToastr("error", result.responseJSON.message);
            $.each(result.responseJSON.errors, function (k, v) {
                var id_arr = k.split(".");
                $("body")
                    .find("#role_form")
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
                    role_table.draw();
                    showToastr("success", result.message);
                },
                error: function (result) {
                    showToastr("error", result.responseJSON.message);
                },
            });
        }
    });
});

// MASTER SELECT ALL (delegated)
$(document).on("change", "#masterSelectAll", function () {
    let checked = $(this).is(":checked");
    $(".permission-checkbox, .rowSelect, .columnSelect").prop(
        "checked",
        checked
    );
});

// ROW SELECT ALL
$(document).on("change", ".rowSelect", function () {
    let row = $(this).closest("tr");
    row.find(".permission-checkbox").prop("checked", $(this).is(":checked"));
});

// COLUMN SELECT ALL
$(document).on("change", ".columnSelect", function () {
    let type = $(this).data("column");
    $(".checkbox-" + type).prop("checked", $(this).is(":checked"));
});

// AUTO SELECT ROW
$(document).on("change", ".permission-checkbox", function () {
    let row = $(this).closest("tr");
    let total = row.find(".permission-checkbox").length;
    let checked = row.find(".permission-checkbox:checked").length;
    row.find(".rowSelect").prop("checked", total === checked);
});

// SUBMIT MODAL FORM
$(document).on("click", "#update_permission_button", function () {
    let updateUrl = $("#update_permission_button").attr("data-url");

    Swal.fire({
        title: "Apply to all users?",
        text: "Do you want to update permissions for every user having this role?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Yes, update all users",
        cancelButtonText: "No, only this role",
    }).then((result) => {
        let applyToUsers = result.isConfirmed ? 1 : 0;

        $("#permission_form").ajaxSubmit({
            url: updateUrl,
            type: "PUT",
            data: { apply_to_users: applyToUsers },
            dataType: "json",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (result) {
                role_table.draw();
                $("#modalForm").modal("hide");
                showToastr("success", result.message);
            },
            error: function (result) {
                showToastr("error", result.responseJSON.message);
            },
        });
    });

    return false;
});
