import {getFormMethod} from "../sinervis/components/get_form_method";
import "../../css/sinervis/UserFlock/user_form.scss";

$(function () {
    sortPermissions();
    toggleQuestionnaireSubCheckboxes();
});

$(document).on('change', '.group-quiz-permissions input', function () {
    if($(this).val() == 'security_fill_checklist' || $(this).val() == 'quality_fill_checklist') {
        const $subInputs = $(this).closest('.permission-group').find('.sub-group input');
        if ($(this).is(':checked')) {
            $subInputs.prop('checked', false).attr("disabled", true);
        } else {
            $subInputs.attr("disabled", false);
        }
    }
})

$(document).on('change', '.user-roles input', function() {
    const $form = $('form');

    const data = {};
    collectFormData(data);

    $.ajax({
        url: $form.attr('action'),
        method: getFormMethod($form),
        data: data,
        success: html => {
            $('div.panel-block-permissions').replaceWith(
                $(html).find('div.panel-block-permissions')
            );
            sortPermissions();
        }
    });
})

function collectFormData(data) {
    const $roleField = $('.user-roles input');
    data[$roleField.attr('name')] = [];
    $('.user-roles input[type=checkbox]:checked').each(function() {
        data[$roleField.attr('name')].push(this.value);
    });

    data[$('#userflock_user_username').attr('name')] = '';
}

function sortPermissions() {
    $('div.quiz-permissions').append('' +
        '<div class="other permission-group"><div class="main-group"></div></div>' +
        '<div class="security permission-group"><div class="main-group"></div><div class="sub-group"><spna>Scegli checklist</spna></div></div>' +
        '<div class="quality permission-group"><div class="main-group"></div><div class="sub-group"><spna>Scegli questionari</spna></div></div>' +
        '');
    $('div.quiz-permissions > label').each(function () {
        if ($(this).hasClass('security-group')) {
            if ($('div.quiz-permissions div.security div.main-group > label').length <= 3) {
                $('div.quiz-permissions div.security div.main-group').append($(this));
            } else {
                $('div.quiz-permissions div.security div.sub-group').append($(this));
            }
        } else if ($(this).hasClass('quality-group')) {
            if ($('div.quiz-permissions div.quality div.main-group > label').length <= 3) {
                $('div.quiz-permissions div.quality div.main-group').append($(this));
            } else {
                $('div.quiz-permissions div.quality div.sub-group').append($(this));
            }
        } else {
            $('div.quiz-permissions div.other div.main-group').append($(this));
        }
    });
}

function toggleQuestionnaireSubCheckboxes() {
    const $mainCheckboxes = $('input[value="security_fill_checklist"], input[value="quality_fill_checklist"]');
    $mainCheckboxes.each(function () {
        if($(this).val() == 'security_fill_checklist' || $(this).val() == 'quality_fill_checklist') {
            const $subInputs = $(this).closest('.permission-group').find('.sub-group input');
            if ($(this).is(':checked')) {
                $subInputs.prop('checked', false).attr("disabled", true);
            } else {
                $subInputs.attr("disabled", false);
            }
        }
    });
}