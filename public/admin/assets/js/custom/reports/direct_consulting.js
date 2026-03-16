
$(document).ready(function () {
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