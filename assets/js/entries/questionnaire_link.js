import runAllForms from './../sinervis/form-plugin-initialization';

$(document).ready(function () {
    reattachAutocompleteToFacilityField();
})

$(document).on('change', 'input#questionnaireflock_questionnaire_link_questionnaire', function () {
    const $facilityField = $('input#autocompleter_questionnaireflock_questionnaire_link_facility');
    $facilityField.closest('div.sv-entity-autocomplete').find('label.input input').val('');
    $facilityField.val('');

    reattachAutocompleteToFacilityField();
})

function reattachAutocompleteToFacilityField() {
    const questionnaireField = 'input#questionnaireflock_questionnaire_link_questionnaire'
    const $facilityField = $('input#autocompleter_questionnaireflock_questionnaire_link_facility');
    const questionnaire_id = $(questionnaireField).val() || null;

    $facilityField.attr('data-autocomplete-route-params', '{"questionnaire": '+ questionnaire_id +'}');
    $facilityField.removeClass('processed');
    $facilityField.autocomplete('destroy');
    runAllForms();
}
