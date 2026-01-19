var client_table = $(".table-list").DataTable({
    order: [[0, "desc"]],
    autoWidth: false,
    processing: true,
    serverSide: true,
    ajax: {
        url: $("#client_table").attr("data-url"),
    },
    columns: [
        // {
        //     data: "id",
        //     mRender: function (v, t, o, meta) {
        //         return meta.row + meta.settings._iDisplayStart + 1;
        //     },
        // },
        { data: "client_name" },
        { data: "email" },
        { data: "phone" },
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
            data: "id",
            sClass: "text-center",
            mRender: function (v, t, o) {
                let edit_url = edit_path.replace(":id", v);
                let delete_url = delete_path.replace(":id", v);
                let editDisabled = window.canEditTask
                    ? ""
                    : "style='pointer-events:none;opacity:0.4;' disabled";
                let deleteDisabled = window.canDeleteTask
                    ? ""
                    : "style='pointer-events:none;opacity:0.4;' disabled";
                return `<a href="${edit_url}" class="text-primary me-2" ${editDisabled}><i class="fas fa-pen"></i></a>
                        <a href="javascript:void(0);" class="text-danger delete-data" ${deleteDisabled} data-url="${delete_url}"><i class="fas fa-trash"></i></a>`;
            },
        },
    ],
    language: {
        searchPlaceholder: "Search...",
        sSearch: "",
        lengthMenu: "_MENU_&nbsp; items/page",
    },
});

$(document).on("click", "#client_form_button", function () {
    let form = $("#client_form")[0];
    let url = $(this).attr("data-url");
    let formData = new FormData(form);

    $.ajax({
        url: url,
        type: "POST",
        data: formData,
        dataType: "json",
        processData: false,
        contentType: false,
        headers: { "X-CSRF-TOKEN": csrf_token },
        success: function (res) {
            if (res.success) {
                showToastr("success", res.message);
                setTimeout(() => (window.location.href = index_path), 1500);
            } else {
                showToastr("error", res.message);
            }
        },
        error: function (res) {
            $("[id$='_error']").text("");
            let errors = res.responseJSON.errors;
            for (let key in errors) {
                $("#" + key + "_error").text(errors[key]);
            }
        },
    });
});

$(document).on("click", ".delete-data", function () {
    let url = $(this).data("url");
    swal.fire({
        title: "Are you sure?",
        text: "This action cannot be undone!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: url,
                type: "DELETE",
                data: { _token: csrf_token },
                success: function (res) {
                    if (res.success) {
                        showToastr("success", res.message);
                        client_table.draw();
                    } else {
                        showToastr("error", res.message);
                    }
                },
            });
        }
    });
});
