var consulting_table = $(".table-list").DataTable({
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
        url: $("#consulting_table").attr("data-url"),
    },
    columns: [
        // 1️⃣ Sr No
        {
            data: "id",
            render: function (data, type, row, meta) {
                return meta.row + meta.settings._iDisplayStart + 1;
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
                return data ? data.name : "-";
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
            data: "consulting_datetime",
            render: function (data) {
                return data ? moment(data).format("DD-MM-YYYY HH:mm") : "-";
            },
        },

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
            $("#consultingForm").modal("show");
            $(".select2").select2({
                placeholder: "Select...",
                width: "100%",
                dropdownParent: $("#consultingForm"),
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
            $("#consultingForm").modal("hide");
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
