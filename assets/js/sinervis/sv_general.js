import './plugins/lobibox/dist/css/lobibox.css';
import $ from 'jquery';
import swal from 'sweetalert2';
import 'core-js/index';
import './sv_popover_tooltip_initialization';
import {getFormattedHourMin, getMinutes} from "./components/duration_calculator";
import {StorageSettings} from "./components/storage-settings.js";

let Lobibox = require('./plugins/lobibox/dist/js/lobibox');

$(window).bind("load", function() {
    $(".se-pre-con").fadeOut("fast");
});

const storage = new StorageSettings();

$(document).ready(function() {

    initializeBlockTitleClickEvents();
    initializeBlockCategoryToggle();
    initializePersistentBlockCollapsingStatus();
    loadPageSettings();

    $('div.sv-flash-msgs div').each(function() {
        var flashMsg = $(this).html()
        Lobibox.notify($(this).data('msg-mode'), {
            size: flashMsg.length < 80 ? 'mini' : 'normal',
            position: 'top right',
            msg: flashMsg,
            delay: 4000,
            iconSource: 'fontAwesome',
            soundPath: '/build/sounds/'
        });
        $(this).remove();
    });

    // resides in form-plugin-initialization.js.
    // runAllForms();

    // Textare auto height
    function h(e) {
        $(e).css({
            'height': 'auto',
            'overflow-y': 'hidden',
            'min-height': '32px'
        }).height(e.scrollHeight);
    }

    $('textarea').each(function() {
        h(this);
    }).on('input', function() {
        h(this);
    });

    $(document).on('click', '.btn', function() {
        if ($(this).attr('data-toggle-slide')) {
            $('#' + $(this).data('toggle-slide')).slideToggle(function() {
                $(this).css('overflow', 'visible');
            });
            $("i.glyphicon", this).toggleClass("glyphicon-arrow-down glyphicon-arrow-up");
        }
    });

    //check if there are active filters
    if ($(".advanced-search-form").hasClass('open')) {
        //show advanced filters
        $(".advanced-search-form").show();
    }

    // Excel export related script.
    var $excel_export_submit = $('input[name=excel-export]');
    $excel_export_submit.css('display', 'none');

    $("div#excel-export").click(function() {
        if ($(location).attr('search').indexOf("?q=") == 0) {
            $('form#quick-search').submit();
        } else {
            $excel_export_submit.click();
        }
    });

    $("div#download-mass-pdfs").click(function() {
        window.location.href = $(this).attr('href');
    });

    $('form#quick-search button').click(function() {
        // remove name attribute from input#excel-export to avoid
        // submitting when searching for something.
        $('form#quick-search #excel-export').removeAttr('name');
    });


    /* Manage similar type coupled fields' data clonning and date restrictions. */
    $(document).on('change', '.coupled-start-field', function() {
        const cur_field = $(this).data('own-field-name');

        if (typeof $(this).attr('id') !== 'undefined') {
            const sibling_field_id = $(this).attr('id').replace(new RegExp(cur_field + '$'), $(this).data('sibling-field-name'));

            if ($(this).hasClass('datepicker')) {
                const prev_date = $(this).val().split("/");
                if ($('#' + sibling_field_id).hasClass('hasDatepicker')) {
                    $('#' + sibling_field_id).datepicker('option', {
                        minDate: new Date(prev_date[2], prev_date[1] - 1, prev_date[0])
                    }).datepicker("refresh");
                } else {
                    $('#' + sibling_field_id).datepicker({
                        prevText: '<i class="fa fa-chevron-left"></i>',
                        nextText: '<i class="fa fa-chevron-right"></i>',
                        minDate: new Date(prev_date[2], prev_date[1] - 1, prev_date[0])
                    });
                }
            } else if ($(this).hasClass('start-time') && !$.trim($('#' + sibling_field_id).val()).length) {
                let startTime = getMinutes($(this).val());
                if (startTime > 0) {
                    startTime = Math.clamp(startTime + 60, 0, 1440);
                    $('#' + sibling_field_id).val(getFormattedHourMin(startTime, 'string')).focus();
                }
            } else if (!$.trim($('#' + sibling_field_id).val()).length) {
                $('#' + sibling_field_id).val($(this).val());
            }
        }
    });

    $(document).on('click', 'a.ask-confirmation-before-proceeding', function(e) {

        e.preventDefault();
        const $clicked_item = $(this);

        swal({
            title: $clicked_item.data('confirm-msg-title') !== undefined ? $clicked_item.data('confirm-msg-title') : "Sei sicuro?",
            text: $clicked_item.data('confirm-msg'),
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Sì",
            cancelButtonText: 'No',
            // closeOnConfirm: true,
            imageWidth: '70',
            imageHeight: '70',
        }).then((result) => {
            if (result.value) {
                window.location = $clicked_item.attr('href')
            }
        });
    });

    // Vich file field.
    $(document).on('click', 'div.real-file-field div.file-remove .delete-file', function(e) {

        e.preventDefault();
        var clicked_item = $(this);

        swal({
            title: "Sei sicuro?",
            text: "Questo file verrà cancellato!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Sì",
            cancelButtonText: 'No',
            // closeOnConfirm: true,
            imageWidth: '70',
            imageHeight: '70',
        }).then((result) => {
            if (result.value) {
                clicked_item.parents('.file-remove').find('input[type="checkbox"]').attr('checked', true);
                clicked_item.parents('.real-file-field').find('.file-already-uploaded').removeClass('file-already-uploaded');
                clicked_item.parents('.real-file-field').find('.file-remove').css('display', 'none');
            }
        });
    });


    // toggle invoice detail in role forms (e.g. Customer)
    $('input.invoice-data-hide').closest('.form-group').addClass('invoice-data-hide');

    readonly_jrating_init();

});

// Fake file upload field
$(document).on('click', 'div.sv-fake-file-group input, div.sv-fake-file-group button', function() {
    $(this).closest('div.sv-file-wrapper').find('label input').click();
});

$(document).on('change', 'input[type=file]', function() {
    var file_name = $.trim($(this).val());
    file_name = file_name.replace(/^.*\\/, "");
    file_name = file_name.length ? file_name : 'Nessun file selezionato';
    $(this).closest('div.sv-file-wrapper').find('div.sv-fake-file-group input').val(file_name);
});

$(document).on('click', 'td.actions a.delete-entity, .page-actions a.delete-entity', function(e) {

    if ($(this).hasClass('processed')) {
        return true;
    }

    e.preventDefault();
    var clicked_item = $(this);

    swal({
        title: "Conferma cancellazione?",
        text: "Attenzione, cancellerà l'elemento selezionato!",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "Sì",
        cancelButtonText: 'No',
        // closeOnConfirm: true,
        imageWidth: '70',
        imageHeight: '70',
    }).then((result) => {
        if (result.value) {
            window.location.href = clicked_item.attr('href');
        }
    });
});

if ($(".custom-bar-chart")) {
    $(".bar").each(function() {
        var i = $(this).find(".value").html();
        $(this).find(".value").html("");
        $(this).find(".value").animate({
            height: i
        }, 2000);
    });
}


function readonly_jrating_init() {
    $('div.readonly-jrating:not(.processed) ').each(function(index) {

        const $current_item = $(this).addClass('processed');

        $current_item.jRate({
            rating: $(this).data('selected-rating'),
            strokeColor: 'black',
            precision: 1,
            count: 10,
            min: 0,
            max: 10,
            shape: 'BULB',
            readOnly: true,
            minSelected: 1,
            startColor: '#ffb266',
            endColor: '#FF7800',
            backgroundColor: '#ccc',
        });
    });
}

function decimalfy(num, str) {
    if (typeof num !== 'undefined' && num != '') {
        num = $.isNumeric(num) ? num : num.replace(',', '.');
        num = parseFloat(num);
        num = num.toFixed(2);

        return str == true ? num.replace('.', ',') : num;
    } else {
        return str == true ? '0,00' : 0;
    }
}


function initializeBlockTitleClickEvents() {
    $('.ibox-title').off('click').on('click', event => {
        if ($(event.target).hasClass('ibox-title')) {
            $(event.target).find('.collapse-link').click();
        }
    });
}

function loadPageSettings() {
    loadBlockCollapseSettings();
    loadBlockCategoryToggleSettings();
}

function initializePersistentBlockCollapsingStatus() {
    $(document).on('click', '.collapse-link', function(e) {
        const $block = $(this).closest('.ibox');
        const $content = $block.children('.ibox-content');
        storage.setPageElementPropertyInCategory(window.location.pathname, 'collapse', $block.attr('id'), $content.is(':visible') ? 1 : 0);
    });
}

function initializeBlockCategoryToggle() {
    $('.action-toggle-elements').off('click').on('click', function(event) {
        event.preventDefault();
        const selector = $(this).data('selector');
        const active = $(this).hasClass('active');

        $(selector).each(function() {
            $(this).animate({
                height: 'toggle',
                opacity: 'toggle'
            }, 'fast');
        });

        $(this).toggleClass('active');
        $(this).attr('aria-pressed', active ? 'off' : 'on');
        $(this).attr('title', active ? 'Mostra i blocchi' : 'Nascondi i blocchi');

        storage.setPageElementPropertyInCategory(window.location.pathname, 'filter', $(this).attr('id'), active ? 0 : 1);

        return false;
    });
}

function loadBlockCollapseSettings() {
    const pageSettings = storage.getPageSettings(window.location.pathname);

    for (const id in pageSettings.collapse) {
        if (pageSettings.collapse[id] === 1) {
            collapseBlock(id);
        }
    }
}

function loadBlockCategoryToggleSettings() {
    const pageSettings = storage.getPageSettings(window.location.pathname);

    for (const id in pageSettings.filter) {
        if (pageSettings.filter[id] == 0) {
            const $button = $('#' + id);
            const selector = $button.data('selector');
            $(selector).hide();
            $button.removeClass('active');
            $button.attr('aria-pressed', 'off');
            $button.attr('title', 'Mostra i blocchi');
        }
    }
}

// fa circa la stessa cosa del pulsante collapse-link in inspinia.js
function collapseBlock(id) {
    const $block = $('#' + id);
    const $button = $block.find('.collapse-link').find('i');
    const $content = $block.children('.ibox-content');
    $content.hide();
    $button.toggleClass('fa-chevron-up').toggleClass('fa-chevron-down');
    $block.toggleClass('').toggleClass('border-bottom');
}




