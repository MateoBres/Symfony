// Adds autocomplete feature to dynamically created fields.
import $ from "jquery";
import 'autocomplete.js/dist/autocomplete.jquery';
import _geoCodeAddressComponentCompleter from './_geoCodeAddressComponentCompleter';

// Address picker - toggle address components
$(document).on('click', '.addresspicker-button', function () {
    $(this).toggleClass('fa-chevron-down').toggleClass('fa-chevron-up');
    let $addressComponents = $(this).closest('div.form-group').siblings('.crouch');
    $addressComponents.slideToggle('slow').toggleClass('fade');
    $addressComponents.css('display','inline-block')
});


$(document).on('autocomplete:selected', "input.geo-full-address", function(event, suggestion, dataset, context) {
    _geoCodeAddressComponentCompleter($(this), suggestion.value);
});

