$(document).on("click", ".open-deliverable-modal", function () {
    let date = $(this).data("date");
    $("#expected_date").val(date);
    $("#deliverable_date").val(date);
    $("#deliverableModal").modal("show");
});

$("#deliverable_form").on("submit", function (e) {
    e.preventDefault();

    let id       = $("#deliverable_id").val();
    let tmpId    = $("#deliverable_tmp_id").val();
    let text     = $("#deliverable").val().trim();
    let expected = $("#expected_date").val();
    let date     = $("#deliverable_date").val();

    if (!text || !expected) {
        showToaster("error", "Enter deliverable & expected date");
        return;
    }

    deliverables[date] ??= [];

    // 🔁 UPDATE DB RECORD
    if (id) {
        let item = deliverables[date].find(d => String(d.id) === String(id));
        if (item) {
            item.text = text;
            item.expected_date = expected;
        }
    }
    // 🔁 UPDATE TEMP RECORD
    else if (tmpId) {
        let item = deliverables[date].find(d => String(d._tmp_id) === String(tmpId));
        if (item) {
            item.text = text;
            item.expected_date = expected;
        }
    }
    // ➕ NEW TEMP RECORD
    else {
        deliverables[date].push({
            _tmp_id: Date.now(), // 🔑 temp key
            text,
            expected_date: expected,
            created_at: moment().format("YYYY-MM-DD"),
        });
    }

    renderDeliverables(date);

    $("#deliverableModal").modal("hide");
    this.reset();
    $("#deliverable_id").val("");
    $("#deliverable_tmp_id").val("");
});

// $("#deliverable_form").on("submit", function (e) {
//     e.preventDefault();

//     let id       = $("#deliverable_id").val();
//     let text     = $("#deliverable").val().trim();
//     let expected = $("#expected_date").val();
//     let date     = $("#deliverable_date").val();

//     if (!text || !expected) {
//         showToaster("error","Enter deliverable & expected date");
//         return;
//     }

//     deliverables[date] ??= [];

//     if (id) {
//         // 🔁 UPDATE existing deliverable
//         let item = deliverables[date].find(d => String(d.id) === String(id));

//         if (item) {
//             item.text = text;
//             item.expected_date = expected;
//         }
//     } else {
//         // ➕ INSERT new deliverable (avoid duplicates)
//         let exists = deliverables[date].some(
//             d => !d.id && d.text === text && d.expected_date === expected
//         );

//         if (!exists) {
//             deliverables[date].push({
//                 text,
//                 expected_date: expected,
//                 created_at: moment().format("YYYY-MM-DD"),
//                 // status: "pending",
//             });
//         }
//     }

//     renderDeliverables(date);

//     $("#deliverableModal").modal("hide");
//     this.reset();
//     $("#deliverable_id").val(""); // 🔑 reset ID
// });

function renderDeliverables(date) {
    let wrapper = $("#deliverables_" + date);
    wrapper.html("");
    let items = deliverables[date] ?? [];

    if (items.length === 0) {
        wrapper.html(
            `<tr><td colspan="4" class="text-muted text-center">No deliverables for this date</td></tr>`,
        );
        return;
    }

    items.forEach((item) => {
        let createdDate = item.created_at
            ? moment(item.created_at).format("DD MMM YYYY")
            : "-";
        let expectedDate = moment(item.expected_date).format("DD MMM YYYY");
        wrapper.append(`
            <tr
                ${item.id ? `data-id="${item.id}"` : ""}
                ${item._tmp_id ? `data-tmp-id="${item._tmp_id}"` : ""}
            >
                <td>${createdDate}</td>
                <td>${expectedDate}</td>
                <td>${item.text}</td>
                <td class="text-center">
                     <button type="button"
                        class="btn btn-sm btn-primary edit-deliverable"
                        data-id="${item.id ?? ""}"
                        data-tmp-id="${item._tmp_id ?? ""}"
                        data-text="${item.text}"
                        data-expected="${item.expected_date}"
                        data-date="${date}"
                    >✎</button>
                    <button type="button" class="btn btn-sm btn-danger delete-deliverable" data-id="${item.id ?? ""}" data-tmp-id="${item._tmp_id ?? ""}" data-date="${date}">✕</button>
                </td>
            </tr>
        `);
    });
}


// --------------- Deliverables ----------------
$(document).on("click", ".edit-deliverable", function () {
    let id = $(this).data("id");
    let text = $(this).data("text");
    let expected = $(this).data("expected");
    let date = $(this).data("date");

    $("#deliverable_id").val(id);
    $("#deliverable_tmp_id").val($(this).data("tmp-id") || "");
    $("#deliverable").val(text);
    $("#expected_date").val(expected);
    $("#deliverable_date").val(date);
    $("#deliverableModal").modal("show");
});

$(document).on("click", ".delete-deliverable", function () {
    let id    = $(this).data("id");
    let tmpId = $(this).data("tmp-id");
    let date  = $(this).data("date");

    removeDeliverable(id, tmpId, date);
});

function removeDeliverable(id, tmpId, date) {
    if (!deliverables[date]) return;

    // 🗑 DB record
    if (id) {
        deliverablesToDelete.push(id);
        deliverables[date] = deliverables[date].filter(
            d => String(d.id) !== String(id)
        );
    }
    // 🗑 TEMP record
    else if (tmpId) {
        deliverables[date] = deliverables[date].filter(
            d => String(d._tmp_id) !== String(tmpId)
        );
    }

    renderDeliverables(date);
}
