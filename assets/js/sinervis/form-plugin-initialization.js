import 'bootstrap-colorpicker/dist/css/bootstrap-colorpicker.css';

import Routing from './components/sv_router';
import Inputmask from 'inputmask';

import $ from 'jquery';
import 'bootstrap';
import '../../css/sinervis/algolia-autocomplete.css';
import 'select2/dist/css/select2.min.css';
import 'autocomplete.js/dist/autocomplete.jquery';
import 'flatpickr';
import 'flatpickr/dist/l10n/it';
import 'bootstrap-colorpicker';
import 'chosen-js';
import 'raty-js';
import 'raty-js/lib/jquery.raty.css';
import './sv_popover_tooltip_initialization';
import 'jquery-ui/ui/widgets/draggable';
import 'select2';
import './plugins/EmojiRating/emoji';
import './plugins/EmojiRating/csshake.min.css';
import {TOOLTIP_DELAY} from "./sv_popover_tooltip_initialization";

/*
 * INITIALIZE FORMSunque
 * Description: Tooltip, Popover, Icheck, Multiselect, Select2, Masking, Datepicker, Autocomplete
 */
export default function runAllForms() {

    if ($.fn.dropdown) {
        $('[data-toggle="dropdown"]:not(.processed)').dropdown();
        $('[data-toggle="dropdown"]').addClass('processed');
    }

    if ($.fn.select2) {
        $('select.multiselect').each(function (e) {
            $(this).select2({
                // theme: 'bootstrap4'
            });
        })
    }

    Inputmask({regex: "[0-9]+:[0-5]{1}[0-9]{1}"}).mask('input.sv-duration-input:not([im-insert])');

    //lesson generator
    Inputmask("datetime", {
        inputFormat: "HH:MM",
        max: 24
    }).mask('input.inputmask-time:not([im-insert])');

    if ($.fn.flatpickr) {
        $('input.datepicker:not(.flatpickr-input)').flatpickr({
            locale: 'it',
            dateFormat: "d/m/Y",
            allowInput: true
        });

        $('input.datepicker-with-time:not(.flatpickr-input)').flatpickr({
            locale: 'it',
            dateFormat: "d/m/Y H:i",
            allowInput: true,
            enableTime: true,
        });
    }

    if ($.fn.draggable) {
        $('.modal-dialog').draggable({
            handle: ".modal-header"
        });
    }

    if ($.fn.colorpicker) {
        $('input.sv-color-picker:not(.colorpicker-element)').colorpicker();

        $('input.sv-color-picker').on('colorpickerChange', function(event) {
            $(this).css('background-color', event.color.toString());
        });

        $('input.sv-color-picker').each(function(e) {
            $(this).css('background-color', $(this).val());
        });

        $('tbody section.sv-color-picker').each(function(e) {
            $(this).css('background-color', ($.trim($(this).html())));
        });
    }

    if ($.fn.readonly) {
        // Make select read only.
        readonly('select[readonly]', true);
    }

    // Make select options disabled.
    $('select[readonly]:not([avoid-option-disable]) option:not(:selected)').prop('disabled', true);

    if ($.fn.autocomplete) {

        $('input.sv-autocomplete-input:not(.processed):not([readonly])').each(function(index) {
            let $current_item = $(this);
            let $sibling_item = $current_item.closest('div.sv-entity-autocomplete').find('label.input input');
            let $reset_button = $current_item.closest('div.sv-entity-autocomplete').find('.autocomplete-reset');

            if ($current_item.attr('disabled')) {
                $reset_button.remove();
            } else {
                $reset_button.on('click', event => {
                    if (!$current_item.attr('disabled')) {
                        $current_item.autocomplete('val', '');
                        $current_item.autocomplete('open');
                        $sibling_item.val(null).trigger('change');
                    }
                });
            }

            if ($current_item.attr('disabled')) {
                return;
            }

            let additional_parameters = {};

            const filtermode = $current_item.is("[data-autocomplete-filtermode]");

            let route_params = '';
            if ($current_item.attr('data-autocomplete-route-params') !== undefined) {
                route_params = JSON.parse($current_item.attr('data-autocomplete-route-params'));
            }
            if (route_params) {
                for (let [key, value] of Object.entries(route_params)) {
                    additional_parameters[key] = value;
                }
            }

            let qsSeparator = jQuery.isEmptyObject(additional_parameters) ? '?' : '&';

            let autocompleteUrl = Routing.generate($current_item.data('autocomplete-route'), additional_parameters)

            let autocompleteParams = $current_item.data('autocompleteParams');

            const AC = $current_item.autocomplete({
                hint: false,
                minLength: 0,
                // autoselectOnBlur: !filtermode,
                autoselect: true,
                openOnFocus: true,
                tabAutocomplete: true,
            }, [
                {
                    source: function(query, cb) {
                        $.ajax({
                            url: autocompleteUrl + qsSeparator + 'term=' + query + (filtermode ? '&filtermode=1' : ''),
                            data: autocompleteParams,
                            beforeSend: function() {
                                $current_item.addClass('ui-autocomplete-loading')
                            }
                        }).then(function(data) {
                            const results = data instanceof Object ? data : JSON.parse(data);
                            $current_item.removeClass('ui-autocomplete-loading')
                            cb(results)
                        })
                    },
                    debounce: 500,
                    cache: false
                }
            ]).on('autocomplete:selected', function(event, suggestion, dataset, context) {
                if ($sibling_item.length) {
                    $sibling_item.val(suggestion.id)
                    $sibling_item.trigger('change');
                }
            });

            $current_item.addClass('processed');

            // remove autocomplete value when the field is empty.
            $(document).on('blur, keyup', 'input.sv-autocomplete-input.processed', function() {
                if (!$.trim($(this).val()).length) {
                    var $sibling_item = $(this).closest('div').find('label.input input');
                    $sibling_item.val(null);
                    // fire change events bound to autocomplete field.
                    $sibling_item.change();
                }
            });

        });
    }


/*****************************
    if ($.fn.raty) {
        var emojis = [
            require('../../images/sinervis/raty/t_1.jpg'),
            require('../../images/sinervis/raty/t_2.jpg'),
            require('../../images/sinervis/raty/t_3.jpg'),
            require('../../images/sinervis/raty/t_4.jpg'),
            require('../../images/sinervis/raty/t_5.jpg'),
        ];

        $('div.rating-wrapper span.rating:not(.processed)').each(function(index) {
            const $current_item = $(this);
            const $skill_input = $current_item.closest('label').find('input');
            $skill_input.hide();

            let initial_rating = 0;

            if ($skill_input.val()) {
                initial_rating = $skill_input.val();
            } else {
                $skill_input.val(0);
            }
            $current_item.addClass('processed');

            $current_item.emoji({
                emojis: emojis,
                width: '24px',
                event: 'mouseover',
                UTF8: false,
                imgTitle: ['Basso', 'Scarso', 'Medio', 'Positivo', 'Molto Positivo'],
                value: initial_rating,
                opacity: 0.25,
                elementId: $skill_input.attr('id'),
                // debug: true
            });
        });

        $('div.readonly-reading-wrapper:not(.processed)').each(function(index) {
            $(this).emoji({
                emojis: emojis,
                width: '24px',
                UTF8: false,
                imgTitle: ['Basso', 'Scarso', 'Medio', 'Positivo', 'Molto Positivo'],
                value: $(this).data('selected-value'),
                opacity: 0.2,
                disabled: true
            });
        });
    }
/*****************************
    /*
     * BOOTSTRAP SLIDER PLUGIN
     * Usage:
     * Dependency: js/plugin/bootstrap-slider
     */
    if ($.fn.slider) {
        $('.slider').slider();
    }

    /*
     * SELECT2 PLUGIN
     * Usage:
     * Dependency: js/plugin/select2/
     */
    /*if ($.fn.select2) {
        $('select').each(function () {
            var $this = $(this);
            var width = $this.attr('data-select-width') || '100%';
            //, _showSearchInput = $this.attr('data-select-search') === 'true';
            $this.select2({
                theme: 'bootstrap4',
                //showSearchInput : _showSearchInput,
                allowClear: false,
                width: width
            })
        })
    }*/

    /**
     * https://harvesthq.github.io/chosen/
     * https://yarnpkg.com/package/chosen
     */
    if ($.fn.chosen) {
        $('select.js-select').chosen({
            disable_search_threshold: 20,
            width: '100%',
            placeholder_text_single: 'Seleziona un\'opzione',
            placeholder_text_multiple: 'Seleziona delle opzioni'
        });
    }

    /*
     * MASKING
     * Dependency: js/plugin/masked-input/
     */
    if ($.fn.mask) {
        $('[data-mask]').each(function() {

            const $this = $(this);
            const mask = $this.attr('data-mask') || 'error...',
                mask_placeholder = $this.attr('data-mask-placeholder') || 'X';

            $this.mask(mask, {
                placeholder: mask_placeholder
            });
        })
    }

    /*
     * Autocomplete
     * Dependency: js/jqui
     */
    // if ($.fn.autocomplete) {
    //     $('[data-autocomplete]').each(function () {
    //
    //         var $this = $(this);
    //         var availableTags = $this.data('autocomplete') || ["The", "Quick", "Brown", "Fox", "Jumps", "Over", "Three", "Lazy", "Dogs"];
    //
    //         $this.autocomplete({
    //             source: availableTags
    //         });
    //     })
    // }

    /*
     * JQUERY UI DATE
     * Dependency: js/libs/jquery-ui-1.10.3.min.js
     * Usage:
     */


    /*
     * AJAX BUTTON LOADING TEXT
     * Usage: <button type="button" data-loading-text="Loading..." class="btn btn-xs btn-default ajax-refresh"> .. </button>
     */
    // $('button[data-loading-text]').on('click', function () {
    //     var btn = $(this)
    //     btn.button('loading')
    //     setTimeout(function () {
    //         btn.button('reset')
    //     }, 3000)
    // });
}
