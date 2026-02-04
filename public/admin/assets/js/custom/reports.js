$(function () {
    let table = $("#consultingTable").DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('reports.consulting.data') }}",
            data: function (d) {
                d.date_range = $("#dateRange").val();
            },
        },
        columns: [
            { data: "DT_RowIndex", orderable: false, searchable: false },
            { data: "client" },
            { data: "objective" },
            { data: "expertise" },
            { data: "focus_area" },
            { data: "date" },
        ],
    });

    $("#dateRange").on("apply.daterangepicker", function () {
        table.ajax.reload();
    });
});
