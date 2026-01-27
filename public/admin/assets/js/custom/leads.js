var leads_table = $("#leads_table").DataTable({
    order: [[0, "desc"]],
    autoWidth: false,
    processing: true,
    serverSide: true,
    serverMethod: "GET",
    lengthMenu: [
        [20, 100, 200, 250],
        [20, 100, 200, 250],
    ],
    ajax: {
        url: $("#leads_table").attr("data-url"),
    },
    columns: [
        {
            data: "id",
            sClass: "text-center",
            mRender: function (v, t, o) {
                let id = o.id;

                // URLs
                let viewFollowUpUrl = follow_up_list_url.replace(":lead", id);
                let editUrl = edit_path.replace(":id", id);
                let deleteUrl = delete_path.replace(":id", id);

                // Permission handling
                let editDisabled = window.canEditTask
                    ? ""
                    : "style='pointer-events:none;opacity:0.4;' disabled";

                let deleteDisabled = window.canDeleteTask
                    ? ""
                    : "style='pointer-events:none;opacity:0.4;' disabled";

                let html = `
            <button class="btn btn-sm btn-info view-followups me-1"
                data-url="${viewFollowUpUrl}"
                data-lead-id="${id}"
                title="View Follow Ups">
                <i class="fas fa-comments"></i>
            </button>
        `;

                const isConverted = o.status === "converted";

                html += `
    <a href="${isConverted ? "javascript:void(0)" : editUrl}"  ${editDisabled}
       class="lead-edit ${isConverted ? "disabled-link" : ""}"
       title="${isConverted ? "Lead already converted" : "Edit"}"
       data-tool-tips="${isConverted ? "Lead already converted" : "Edit"}"
       ${isConverted ? 'aria-disabled="true"' : ""}>
        <i class="fas fa-pen p-1 ${isConverted ? "text-muted" : "text-primary"}"></i>
    </a>
`;

                html += `
            <a href="javascript:void(0);"
               data-url="${deleteUrl}"
               title="Delete"
               data-tool-tips="Delete"
               class="delete-lead"
               ${deleteDisabled}>
                <i class="fas fa-trash p-1 text-danger"></i>
            </a>
        `;

                return html;
            },
        },
        { data: "name" },
        { data: "phone" },
        { data: "email" },
        // {
        //     data: "objective_manager",
        //     mRender: function (v) {
        //         return v ? v.name : "-";
        //     },
        // },
        {
            data: "status",
            mRender: function (v, t, o) {
                // let badge = {
                //     new: "secondary",
                //     contacted: "info",
                //     converted: "success",
                //     lost: "danger",
                // };

                return `<select class="form-select lead-status" data-id="${
                    o.id
                }" ${v === "converted" ? "disabled" : ""}>
                            <option value="new" ${
                                v == "new" ? "selected" : ""
                            }>New</option>
                            <option value="contacted" ${
                                v == "contacted" ? "selected" : ""
                            }>Contacted</option>
                            <option value="converted" ${
                                v == "converted" ? "selected" : ""
                            }>Converted</option>
                            <option value="lost" ${
                                v == "lost" ? "selected" : ""
                            }>Lost</option>
                        </select>`;
                // return `<span class="badge bg-${badge[v]}">${v.replace(
                //     "_",
                //     " "
                // )}</span>`;
            },
        },
        {
            data: "created_at",
            mRender: function (v) {
                return moment(v).format("D-M-Y");
            },
        },
    ],
    language: {
        searchPlaceholder: "Search...",
        sSearch: "",
        lengthMenu: "_MENU_&nbsp; items/page",
    },
});

/* 🔄 STATUS UPDATE (AJAX – SAME PATTERN) */
$(document).on("change", ".lead-status", function () {
    let status = $(this).val();
    let leadId = $(this).data("id");

    $.ajax({
        url: updateLeadStatusUrl, // define globally
        type: "POST",
        data: {
            _token: csrf_token,
            lead_id: leadId,
            status: status,
        },
        success: function (res) {
            toastr.success(res.message);
            leads_table.draw();
        },
        error: function () {
            toastr.error("Failed to update lead");
        },
    });
});

$(document).on("click", "#saveFollowUp", function () {
    $.ajax({
        url: $(this).attr("data-url"),
        type: "POST",
        data: {
            _token: csrf_token,
            lead_id: $(this).data("id"),
            remark: $("#follow_remark").val(),
            next_follow_up_at: $("#next_follow_up_at").val(),
            status: $("#follow_status").val(),
        },
        success: function (res) {
            toastr.success(res.message);
            location.reload();
        },
        error: function () {
            toastr.error("Failed to save follow up");
        },
    });
});

$(document).on("click", ".view-followups", function () {
    currentLeadId = $(this).data("lead-id");
    currentFollowUpUrl = $(this).data("url");

    if (!currentLeadId) {
        toastr.error("Invalid lead");
        return;
    }

    $("#followUpLeadId").val(currentLeadId);
    $("#followUpModal").modal("show");

    loadFollowUps();
});

$(document).on("submit", "#followUpForm", function (e) {
    e.preventDefault();

    let form = $(this);

    $.post(form.data("url"), form.serialize(), function (res) {
        toastr.success(res.message);

        loadFollowUps();

        // reload table to reflect lead status
        $("#leads_table").DataTable().ajax.reload(null, false);

        form[0].reset();
    });
});

function loadFollowUps() {
    $("#followUpList").html(`
        <div class="text-center py-4">
            <i class="fas fa-spinner fa-spin"></i>
        </div>
    `);

    $.get(currentFollowUpUrl, function (res) {
        $("#followUpList").html(res);
    });
}

function setClientInfo(guestSelect) {
    let name = $(guestSelect).find(":selected").data("name") || "";
    let phone = $(guestSelect).find(":selected").data("phone") || "";
    let email = $(guestSelect).find(":selected").data("email") || "";
    $("#name").val(name);
    $("#phone").val(phone);
    $("#email").val(email);
}

$(document).on("click", "#lead_form_button", function () {
    let form = $("#lead_form");
    let url = $(this).attr("data-url");

    // Detect create / update
    let method = form.find("input[name='_method']").length ? "PUT" : "POST";

    $("#lead_form").ajaxSubmit({
        url: url,
        type: method,
        dataType: "json",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        beforeSubmit: function () {
            $("[id$='_error']").empty();
        },
        success: function (result) {
            if (typeof leads_table !== "undefined") {
                leads_table.draw();
            }
            showToastr("success", result.message);
            window.location.href = index_path; // or modal hide if modal
        },
        error: function (result) {
            $("[id$='_error']").empty();

            if (result.responseJSON?.errors) {
                $.each(result.responseJSON.errors, function (k, v) {
                    let field = k.split(".")[0];
                    $("#" + field + "_error").text(v[0]);
                });
            }

            showToastr(
                "error",
                result.responseJSON?.message || "Validation error",
            );
        },
    });

    return false;
});

$(document).on("click", ".delete-lead", function (event) {
    event.preventDefault();

    let delete_url = $(this).attr("data-url");

    swal.fire({
        title: "Are you sure?",
        text: "This lead will be permanently deleted!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: delete_url,
                type: "DELETE",
                data: {
                    _token: csrf_token,
                },
                success: function (result) {
                    if (typeof leads_table !== "undefined") {
                        leads_table.draw();
                    }
                    showToastr("success", result.message);
                },
                error: function (result) {
                    showToastr(
                        "error",
                        result.responseJSON?.message || "Delete failed",
                    );
                },
            });
        }
    });
});
