$(document).on("shown.bs.tab", 'button[data-bs-toggle="tab"]', function (e) {
    let activeTab = $(e.target).attr("data-bs-target");
    localStorage.setItem("dashboard_active_tab", activeTab);
});

$(document).ready(function () {
    let lastTab = localStorage.getItem("dashboard_active_tab");

    if (lastTab) {
        let tabTrigger = document.querySelector(
            `button[data-bs-toggle="tab"][data-bs-target="${lastTab}"]`,
        );

        if (tabTrigger) {
            new bootstrap.Tab(tabTrigger).show();
        }
    }

    const params = new URLSearchParams(window.location.search);
    const dateRange = params.get("date_range");

    if (dateRange) {
        $("#dateRange").val(dateRange);
    }
});

$(document).on("change", "#dateRange", function () {
    let dateRange = $(this).val();

    let url = new URL(window.location.href);
    url.searchParams.set("date_range", dateRange);

    window.location.href = url.toString();
});

// Reset Filters Button
$(document).on("click", "#resetFilters", function () {
    let url = new URL(window.location.href);
    url.searchParams.delete("date_range");
    window.location.href = url.toString();
});

$(document).ready(function () {
    $(document).on("click", ".calendar-add-btn", function () {
        const date = $(this).data("date");
        const url = $(this).data("url");

        $("#sub_modal_show_html").html(
            '<div class="text-center p-5"><i class="bi bi-hourglass-split fs-1"></i><p>Loading...</p></div>',
        );

        $.ajax({
            url: url,
            method: "GET",
            dataType: "json",
            success: function (response) {
                $("#sub_modal_show_html").html(response.html);

                const dateInput = $("#consulting_date");
                if (dateInput.length) {
                    dateInput.val(date);
                }

                if ($.fn.select2) {
                    $(".select2").select2({
                        placeholder: "Select...",
                        width: "100%",
                        dropdownParent: $("#consultingModal"),
                        allowClear: true,
                    });
                }

                $("#consultingModal").modal("show");
            },
            error: function () {
                showToastr("error", "Error loading form");
            },
        });
    });
});

// $(document).on("click", ".calendar-dot", function (e) {
//     e.stopPropagation();
// });

$(document).on("click", ".calendar-day", function () {
    const date = $(this).data("date");
    if (!date) return;

    $.ajax({
        url: routeDayConsultings,
        type: "GET",
        data: { date },
        success: function (html) {
            $("#modal_show_html").html(html);
            $("#dayConsultingModal").modal("show");
        },
        error: function () {
            $("#dayConsultingBody").html(
                '<div class="text-danger text-center py-4">Failed to load consultings</div>',
            );
        },
    });
});

$(document).ready(function () {
    let activeTabId = localStorage.getItem("dashboardActiveTab");

    if (activeTabId) {
        let tabBtn = document.getElementById(activeTabId);

        if (tabBtn) {
            new bootstrap.Tab(tabBtn).show();
        }

        localStorage.removeItem("dashboardActiveTab");
    }
});

$(document).on("click", ".mark-completed", function () {
    let activeTabId = $(".nav-tabs .nav-link.active").attr("id");

    $.post(
        routeUpdateStatue,
        {
            _token: csrf_token,
            type: $(this).data("type"), // followup | task
            id: $(this).data("id"),
            status: "completed",
        },
        function () {
            // remember active tab
            localStorage.setItem("dashboardActiveTab", activeTabId);

            // reload full page
            location.reload();
        },
    );
});

// Reset Filters Button
$(document).on("click", "#resetFilters", function () {
    // Clear all filter dropdowns
    $(".applyFilters").val("").trigger("change");
});
