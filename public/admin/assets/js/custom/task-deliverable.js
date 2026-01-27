$(document).on("click", ".open-deliverable-modal", function () {
    let date = $(this).data("date");

    $("#deliverable_form")[0].reset();
    $("#deliverable_id").val("");
    $("#deliverable_tmp_id").val("");
    $("#deliverable_date").val(date);
    $("#expected_date").val(date);

    $("#deliverableModal").modal("show");
});

$("#deliverable_form").on("submit", function (e) {
    e.preventDefault();

    let id = $("#deliverable_id").val(); // DB id
    let tmpId = $("#deliverable_tmp_id").val(); // TEMP id
    let date = $("#deliverable_date").val();
    let text = $("#deliverable").val().trim();
    let expected = $("#expected_date").val();

    if (!text) return;

    // ===============================
    // 🟢 EXISTING DB RECORD
    // ===============================
    if (id) {
        let row = $(`#deliverables_${date}`).find(`tr[data-id="${id}"]`);

        // Update table
        row.find("td:eq(1)").text(moment(expected).format("DD MMM YYYY"));
        row.find("td:eq(2)").contents().first()[0].textContent = text;

        // ✅ update hidden inputs
        row.find(`input[name="deliverables_existing[${id}][text]"]`).val(text);
        row.find(
            `input[name="deliverables_existing[${id}][expected_date]"]`,
        ).val(expected);

        // update edit button data
        row.find(".edit-deliverable").data("text", text).data("expected", expected);

        $("#deliverableModal").modal("hide");
        return;
    }

    // ===============================
    // 🟡 UPDATE EXISTING TEMP RECORD
    // ===============================
    if (tmpId) {
        let item = deliverables[date]?.find(
            (d) => String(d._tmp_id) === String(tmpId),
        );

        if (item) {
            item.text = text;
            item.expected_date = expected;

            let row = $(`#deliverables_${date}`).find(
                `tr[data-tmp-id="${tmpId}"]`,
            );

            row.find("td:eq(1)").text(moment(expected).format("DD MMM YYYY"));
            row.find("td:eq(2)").text(text);

            row.find(".edit-deliverable").data("text", text).data("expected", expected);

            $("#deliverableModal").modal("hide");
            return;
        }
    }

    // ===============================
    // ➕ NEW TEMP RECORD
    // ===============================
    deliverables[date] ??= [];
    deliverables[date].push({
        _tmp_id: Date.now(),
        text,
        expected_date: expected,
        created_at: date, // ✅ SAME DATE LOGIC
    });

    renderDeliverables(date);
    $("#deliverableModal").modal("hide");
});

function renderDeliverables(date) {
    let wrapper = $("#deliverables_" + date);
    let items = deliverables[date] ?? [];

    items.forEach((item) => {
        if (!item._tmp_id) return;
        if (wrapper.find(`tr[data-tmp-id="${item._tmp_id}"]`).length) return;

        let createdDate  = moment(item.created_at).format("DD MMM YYYY");
        let expectedView = moment(item.expected_date).format("DD MMM YYYY"); // 👀 UI only

        wrapper.append(`
            <tr data-tmp-id="${item._tmp_id}">
                <td>${createdDate}</td>
                <td>${expectedView}</td>
                <td>${item.text}</td>
                <td class="text-center">
                    <button
                        type="button"
                        class="btn btn-sm btn-primary edit-deliverable"
                        data-tmp-id="${item._tmp_id}"
                        data-text="${item.text}"
                        data-expected="${item.expected_date}" 
                        data-date="${date}"
                    >✎</button>

                    <button
                        type="button"
                        class="btn btn-sm btn-danger delete-deliverable"
                        data-tmp-id="${item._tmp_id}"
                        data-date="${date}"
                    >✕</button>
                </td>
            </tr>
        `);
    });
}

$(document).on("click", ".edit-deliverable", function () {
    $("#deliverable_id").val($(this).data("id") || "");
    $("#deliverable_tmp_id").val($(this).data("tmp-id") || "");

    $("#deliverable").val($(this).data("text"));
    $("#expected_date").val($(this).data("expected")); // ✅ YYYY-MM-DD
    $("#deliverable_date").val($(this).data("date"));

    $("#deliverableModal").modal("show");
});

$(document).on("click", ".delete-deliverable", function () {
    let id = $(this).data("id");
    let tmpId = $(this).data("tmp-id");
    let date = $(this).data("date");

    removeDeliverable(id, tmpId, date);
});

function removeDeliverable(id, tmpId, date) {
    if (id) {
        deliverablesToDelete.push(id);
        $(`#deliverables_${date}`).find(`tr[data-id="${id}"]`).remove();
        return;
    }

    if (tmpId) {
        deliverables[date] = deliverables[date].filter(
            (d) => String(d._tmp_id) !== String(tmpId),
        );

        $(`#deliverables_${date}`).find(`tr[data-tmp-id="${tmpId}"]`).remove();
    }
}
