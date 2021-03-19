import './../../css/sinervis/QuestionnaireFlock/sv_questionnaire.scss';
import './../sinervis/sv_form_helper';
import './../sinervis/_javascript_collection_widget'
import runAllForms from './../sinervis/form-plugin-initialization';

runAllForms();

import '../sinervis/QuestionnaireFlock/sv_question_edit';
import '../sinervis/QuestionnaireFlock/sv_questionnaire_edit';
import './../sinervis/QuestionnaireFlock/questionnaire_link_generator';

import '../sinervis/plugins/colourful_rating/js/script';

$('a.description-toggle').on('click', function (e) {
    e.preventDefault();
    if ($(this).hasClass('collapse')) {
        $(this).addClass('collapsed').removeClass('collapse');
        $(this).html('<i class="fas fa-angle-up"></i>')
    } else {
        $(this).addClass('collapse').removeClass('collapsed');
        $(this).html('<i class="fas fa-angle-down"></i>')
    }
    $(this).parent().next().find('.question-description').toggle('fast');
})