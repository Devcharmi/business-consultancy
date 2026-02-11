function initSelect2() {
    $(".select2").select2({
        placeholder: "Select...",
        width: "100%",
        dropdownParent: $("#commitmentModal"),
        // allowClear: true,
        // closeOnSelect: false, // keep dropdown open for multiple selections
    });
}

$(document).on("click", ".open-commitment-modal", function () {
    let date = $(this).data("date");
    $("#commitment_form")[0].reset();
    $("#commitment_id").val("");
    $("#commitment_tmp_id").val("");
    $("#commitment_date").val(date);
    $("#commitment_due_date").val(date);

    $("#commitmentModal").modal("show");
    initSelect2();
});

// $("#commitment_form").on("submit", function (e) {
//     e.preventDefault();

//     let id     = $("#commitment_id").val();
//     let tmpId  = $("#commitment_tmp_id").val();
//     let text   = $("#commitment").val().trim();
//     let due    = $("#commitment_due_date").val();
//     let date   = $("#commitment_date").val();

//     commitments[date] ??= [];

//     // 🔁 UPDATE DB RECORD
//     if (id) {
//         let item = commitments[date].find(c => String(c.id) === String(id));
//         if (item) {
//             item.text = text;
//             item.commitment_due_date = due;
//         }
//     }
//     // 🔁 UPDATE TEMP RECORD
//     else if (tmpId) {
//         let item = commitments[date].find(c => String(c._tmp_id) === String(tmpId));
//         if (item) {
//             item.text = text;
//             item.commitment_due_date = due;
//         }
//     }
//     // ➕ NEW TEMP RECORD
//     else {
//         commitments[date].push({
//             _tmp_id: Date.now(),
//             text,
//             commitment_due_date: due,
//             created_at: moment().format("YYYY-MM-DD"),
//         });
//     }

//     renderCommitments(date);

//     $("#commitmentModal").modal("hide");
//     this.reset();
//     $("#commitment_id").val("");
//     $("#commitment_tmp_id").val("");
// });
$("#commitment_form").on("submit", function (e) {
    e.preventDefault();

    let id = $("#commitment_id").val();
    let tmpId = $("#commitment_tmp_id").val();
    let text = $("#commitment").val().trim();
    let due = $("#commitment_due_date").val();
    let staffManagerId = $("#staff_manager_id").val();

    if (!text) return;

    // ===============================
    // 🟢 UPDATE EXISTING DB RECORD
    // ===============================
    if (id) {
        let row = $(`#commitments_table`).find(`tr[data-id="${id}"]`);

        row.find("td:eq(1)").text(moment(due).format("DD MMM YYYY"));
        row.find("td:eq(2)").text(text);

        row.find(`input[name="commitments_existing[${id}][text]"]`).val(text);
        row.find(`input[name="commitments_existing[${id}][due_date]"]`).val(
            due,
        );
        row.find(
            `input[name="commitments_existing[${id}][staff_manager_id]"]`,
        ).val(staffManagerId);

        $("#commitmentModal").modal("hide");
        return;
    }

    // ===============================
    // 🟡 UPDATE TEMP RECORD
    // ===============================
    if (tmpId) {
        let item = commitments.find((c) => String(c._tmp_id) === String(tmpId));

        if (item) {
            item.text = text;
            item.commitment_due_date = due;

            let row = $(`#commitments_table`).find(
                `tr[data-tmp-id="${tmpId}"]`,
            );

            row.find("td:eq(1)").text(moment(due).format("DD MMM YYYY"));
            row.find("td:eq(2)").text(text);

            $("#commitmentModal").modal("hide");
            return;
        }
    }

    // ===============================
    // ➕ NEW TEMP RECORD
    // ===============================
    commitments.push({
        _tmp_id: Date.now(),
        text,
        commitment_due_date: due,
        created_at: moment().format("YYYY-MM-DD"),
        staff_manager_id: staffManagerId,
    });

    renderCommitments();
    $("#commitmentModal").modal("hide");
});

function renderCommitments() {
    let wrapper = $("#commitments_table");
    wrapper.find(".no-commitments").remove();

    commitments.forEach((item) => {
        if (!item._tmp_id) return;

        if (wrapper.find(`tr[data-tmp-id="${item._tmp_id}"]`).length) return;

        let createdDate = moment(item.created_at).format("DD MMM YYYY");
        let dueDate = moment(item.commitment_due_date).format("DD MMM YYYY");

        wrapper.append(`
            <tr data-tmp-id="${item._tmp_id}">
                <td>${createdDate}</td>
                <td>${dueDate}</td>
                <td>${item.text}</td>
                <td class="text-center">
                    <button type="button"
                        class="btn btn-sm btn-primary edit-commitment"
                        data-tmp-id="${item._tmp_id}"
                        data-text="${item.text}"
                        data-due="${item.commitment_due_date}">
                        ✎
                    </button>

                    <button type="button"
                        class="btn btn-sm btn-danger delete-commitment"
                        data-tmp-id="${item._tmp_id}">
                        ✕
                    </button>
                </td>
            </tr>
        `);
    });
}

$(document).on("click", ".edit-commitment", function () {
    let id = $(this).data("id");
    let tmpId = $(this).data("tmp-id");
    let text = $(this).data("text");
    let dueDate = $(this).data("due");

    $("#commitment_id").val(id ?? "");
    $("#commitment_tmp_id").val(tmpId ?? "");
    $("#commitment").val(text);
    $("#commitment_due_date").val(dueDate);

    $("#commitmentModal").modal("show");
    initSelect2();
});

$(document).on("click", ".delete-commitment", function () {
    let id = $(this).data("id");
    let tmpId = $(this).data("tmp-id");

    removeCommitment(id, tmpId);
});

function removeCommitment(id, tmpId) {
    if (id) {
        commitmentsToDelete.push(id);
        $(`#commitments_table`).find(`tr[data-id="${id}"]`).remove();
    }

    if (tmpId) {
        commitments = commitments.filter(
            (c) => String(c._tmp_id) !== String(tmpId),
        );

        $(`#commitments_table`).find(`tr[data-tmp-id="${tmpId}"]`).remove();
    }

    // Empty state check
    let wrapper = $("#commitments_table");

    if (wrapper.find("tr").length === 0) {
        wrapper.html(`
            <tr class="no-commitments">
                <td colspan="4" class="text-muted text-center">
                    No commitments added
                </td>
            </tr>
        `);
    }
}
