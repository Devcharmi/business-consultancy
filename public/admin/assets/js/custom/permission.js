var permission_table = $("#permission_table").DataTable({
    order: [[0, "desc"]],
    autoWidth: false,
    processing: true,
    serverSide: true,
    serverMethod: "GET",
    lengthMenu: [
        [50, 100, 200, 250],
        [50, 100, 200, 250],
    ],
    ajax: {
        url: $("#permission_table").attr("data-url"),
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
                var edit_path_set = edit_path;
                edit_path_set = edit_path_set.replace(":permission", id);

                var delete_path_set = delete_path;
                delete_path_set = delete_path_set.replace(":permission", id);

                let editDisabled = window.canEditTask
                    ? ""
                    : "style='pointer-events:none;opacity:0.4;' disabled";
                let deleteDisabled = window.canDeleteTask
                    ? ""
                    : "style='pointer-events:none;opacity:0.4;' disabled";

                var permission_html = `<a href="javascript:void(0);" data-url="${edit_path_set}" title="Edit" data-tool-tips="Edit" class="open-modal btns-primary" ${editDisabled}>
                                       <i class="fas fa-pen p-1 text-primary"></i>
                                    </a>`;
                permission_html =
                    permission_html +
                    `<a href="javascript:void(0);" data-url="${delete_path_set}" title="Delete" data-tool-tips="Delete" class="delete-data btns-danger" ${deleteDisabled}>
                    <i class="fas fa-trash p-1 text-danger"></i>
                                    </a>`;
                return permission_html;
            },
        },
    ],
    language: {
        searchPlaceholder: "Search...",
        sSearch: "",
        lengthMenu: "_MENU_&nbsp; items/page",
    },
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

$(document).on("click", "#permission_form_button", function () {
    // var check_validation = $("#permission_form").parsley().validate();
    // if (check_validation) {
    $("#permission_form").ajaxSubmit({
        url: $("#permission_form_button").attr("data-url"),
        type: "POST",
        dataType: "json",
        header: {
            Accept: "application/json",
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        beforeSubmit: function () {},
        success: function (result) {
            permission_table.draw();
            $("#modalForm").modal("hide");
            showToastr("success", result.message);
        },
        error: function (result) {
            $("[id$='_error']").empty();
            showToastr("error", result.responseJSON.message);
            $.each(result.responseJSON.errors, function (k, v) {
                var id_arr = k.split(".");
                $("body")
                    .find("#permission_form")
                    .find("#" + id_arr[0] + "_error")
                    .text(v);
            });
        },
    });
    // }
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
                    permission_table.draw();
                    showToastr("success", result.message);
                },
                error: function (result) {
                    showToastr("error", result.responseJSON.message);
                },
            });
        }
    });
});
