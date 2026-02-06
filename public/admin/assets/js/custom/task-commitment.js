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
    // $(".select2").select2({
    //     placeholder: "Select...",
    //     width: "100%",
    //     dropdownParent: $("#commitmentModal"),
    //     // allowClear: true,
    //     // closeOnSelect: false, // keep dropdown open for multiple selections
    // });
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

    let id = $("#commitment_id").val(); // DB id
    let tmpId = $("#commitment_tmp_id").val(); // TEMP id
    let date = $("#commitment_date").val();
    let text = $("#commitment").val().trim();
    let due = $("#commitment_due_date").val();
    let staffManagerId = $("#staff_manager_id").val();

    if (!text) return;

    // ===============================
    // 🟢 EXISTING DB RECORD
    // ===============================
    if (id) {
        let row = $(`#commitments_${date}`).find(`tr[data-id="${id}"]`);

        // Update table
        row.find("td:eq(1)").text(moment(due).format("DD MMM YYYY"));
        row.find("td:eq(2)").contents().first()[0].textContent = text;

        // ✅ update hidden inputs (MOST IMPORTANT)
        row.find(`input[name="commitments_existing[${id}][text]"]`).val(text);
        row.find(`input[name="commitments_existing[${id}][due_date]"]`).val(
            due,
        );
        row.find(
            `input[name="commitments_existing[${id}][staff_manager_id]"]`,
        ).val(staffManagerId);

        // update edit button dataset
        row.find(".edit-commitment")
            .data("text", text)
            .data("due", due)
            .data("staff_manager_id", staffManagerId);

        $("#commitmentModal").modal("hide");
        return;
    }

    // ===============================
    // 🟡 UPDATE EXISTING TEMP RECORD
    // ===============================
    if (tmpId) {
        let item = commitments[date]?.find(
            (c) => String(c._tmp_id) === String(tmpId),
        );

        if (item) {
            item.text = text;
            item.commitment_due_date = due;

            let row = $(`#commitments_${date}`).find(
                `tr[data-tmp-id="${tmpId}"]`,
            );

            row.find("td:eq(1)").text(moment(due).format("DD MMM YYYY"));
            row.find("td:eq(2)").text(text);

            row.find(".edit-commitment").data("text", text).data("due", due);

            $("#commitmentModal").modal("hide");
            return;
        }
    }

    // ===============================
    // ➕ NEW TEMP RECORD
    // ===============================
    commitments[date] ??= [];
    commitments[date].push({
        _tmp_id: Date.now(),
        text,
        commitment_due_date: due,
        created_at: date,
        staff_manager_id: staffManagerId,
        // created_at: moment().format("YYYY-MM-DD"),
    });

    renderCommitments(date);
    $("#commitmentModal").modal("hide");
});

function renderCommitments(date) {
    let wrapper = $("#commitments_" + date);
    let items = commitments[date] ?? [];

    // ✅ REMOVE EMPTY ROW IF EXISTS
    wrapper.find(".no-commitments").remove();

    items.forEach((item) => {
        // ❗ TEMP ONLY
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
                    <button
                        type="button"
                        class="btn btn-sm btn-primary edit-commitment"
                        data-tmp-id="${item._tmp_id}"
                        data-text="${item.text}"
                        data-due="${item.commitment_due_date}"
                        data-date="${date}"
                    >✎</button>

                    <button
                        type="button"
                        class="btn btn-sm btn-danger delete-commitment"
                        data-tmp-id="${item._tmp_id}"
                        data-date="${date}"
                    >✕</button>
                </td>
            </tr>
        `);
    });
}

$(document).on("click", ".edit-commitment", function () {
    let id = $(this).data("id");
    let text = $(this).data("text");
    let staffId = $(this).data("staff-manager-id");
    let dueDate = $(this).data("due");
    let date = $(this).data("date");

    $("#commitment_id").val(id);
    $("#commitment_text").val(text);
    $("#commitment_due_date").val(dueDate);

    let staffSelect = $("#commitment_staff_manager_id");

    $("#commitmentModal").modal("show");
    initSelect2();

    // 🔥 RESET first (important)
    staffSelect.val(null).trigger("change");

    // 🔥 FORCE string match for Select2
    if (staffId) {
        staffSelect.val(String(staffId)).trigger("change.select2");
    }
});

$(document).on("click", ".delete-commitment", function () {
    let id = $(this).data("id");
    let tmpId = $(this).data("tmp-id");
    let date = $(this).data("date");

    removeCommitment(id, tmpId, date);
});

function removeCommitment(id, tmpId, date) {
    if (id) {
        commitmentsToDelete.push(id);
        $(`#commitments_${date}`).find(`tr[data-id="${id}"]`).remove();
    }

    if (tmpId) {
        commitments[date] =
            commitments[date]?.filter(
                (c) => String(c._tmp_id) !== String(tmpId),
            ) || [];

        $(`#commitments_${date}`).find(`tr[data-tmp-id="${tmpId}"]`).remove();
    }

    // ✅ EMPTY STATE CHECK
    let wrapper = $(`#commitments_${date}`);

    if (wrapper.find("tr").length === 0) {
        wrapper.html(`
            <tr class="no-commitments">
                <td colspan="4" class="text-muted text-center">
                    No commitments for this date
                </td>
            </tr>
        `);
    }
}
