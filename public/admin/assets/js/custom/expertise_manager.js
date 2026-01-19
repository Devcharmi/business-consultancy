var expertise_table = $(".table-list").DataTable({
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
        url: $("#expertise_table").attr("data-url"),
    },
    columns: [
        {
            data: "id",
            mRender: function (v, t, o, meta) {
                return meta.row + meta.settings._iDisplayStart + 1;
            },
        },
        { data: "name" },
        { data: "color_name" },
        {
            data: "status",
            sClass: "text-center",
            mRender: function (v, t, o) {
                var status = o["status"];
                if (status == 1) {
                    return '<span class="badge bg-success p-2">Active</span>';
                } else {
                    return '<span class="badge bg-danger p-2">Inactive</span>';
                }
            },
        },
        {
            data: "guard_name",
            sClass: "text-center",
            mRender: function (v, t, o) {
                var id = o["id"];

                var edit_path_set = edit_path;
                edit_path_set = edit_path_set.replace(":expertise_manager", id);

                var delete_path_set = delete_path;
                delete_path_set = delete_path_set.replace(
                    ":expertise_manager",
                    id
                );

                let editDisabled = window.canEditTask
                    ? ""
                    : "style='pointer-events:none;opacity:0.4;' disabled";
                let deleteDisabled = window.canDeleteTask
                    ? ""
                    : "style='pointer-events:none;opacity:0.4;' disabled";

                var html = `<a href="javascript:void(0);" data-url="${edit_path_set}" title="Edit" data-tool-tips="Edit" class="open-modal" ${editDisabled}>
                                       <i class="fas fa-pen p-1 text-primary"></i>
                                    </a>`;
                html += `<a href="javascript:void(0);" data-url="${delete_path_set}" title="Delete" data-tool-tips="Delete" class="delete-data" ${deleteDisabled}>
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

$(document).on("click", "#expertise_form_button", function () {
    let form = $("#expertise_form");
    let url = $(this).attr("data-url");
    let method = form.find("input[name='_method']").length ? "PUT" : "POST"; // Detect if it's an update
    $("#expertise_form").ajaxSubmit({
        url: url,
        type: method,
        dataType: "json",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        beforeSubmit: function () {},
        success: function (result) {
            expertise_table.draw();
            $("#modalForm").modal("hide");
            showToastr("success", result.message);
        },
        error: function (result) {
            $("[id$='_error']").empty();
            showToastr("error", result.responseJSON.message);
            $.each(result.responseJSON.errors, function (k, v) {
                var id_arr = k.split(".");
                $("body")
                    .find("#expertise_form")
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
                    expertise_table.draw();
                    showToastr("success", result.message);
                },
                error: function (result) {
                    showToastr("error", result.responseJSON.message);
                },
            });
        }
    });
});
