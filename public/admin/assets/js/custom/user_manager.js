var user_table = $(".table-list").DataTable({
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
        url: $("#user_table").attr("data-url"),
    },
    columns: [
        {
            data: "id",
            mRender: function (v, t, o, meta) {
                return meta.row + meta.settings._iDisplayStart + 1;
            },
        },
        { data: "name" },
        { data: "email" },
        { data: "phone" },
        {
            data: "role_names",
            className: "text-center",
            render: function (role) {
                if (!role) return "-";

                if (role.toLowerCase() === "vendor") {
                    return `<span class="badge bg-warning text-dark px-3 py-2">
                        Vendor
                    </span>`;
                }

                // All other roles → plain text
                return `<span class="fw-medium text-dark">${role}</span>`;
            },
        },
        {
            data: "guard_name",
            sClass: "text-center",
            mRender: function (v, t, o) {
                var id = o["id"];

                var modal_path = user_permission_modal_path;
                modal_path = modal_path.replace(":user", o["id"]);

                var edit_path_set = edit_path;
                edit_path_set = edit_path_set.replace(":user_manager", id);

                var delete_path_set = delete_path;
                delete_path_set = delete_path_set.replace(":user_manager", id);

                let editDisabled = window.canEditTask
                    ? ""
                    : "style='pointer-events:none;opacity:0.4;' disabled";
                let deleteDisabled = window.canDeleteTask
                    ? ""
                    : "style='pointer-events:none;opacity:0.4;' disabled";

                var html = `<a href="javascript:void(0);" data-url="${modal_path}" title="Give Permission" data-tool-tips="Give Permission" class="user-permission-modal">
                                       <i class="fas fa-cog p-1 text-dark"></i>
                                    </a>`;

                html += `<a href="${edit_path_set}" title="Edit" data-tool-tips="Edit" ${editDisabled}>
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

$(document).ready(function () {
    $("#profileImageBtn").on("click", function () {
        $("#profileImage").click();
    });

    // User Image Preview
    $("#profileImage").change(function (e) {
        let reader = new FileReader();
        reader.onload = function (e) {
            $("#profilePreview").attr("src", e.target.result);
        };
        reader.readAsDataURL(this.files[0]);
        $("#removeUserInput").val(0); // reset remove flag
    });

    // Remove User Image
    $("#removeUser").click(function () {
        $("#profilePreview").attr("src", "");
        $("#profileImage").val("");
        $("#removeUserInput").val(1); // set remove flag
    });
});

$(document).on("click", "#user_form_button", function (e) {
    e.preventDefault(); // prevent default form submit
    let form = $("#user_form")[0]; // get DOM element
    let url = $(this).attr("data-url");
    let method = $("#user_form").find("input[name='_method']").length
        ? "PUT"
        : "POST";

    let formData = new FormData(form); // automatically includes file inputs
    // for (let [key, value] of formData.entries()) {
    //     console.log(key, value);
    // }
    $.ajax({
        url: url,
        type: "POST",
        data: formData,
        dataType: "json",
        processData: false, // important for files
        contentType: false, // important for files
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        beforeSend: function () {},
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
                $("#user_form")
                    .find("#" + id_arr[0] + "_error")
                    .text(v);
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
                    user_table.draw();
                    showToastr("success", result.message);
                },
                error: function (result) {
                    showToastr("error", result.responseJSON.message);
                },
            });
        }
    });
});
