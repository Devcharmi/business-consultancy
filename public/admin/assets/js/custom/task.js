var task_table = $(".table-list").DataTable({
    order: [[0, "desc"]],
    autoWidth: false,
    processing: true,
    serverSide: true,
    serverMethod: "GET",
    lengthMenu: [
        [25, 100, 200, 250],
        [25, 100, 200, 250],
    ],
    ajax: {
        url: $("#task_table").attr("data-url"),
    },
    columns: [
        // 1️⃣ Sr No
        {
            data: "id",
            render: function (data, type, row, meta) {
                return meta.row + meta.settings._iDisplayStart + 1;
            },
        },

        // 2️⃣ Client Name
        {
            data: "client_objective",
            render: function (data, type, row) {
                return data && data.client ? data.client.client_name : "-";
            },
        },

        // 3️⃣ Objective
        {
            data: "client_objective",
            render: function (data, type, row) {
                return data && data.objective_manager
                    ? data.objective_manager.name
                    : "-";
            },
        },

        // 4️⃣ Expertise
        {
            data: "expertise_manager",
            render: function (data) {
                return data ? data.name : "-";
            },
        },

        // 6️⃣ Date
        {
            data: "task_due_date",
            render: function (data) {
                return data ? moment(data).format("DD-MM-YYYY HH:mm") : "-";
            },
        },
        {
            data: "status_manager",
            mRender: function (v, t, o) {
                if (o.status_manager) {
                    var name = o.status_manager.name;
                    var color = o.status_manager.color_name || "gray"; // fallback color
                    return `<span style="background-color:${color}; color:#fff; padding:2px 6px; border-radius:4px;">${name}</span>`;
                }
                return "N/A";
            },
        },
        // 7️⃣ Action
        {
            data: "id",
            orderable: false,
            searchable: false,
            className: "text-center",
            render: function (id, type, row) {
                let edit_path_set = edit_path.replace(":task", id);
                let delete_path_set = delete_path.replace(":task", id);

                let editDisabled = window.canEditTask
                    ? ""
                    : "style='pointer-events:none;opacity:0.4;' disabled";
                let deleteDisabled = window.canDeleteTask
                    ? ""
                    : "style='pointer-events:none;opacity:0.4;' disabled";

                return `
                    <a href="${edit_path_set}"
                       class="open-modal" title="Edit" ${editDisabled}>
                        <i class="fas fa-pen p-1 text-primary"></i>
                    </a>
                    <a href="javascript:void(0);" data-url="${delete_path_set}"
                       class="delete-data" title="Delete" ${deleteDisabled}>
                        <i class="fas fa-trash p-1 text-danger"></i>
                    </a>
                `;
            },
        },
    ],
    language: {
        searchPlaceholder: "Search...",
        sSearch: "",
        lengthMenu: "_MENU_&nbsp; items/page",
    },
});

// $(document).on("click", ".open-modal", function () {
//     $.ajax({
//         url: $(this).attr("data-url"),
//         type: "GET",
//         dataType: "json",
//         success: function (data) {
//             // alert(data.html);
//             $("#modal_show_html").html(data.html);
//             $("#taskForm").modal("show");
//             $(".select2").select2({
//                 placeholder: "Select...",
//                 width: "100%",
//                 dropdownParent: $("#taskForm"),
//                 // allowClear: true,
//                 // closeOnSelect: false, // keep dropdown open for multiple selections
//             });
//             initAllCKEditors([
//                 "content",
//             ]);
//         },
//     });
//     return false;
// });

$(document).on("click", "#task_form_button", function () {
    $("<input>")
        .attr({
            type: "hidden",
            name: "commitments",
            value: JSON.stringify(commitments),
        })
        .appendTo(this);

    $("<input>")
        .attr({
            type: "hidden",
            name: "deliverables",
            value: JSON.stringify(deliverables),
        })
        .appendTo(this);

    let form = $("#task_form");
    let url = $(this).attr("data-url");
    let method = form.find("input[name='_method']").length ? "PUT" : "POST"; // Detect if it's an update
    $("#task_form").ajaxSubmit({
        url: url,
        type: method,
        dataType: "json",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        beforeSubmit: function () {},
        success: function (result) {
            task_table.draw();
            $("#taskForm").modal("hide");
            showToastr("success", result.message);
        },
        error: function (result) {
            $("[id$='_error']").empty();
            showToastr("error", result.responseJSON.message);
            $.each(result.responseJSON.errors, function (k, v) {
                var id_arr = k.split(".");
                $("body")
                    .find("#task_form")
                    .find("#" + id_arr[0] + "_error")
                    .text(v);
            });
        },
    });
    return false;
});

$(document).on("click", ".delete-data", function () {
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
                    task_table.draw();
                    showToastr("success", result.message);
                },
                error: function (result) {
                    showToastr("error", result.responseJSON.message);
                },
            });
        }
    });
});

$(document).on("click", ".open-commitment-modal", function () {
    let date = $(this).data("date");

    $("#commitment_due_date").val(date);
    $("#commitment_date").val(date);

    $("#commitmentModal").modal("show");
});

$("#commitment_form").on("submit", function (e) {
    e.preventDefault();
    e.stopImmediatePropagation(); // ✅ prevent other listeners from firing

    let date = $("#commitment_due_date").val();
    let text = $("#commitment").val();

    if (!date || !text) {
        alert("Please select date and enter commitment");
        return;
    }

    commitments[date] ??= [];
    commitments[date].push({ text });

    renderCommitments(date);

    $("#commitmentModal").modal("hide");
    this.reset();
});

function renderCommitments(date) {
    let wrapper = $("#commitments_" + date);
    wrapper.html("");

    (commitments[date] ?? []).forEach((item, index) => {
        wrapper.append(`
            <div class="d-flex mb-2">
                <input class="form-control me-2" readonly value="${item.text}">
                <button type="button" class="btn btn-sm btn-danger"
                    onclick="removeCommitment('${date}', ${index})">✕</button>
            </div>
        `);
    });
}

function renderCommitments(date) {
    let wrapper = $("#commitments_" + date);
    wrapper.html("");

    let formattedDate = moment(date).format("DD MMM YYYY"); // simple format

    (commitments[date] ?? []).forEach((item, index) => {
        wrapper.append(`
            <div class="d-flex mb-2 align-items-center">
                <span class="me-2 fw-bold">${formattedDate}:</span>
                <input class="form-control me-2" readonly value="${item.text}">
                <button type="button" class="btn btn-sm btn-danger"
                    onclick="removeCommitment('${date}', ${index})">✕</button>
            </div>
        `);
    });
}


function removeCommitment(date, index) {
    commitments[date].splice(index, 1);
    renderCommitments(date);
}

$(document).on("click", ".open-deliverable-modal", function () {
    let date = $(this).data("date");

    $("#deliverable_date").val(date);

    $("#deliverableModal").modal("show");
});

$("#deliverable_form").on("submit", function (e) {
    e.preventDefault();

    let date = $("#deliverable_date").val();
    let text = $("#deliverable").val();

    if (!date || !text) {
        alert("Please select date and enter deliverable");
        return;
    }

    deliverables[date] ??= [];
    deliverables[date].push({ text });

    renderDeliverables(date);

    $("#deliverableModal").modal("hide");
    this.reset();
});

function renderDeliverables(date) {
    let wrapper = $("#deliverables_" + date);
    wrapper.html("");

    (deliverables[date] ?? []).forEach((item, index) => {
        wrapper.append(`
            <div class="d-flex mb-2">
                <input class="form-control me-2" readonly value="${item.text}">
                <button type="button" class="btn btn-sm btn-danger"
                    onclick="removeDeliverable('${date}', ${index})">✕</button>
            </div>
        `);
    });
}

function removeDeliverable(date, index) {
    deliverables[date].splice(index, 1);
    renderDeliverables(date);
}
