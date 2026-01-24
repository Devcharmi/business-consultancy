let selectedAttachments = [];

// Select & preview
$("#attachmentInput").on("change", function (e) {
    selectedAttachments = Array.from(e.target.files);
    renderAttachmentPreviews();
});

function renderAttachmentPreviews() {
    let wrapper = $("#previewContainer");
    wrapper.html("");

    selectedAttachments.forEach((file, index) => {
        let reader = new FileReader();
        reader.onload = e => {
            wrapper.append(`
                <div class="col-md-3 mb-3">
                    <div class="card position-relative">
                        <img src="${e.target.result}"
                             class="card-img-top"
                             style="height:150px;object-fit:cover">

                        <button type="button"
                            class="btn btn-sm btn-danger position-absolute top-0 end-0 remove-preview"
                            data-index="${index}">
                            ✕
                        </button>
                    </div>
                </div>
            `);
        };
        reader.readAsDataURL(file);
    });
}

// Remove selected (before submit)
$(document).on("click", ".remove-preview", function () {
    let index = $(this).data("index");
    selectedAttachments.splice(index, 1);
    renderAttachmentPreviews();
});

$(document).on("click", ".delete-existing", function () {
    let id = $(this).data("id");

    if (!confirm("Delete attachment?")) return;

    let url = window.deleteAttachment.replace(":id", id);

    $.ajax({
        url: url,
        type: "DELETE",
        data: {
            _token: csrf_token
        },
        success: function (res) {
            if (res.success) {
                $("#attachment-" + id).fadeOut(300, function () {
                    $(this).remove();
                });
            }
        },
        error: function () {
            alert("Failed to delete attachment");
        }
    });
});

