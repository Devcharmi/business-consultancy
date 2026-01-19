/* ============================================================
   AUTO LOAD STATES & CITIES (Country → State → City)
============================================================ */

/**
 * Load all States for a given country
 */
function loadStates(countryId, selectedState = null) {
    if ($("#country_id").length === 0 || !$("#country_id").data("states-url")) {
        return;
    }

    $("#state_id").html(`<option value="">Loading...</option>`);
    $("#city_id").html(`<option value="">Select City</option>`);

    if (!countryId) {
        $("#state_id").html(`<option value="">Select Country First</option>`);
        return;
    }

    let url = $("#country_id")
        .data("states-url")
        .replace(":countryId", countryId);

    $.ajax({
        url: url,
        type: "GET",
        dataType: "json",
        success: function (states) {
            let options = `<option value="">Select State</option>`;

            states.forEach((s) => {
                options += `<option value="${s.id}" ${
                    selectedState == s.id ? "selected" : ""
                }>${s.name}</option>`;
            });

            $("#state_id").html(options);

            // If editing → load cities also
            if (selectedState) {
                let selectedCity = $("#city_id").data("selected");
                loadCities(selectedState, selectedCity);
            }
        },
        error: function () {
            $("#state_id").html(
                `<option value="">Error loading states</option>`
            );
        },
    });
}

/**
 * Load all Cities for a given state
 */
function loadCities(stateId, selectedCity = null) {
    if ($("#state_id").length === 0 || !$("#state_id").data("cities-url")) {
        return;
    }

    $("#city_id").html(`<option value="">Loading...</option>`);

    if (!stateId) {
        $("#city_id").html(`<option value="">Select State First</option>`);
        return;
    }

    let url = $("#state_id").data("cities-url").replace(":stateId", stateId);

    $.ajax({
        url: url,
        type: "GET",
        dataType: "json",
        success: function (cities) {
            let options = `<option value="">Select City</option>`;

            cities.forEach((c) => {
                options += `<option value="${c.id}" ${
                    selectedCity == c.id ? "selected" : ""
                }>${c.city}</option>`;
            });

            $("#city_id").html(options);
        },
        error: function () {
            $("#city_id").html(
                `<option value="">Error loading cities</option>`
            );
        },
    });
}

/* ============================================================
   EVENT LISTENERS
============================================================ */

$(document).on("change", "#country_id", function () {
    loadStates($(this).val());
});

$(document).on("change", "#state_id", function () {
    loadCities($(this).val());
});

/* ============================================================
   AUTO INIT FOR DEFAULT COUNTRY (101) OR EDIT MODE
============================================================ */
$(document).ready(function () {
    if ($("#country_id").length === 0 || !$("#country_id").data("states-url")) {
        return;
    }

    let countryId = $("#country_id").val() || 101; // default India
    let selectedState = $("#state_id").data("selected") || null;

    // When opening create form → auto-load Indian states (ID = 101)
    // When editing → auto-load existing selected state & city
    if (countryId) {
        loadStates(countryId, selectedState);
    }
});
