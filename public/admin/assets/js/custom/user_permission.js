$(document).on("click", ".user-permission-modal", function () {
    $.ajax({
        url: $(this).attr("data-url"),
        type: "GET",
        dataType: "json",
        success: function (data) {
            $("#modal_show_html").html(data.html);
            $("#userPermissionModal").modal("show");
        },
    });
});

$(document).on("change", "#masterSelectAll", function () {
    $(".permission-checkbox").prop("checked", $(this).is(":checked"));
});

$(document).on("change", ".columnSelect", function () {
    let column = $(this).attr("data-column");
    $(".checkbox-" + column).prop("checked", $(this).is(":checked"));
});

$(document).on("change", ".rowSelect", function () {
    $(this)
        .closest("tr")
        .find("input.permission-checkbox")
        .prop("checked", $(this).is(":checked"));
});

// Update Role Permission
$(document).on("click", "#update_permission_button", function (e) {
    e.preventDefault();

    let url = $(this).attr("data-url");
    let formData = $("#permission_form").serialize();

    $.ajax({
        url: url,
        type: "PUT",
        data: formData,
        success: function (result) {
            showToastr("success", result.message);
            $("#userPermissionModal").modal("show");
        },
        error: function (xhr) {
            showToastr("error", "Failed to update permissions");
            console.log(xhr.responseText);
        },
    });
});

// LOAD ROLE PERMISSIONS + RENDER
$(document).on("click", "#display_btn", function () {
    let roleOption = $("#role_select").find(":selected");
    let roleId = roleOption.val();
    let url = roleOption.attr("data-url");

    if (!roleId) {
        showToastr("error", "Please select a role first");
        return;
    }

    // Confirm Change
    if (!confirm("Are you sure want to assign this role?")) {
        $(this).val(oldRole); // revert selection
        return;
    }

    $.ajax({
        url: url,
        type: "GET",
        dataType: "json",
        data: { roleId: roleId },
        success: function (response) {
            if (response.success) {
                $("#permission_form_container").html(response.html);
                showToastr("success", "Permissions loaded");
            }
        },
        error: function (xhr) {
            showToastr("error", "Failed to load permissions");
            console.log(xhr.responseText);
        },
    });
    return false;
});
