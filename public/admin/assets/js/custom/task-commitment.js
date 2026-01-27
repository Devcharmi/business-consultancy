$(document).on("click", ".open-commitment-modal", function () {
    let date = $(this).data("date");

    $("#commitment_form")[0].reset();
    $("#commitment_id").val("");
    $("#commitment_tmp_id").val("");
    $("#commitment_date").val(date);
    $("#commitment_due_date").val(date);

    $("#commitmentModal").modal("show");
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

    let id    = $("#commitment_id").val();        // DB id
    let tmpId = $("#commitment_tmp_id").val();    // TEMP id
    let date  = $("#commitment_date").val();
    let text  = $("#commitment").val().trim();
    let due   = $("#commitment_due_date").val();

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
        row.find(`input[name="commitments_existing[${id}][due_date]"]`).val(due);

        // update edit button dataset
        row.find(".edit-commitment")
            .data("text", text)
            .data("due", due);

        $("#commitmentModal").modal("hide");
        return;
    }

    // ===============================
    // 🟡 UPDATE EXISTING TEMP RECORD
    // ===============================
    if (tmpId) {
        let item = commitments[date]?.find(
            c => String(c._tmp_id) === String(tmpId)
        );

        if (item) {
            item.text = text;
            item.commitment_due_date = due;

            let row = $(`#commitments_${date}`).find(
                `tr[data-tmp-id="${tmpId}"]`
            );

            row.find("td:eq(1)").text(moment(due).format("DD MMM YYYY"));
            row.find("td:eq(2)").text(text);

            row.find(".edit-commitment")
                .data("text", text)
                .data("due", due);

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
        // created_at: moment().format("YYYY-MM-DD"),
    });

    renderCommitments(date);
    $("#commitmentModal").modal("hide");
});


function renderCommitments(date) {
    let wrapper = $("#commitments_" + date);
    let items = commitments[date] ?? [];

    items.forEach(item => {
        // ❗ TEMP ONLY
        if (!item._tmp_id) return;

        if (wrapper.find(`tr[data-tmp-id="${item._tmp_id}"]`).length) return;

        let createdDate = moment(item.created_at).format("DD MMM YYYY");
        let dueDate     = moment(item.commitment_due_date).format("DD MMM YYYY");

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

// function renderCommitments(date) {
//     let wrapper = $("#commitments_" + date);
//     wrapper.html("");
//     let items = commitments[date] ?? [];

//     if (items.length === 0) {
//         wrapper.html(
//             `<tr><td colspan="4" class="text-muted text-center">No commitments for this date</td></tr>`,
//         );
//         return;
//     }

//     items.forEach((item) => {
//         let createdDate = item.created_at
//             ? moment(item.created_at).format("DD MMM YYYY")
//             : "-";
//         let dueDate = moment(item.commitment_due_date).format("DD MMM YYYY");
//         wrapper.append(`
//             <tr
//                 ${item.id ? `data-id="${item.id}"` : ""}
//                 ${item._tmp_id ? `data-tmp-id="${item._tmp_id}"` : ""}
//                 >
//                 <td>${createdDate}</td>
//                 <td>${dueDate}</td>
//                 <td>${item.text}</td>
//                 <td class="text-center">
//                     <button type="button" class="btn btn-sm btn-primary edit-commitment" data-id="${item.id ?? ""}" data-tmp-id="${item._tmp_id ?? ""}" data-text="${item.text}" data-due="${item.commitment_due_date}" data-date="${date}">✎</button>
//                     <button type="button" class="btn btn-sm btn-danger delete-commitment" data-id="${item.id ?? ""}" data-tmp-id="${item._tmp_id ?? ""}" data-date="${date}">✕</button>
//                 </td>
//             </tr>
//         `);
//     });
// }

// --------------- Commitments ----------------

$(document).on("click", ".edit-commitment", function () {
    $("#commitment_id").val($(this).data("id") || "");
    $("#commitment_tmp_id").val($(this).data("tmp-id") || "");

    $("#commitment").val($(this).data("text"));
    $("#commitment_due_date").val($(this).data("due"));
    $("#commitment_date").val($(this).data("date"));

    $("#commitmentModal").modal("show");
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
        return;
    }

    if (tmpId) {
        commitments[date] = commitments[date].filter(
            c => String(c._tmp_id) !== String(tmpId)
        );

        $(`#commitments_${date}`).find(
            `tr[data-tmp-id="${tmpId}"]`
        ).remove();
    }
}

