/*
    Teacher SHow script
 */

import $ from "jquery";
import Routing from "../../components/sv_router";
import Swal from 'sweetalert2';

$(function() {
    new TeacherShow;
});

class TeacherShow {

    /**************/
    /* PROPERTIES */
    /**************/

    questionnaireInput = 'select.questionnaire-select';
    compileButton = '#compile-button';
    modal = '#choose-questionnaire-modal';

    /**************/
    /* CONSTUCTOR */
    /**************/

    constructor() {
        this.setupModal();
        this.initializeQuestionnaireInput();
        this.initializeCompileButton();
    }

    setupModal() {
        $(this.modal).on('show.bs.modal', event => {

            if ($.fn.chosen) {
                $(this.modal).find('select.js-select').chosen({
                    disable_search_threshold: 20,
                    width: '100%',
                    placeholder_text_single: 'Seleziona un\'opzione',
                    placeholder_text_multiple: 'Seleziona delle opzioni'
                });
            }

            this.checkIfQuestionnaireIsSelected();
        });
    }

    /***********/
    /* METHODS */
    /***********/

    initializeQuestionnaireInput() {
        $(document).off('change', this.questionnaireInput).on('change', this.questionnaireInput, event => {
            this.checkIfQuestionnaireIsSelected();
        })
    }

    initializeCompileButton() {
        $(document).off('click', this.compileButton).on('click', this.compileButton, event => {
            const questionnaireId = $(this.questionnaireInput).val();
            const teacherId = $(event.currentTarget).data('teacherId');
            if (questionnaireId && teacherId) {
                const questionnaireCreationUrl = Routing.generate(
                    'questionnaire_flock_teacher_assessment_completed_questionnaire_create',
                    {
                        'questionnaire': questionnaireId,
                        'teacher': teacherId
                    });
                window.location.href = questionnaireCreationUrl;
            } else {
                Swal.fire({
                    type: 'error',
                    title: 'Errore questionario',
                    text: 'Non è stato specificato il questionario!',
                })
            }
        });
    }

    checkIfQuestionnaireIsSelected() {
        if ($(this.questionnaireInput).val()) {
            $(this.compileButton).removeAttr('disabled');
        } else {
            $(this.compileButton).attr('disabled', true);
        }
    }
}
