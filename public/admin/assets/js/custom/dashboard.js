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
                showToastr("error", "Error loading form");
            },
        });
    });

    // $(document).on("submit", "#consulting_form", function (e) {
    //     e.preventDefault();

    //     let form = $("#consulting_form");
    //     let url = form.attr("action");
    //     let method = form.find('input[name="_method"]').length ? "PUT" : "POST";

    //     $("[id$='_error']").empty();

    //     let formData = new FormData(form[0]);

    //     formData.append("_token", csrf_token);

    //     $.ajax({
    //         url: url,
    //         type: method,
    //         data: formData,
    //         processData: false,
    //         contentType: false,
    //         dataType: "json",
    //         success: function (result) {
    //             if (result.success) {
    //                 $("#consultingForm").modal("hide");

    //                 let message = result.message;
    //                 if (result.task_id) {
    //                     message +=
    //                         " (Task ID: " +
    //                         result.task_id +
    //                         " created/updated)";
    //                 }
    //                 showToastr("success", message);

    //                 setTimeout(function () {
    //                     window.location.reload();
    //                 }, 1500);
    //             }
    //         },
    //         error: function (xhr) {
    //             if (xhr.status === 422) {
    //                 const errors = xhr.responseJSON.errors;
    //                 $.each(errors, function (k, v) {
    //                     var id_arr = k.split(".");
    //                     $("#consulting_form")
    //                         .find("#" + id_arr[0] + "_error")
    //                         .text(v);
    //                 });
    //                 showToastr("error", "Please fix the validation errors");
    //             } else {
    //                 showToastr(
    //                     "error",
    //                     xhr.responseJSON.message || "Something went wrong!",
    //                 );
    //             }
    //         },
    //     });

    //     return false;
    // });

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
                $("#consultingForm").modal("hide");
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

    $(document).on("hidden.bs.modal", "#consultingForm", function () {
        $("[id$='_error']").empty();
    });

    $(document).on("click", ".calendar-edit-btn", function () {
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
