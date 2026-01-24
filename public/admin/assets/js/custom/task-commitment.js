$(document).on("click", ".open-commitment-modal", function () {
    let date = $(this).data("date");

    $("#commitment_due_date").val(date);
    $("#commitment_date").val(date);

    $("#commitmentModal").modal("show");
});

$("#commitment_form").on("submit", function (e) {
    e.preventDefault();

    let id     = $("#commitment_id").val();
    let tmpId  = $("#commitment_tmp_id").val();
    let text   = $("#commitment").val().trim();
    let due    = $("#commitment_due_date").val();
    let date   = $("#commitment_date").val();

    commitments[date] ??= [];

    // 🔁 UPDATE DB RECORD
    if (id) {
        let item = commitments[date].find(c => String(c.id) === String(id));
        if (item) {
            item.text = text;
            item.commitment_due_date = due;
        }
    }
    // 🔁 UPDATE TEMP RECORD
    else if (tmpId) {
        let item = commitments[date].find(c => String(c._tmp_id) === String(tmpId));
        if (item) {
            item.text = text;
            item.commitment_due_date = due;
        }
    }
    // ➕ NEW TEMP RECORD
    else {
        commitments[date].push({
            _tmp_id: Date.now(),
            text,
            commitment_due_date: due,
            created_at: moment().format("YYYY-MM-DD"),
        });
    }

    renderCommitments(date);

    $("#commitmentModal").modal("hide");
    this.reset();
    $("#commitment_id").val("");
    $("#commitment_tmp_id").val("");
});


// $("#commitment_form").on("submit", function (e) {
//     e.preventDefault();

//     let id = $("#commitment_id").val();
//     let text = $("#commitment").val().trim();
//     let due = $("#commitment_due_date").val();
//     let date = $("#commitment_date").val();

//     if (!text || !due) {
//         showToaster("error", "Enter text & date");
//         return;
//     }

//     commitments[date] ??= [];

//     if (id) {
//         // 🔁 UPDATE existing
//         let item = commitments[date].find((c) => String(c.id) === String(id));

//         if (item) {
//             item.text = text;
//             item.commitment_due_date = due;
//         }
//     } else {
//         // ➕ INSERT new (avoid duplicates)
//         let exists = commitments[date].some(
//             (c) => !c.id && c.text === text && c.commitment_due_date === due,
//         );

//         if (!exists) {
//             commitments[date].push({
//                 text,
//                 commitment_due_date: due,
//                 created_at: moment().format("YYYY-MM-DD"),
//                 // status: "pending",
//             });
//         }
//     }

//     renderCommitments(date);

//     $("#commitmentModal").modal("hide");
//     this.reset();
//     $("#commitment_id").val(""); // 🔑 important
// });

function renderCommitments(date) {
    let wrapper = $("#commitments_" + date);
    wrapper.html("");
    let items = commitments[date] ?? [];

    if (items.length === 0) {
        wrapper.html(
            `<tr><td colspan="4" class="text-muted text-center">No commitments for this date</td></tr>`,
        );
        return;
    }

    items.forEach((item) => {
        let createdDate = item.created_at
            ? moment(item.created_at).format("DD MMM YYYY")
            : "-";
        let dueDate = moment(item.commitment_due_date).format("DD MMM YYYY");
        wrapper.append(`
            <tr 
                ${item.id ? `data-id="${item.id}"` : ""}
                ${item._tmp_id ? `data-tmp-id="${item._tmp_id}"` : ""}
                >
                <td>${createdDate}</td>
                <td>${dueDate}</td>
                <td>${item.text}</td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-primary edit-commitment" data-id="${item.id ?? ""}" data-tmp-id="${item._tmp_id ?? ""}" data-text="${item.text}" data-due="${item.commitment_due_date}" data-date="${date}">✎</button>
                    <button type="button" class="btn btn-sm btn-danger delete-commitment" data-id="${item.id ?? ""}" data-tmp-id="${item._tmp_id ?? ""}" data-date="${date}">✕</button>
                </td>
            </tr>
        `);
    });
}

// --------------- Commitments ----------------
$(document).on("click", ".edit-commitment", function () {
    let id = $(this).data("id");
    let text = $(this).data("text");
    let due = $(this).data("due");
    let date = $(this).data("date");

    $("#commitment_id").val(id);
    $("#commitment_tmp_id").val($(this).data("tmp-id") || "");
    $("#commitment").val(text);
    $("#commitment_due_date").val(due);
    $("#commitment_date").val(date);
    $("#commitmentModal").modal("show");
});

$(document).on("click", ".delete-commitment", function () {
    let id    = $(this).data("id");
    let tmpId = $(this).data("tmp-id");
    let date  = $(this).data("date");

    removeCommitment(id, tmpId, date);
});


function removeCommitment(id, tmpId, date) {
    if (!commitments[date]) return;

    // 🗑 DB record
    if (id) {
        commitmentsToDelete.push(id);
        commitments[date] = commitments[date].filter(
            c => String(c.id) !== String(id)
        );
    }
    // 🗑 TEMP record
    else if (tmpId) {
        commitments[date] = commitments[date].filter(
            c => String(c._tmp_id) !== String(tmpId)
        );
    }

    renderCommitments(date);
}
