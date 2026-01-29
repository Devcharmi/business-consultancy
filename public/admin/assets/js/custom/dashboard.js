$(document).ready(function () {
    $(document).on("click", ".calendar-add-btn", function () {
        const date = $(this).data("date");
        const url = $(this).data("url");

        $("#modal_show_html").html(
            '<div class="text-center p-5"><i class="bi bi-hourglass-split fs-1"></i><p>Loading...</p></div>',
        );

        $.ajax({
            url: url,
            method: "GET",
            dataType: "json",
            success: function (response) {
                $("#modal_show_html").html(response.html);

                const datetimeInput = $("#consulting_datetime");
                if (datetimeInput.length) {
                    datetimeInput.val(date + "T09:00");
                }

                if ($.fn.select2) {
                    $(".select2").select2({
                        placeholder: "Select...",
                        width: "100%",
                        dropdownParent: $("#consultingForm"),
                        allowClear: true,
                    });
                }

                $("#consultingForm").modal("show");
            },
            error: function () {
                alert("Error loading form");
            },
        });
    });

    $(document).on("submit", "#consulting_form", function (e) {
        e.preventDefault();

        let form = $("#consulting_form");
        let url = form.attr("action");
        let method = form.find('input[name="_method"]').length ? "PUT" : "POST";

        $("[id$='_error']").empty();

        let formData = new FormData(form[0]);

        formData.append("_token", csrf_token);

        $.ajax({
            url: url,
            type: method,
            data: formData,
            processData: false,
            contentType: false,
            dataType: "json",
            success: function (result) {
                if (result.success) {
                    $("#consultingForm").modal("hide");

                    let message = result.message;
                    if (result.task_id) {
                        message +=
                            " (Task ID: " +
                            result.task_id +
                            " created/updated)";
                    }
                    showToastr("success", message);

                    setTimeout(function () {
                        window.location.reload();
                    }, 1500);
                }
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    $.each(errors, function (k, v) {
                        var id_arr = k.split(".");
                        $("#consulting_form")
                            .find("#" + id_arr[0] + "_error")
                            .text(v);
                    });
                    showToastr("error", "Please fix the validation errors");
                } else {
                    showToastr(
                        "error",
                        xhr.responseJSON.message || "Something went wrong!",
                    );
                }
            },
        });

        return false;
    });

    $(document).on("hidden.bs.modal", "#consultingForm", function () {
        $("[id$='_error']").empty();
    });
});
