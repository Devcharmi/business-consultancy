/** 🖼️ Handle new attachment selection **/
/** 🖼️ Handle new attachment selection **/
$(document).on("change", "#attachmentInput", function (e) {
    let $preview = $("#previewContainer");
    $preview.html("");

    let files = e.target.files;

    $.each(files, function (index, file) {
        let reader = new FileReader();

        reader.onload = function (e) {
            let html = `
                <div class="col-md-3 mb-3 new-attachment-item" data-index="${index}">
                    <div class="card position-relative">
                        
                        <img src="${e.target.result}"
                             class="card-img-top"
                             style="height:140px;object-fit:cover;">

                        <button type="button"
                                class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1 btn-remove-new-attachment">
                            ✕
                        </button>

                        <div class="card-body p-2">
                            <input type="text"
                                   name="attachment_names[]"
                                   class="form-control form-control-sm"
                                   placeholder="Enter file name"
                                   value="${file.name.replace(/\.[^/.]+$/, "")}">
                        </div>
                    </div>
                </div>
            `;

            $preview.append(html);
        };

        reader.readAsDataURL(file);
    });
});


/** ❌ Remove newly selected attachment **/
$(document).on("click", ".btn-remove-new-attachment", function () {
    let container = $(this).closest(".new-attachment-item");
    let removeIndex = parseInt(container.attr("data-index"));

    let input = document.getElementById("attachmentInput");
    let dt = new DataTransfer();

    $.each(input.files, function (i, file) {
        if (i !== removeIndex) {
            dt.items.add(file);
        }
    });

    input.files = dt.files;
    container.remove();

    // 🔁 Re-index remaining previews (VERY IMPORTANT)
    $("#previewContainer .new-attachment-item").each(function (i) {
        $(this).attr("data-index", i);
    });
});


/** 🗑️ Delete existing attachment **/
$(document).on("click", ".btn-remove-attachment", function () {
    let $btn = $(this);
    let deleteUrl = $btn.data("url");

    if (!confirm("Delete this attachment?")) return;

    $.ajax({
        url: deleteUrl,
        type: "DELETE",
        data: {
            _token: csrf_token,
        },
        beforeSend: function () {
            $btn.prop("disabled", true);
        },
        success: function (res) {
            if (res.success) {
                $btn.closest(".attachment-item").fadeOut(200, function () {
                    $(this).remove();
                });
            } else {
                showToastr("error", "Unable to delete attachment");
            }
        },
        error: function () {
            showToastr("error", "Something went wrong");
        },
        complete: function () {
            $btn.prop("disabled", false);
        },
    });
});
