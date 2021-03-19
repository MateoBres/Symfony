import $ from "jquery";
import 'bootstrap';
import './../../sinervis/sv_jquery.stepy';
import runAllForms from './../../sinervis/form-plugin-initialization';
import applyAutocompleteToCityField from './../../sinervis/ContactFlock/taxCode';
import _geoCodeAddressComponentCompleter from './../../sinervis/components/_geoCodeAddressComponentCompleter';

import swal from "sweetalert2";

$(function() {
    new RoleFieldsetManager;
});

class RoleFieldsetManager {

    /* PROPERTIES */

    form = 'form#admin-form';
    originalRoles = 'form div.group-js-role-names';
    dropDownMenuContent = '.js-drop-down-menu-content';
    dropDownMenuInputs = 'div.role-toggle-button-wrapper div.js-role-names input';

    /* CONSTUCTOR */

    constructor() {
        this.populateRoleToggle();
        this.initializeRoleChangeEvent();
    }

    /* METHODS */

    populateRoleToggle() {
        const $originalRoles = $(this.originalRoles);
        const $clonedRoles = $originalRoles.clone(true, true);
        $(this.dropDownMenuContent).append($clonedRoles);
        $(this.dropDownMenuContent + ' input').each(function() {
            const id = $(this).attr('id');
            const newId = 'roleid_' + Math.floor(Math.random() * 99999);
            $(this)
                .attr('rel', id)
                .attr('id', newId)
                .removeAttr('name')
                .siblings('label')
                .attr('for', newId);
        })
    }

    initializeRoleChangeEvent() {
        $(document).on('change', this.dropDownMenuInputs, event => {
            const input = event.currentTarget;
            let roleLabel = $(input).closest('label').find('label.border-checkbox-label').find('span').text()
            if (!$(input).is(":checked")) {
                swal.fire({
                    title: "Sei sicuro?",
                    html: "Il ruolo <strong></strong> verrà cancellato",
                    type: "warning",
                    onBeforeOpen: () => {
                        swal.getContent().querySelector('strong')
                            .textContent = roleLabel
                    },
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Sì",
                    cancelButtonText: 'No',
                    imageWidth: '70',
                    imageHeight: '70',
                }).then((result) => {
                    if (result.value) {
                        $('form div.group-js-role-names').find('input#' + $(input).attr('rel')).prop('checked', false);
                        this.loadRoleForms($(input), 'unchecked');
                    } else {
                        $(input).prop('checked', true)
                    }
                });
            } else {
                $('form div.group-js-role-names').find('input#' + $(input).attr('rel')).prop('checked', true);
                this.loadRoleForms($(input), 'checked');
            }
        });
    }

    loadRoleForms($current_element, op) {

        let $form = $('form#admin-form').clone();
        let formName = $form.attr('name')
        $form.find('input[name="' + formName + '[_token]"]').remove()

        $.ajax({
            url: $form.attr('action'),
            type: $form.attr('method'),
            data: $form.serialize(),
            beforeSend: function() {
                $('div.se-pre-con').css('display', 'block');
            },
            complete: function() {
                $('div.se-pre-con').css('display', 'none');
            },
            success: html => {

                const $form = this.getValidationErrorRemovedForm($(html).find('form#admin-form'));

                window.location.hash = $current_element.val()
                $('div.stepy-tab').replaceWith($(html).find('div.stepy-tab'));
                $('form#admin-form').replaceWith($form);

                if ($('div.contact_type_choice input:checked').val() == 'p') {
                    applyAutocompleteToCityField();
                }

                runAllForms();
                this.populateRoleToggle();

                // todo: find a better solution for address copying
                $(document).on('autocomplete:selected', "input.geo-full-address", function(event, suggestion, dataset, context) {
                    _geoCodeAddressComponentCompleter($(this), suggestion.value);
                });
            }
        });
    }

    getValidationErrorRemovedForm($elements) {
        $elements.find('.state-error').removeClass('state-error');
        $elements.find('div.note.note-error').remove();
        return $elements;
    }
}


