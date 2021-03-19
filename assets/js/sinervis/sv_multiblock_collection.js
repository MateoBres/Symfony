import {areLessonsInfoValid, getEditionTrackClass, assignLessonSequenceNum} from './CourseFlock/CourseEdition/sv_course_edition';

var mbc_selector = '.mbc-selector';
var mbc_collection = '.form-group.mbc-collection';
var mbc_collection_block = '.card.mbc-collection-block';
var mbc_selector_block = '.card.mbc-selector-block';
var $modifying_cycle = null;
var $closest_action_buttons = null;
var $page_action_buttons = $('div.page-actions a.btn');


$(document).on('click', mbc_selector, function(e) {
    $closest_action_buttons = $(this).closest('table').closest('tr').find('div.action-button-wrapper a');
    if ($(this).hasClass('focus')) {
        deactivateCycleModification(true);
    } else {
        var deactivation_succeeded = deactivateCycleModification();
        if (deactivation_succeeded) {
            activateCycleModification($(this));
        }
    }
});

$(document).on('click', '.collection-widget-edit, .collection-widget-save-modification, .collection-widget-cancel-modification', function(e) {
    deactivateCycleModification();
});

$(document).on('click', 'div.mbc-selector-block div.mbc-record a.delete, div.mbc-selector-block div.group-mbc-record a.delete', function() {
    //e.preventDefault();
    var clicked_item = $(this);

    swal({
        title: "Sei sicuro?",
        text: "Questa riga verrà cancellata!",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "Sì",
        cancelButtonText: 'No',
        closeOnConfirm: true,
        imageSize: '70x70',
    }, function() {
        const $tr = clicked_item.closest('tr');
        const closest_data_rep_table = $tr.closest('table');
        $(document).trigger('preTrackRemove', [$tr]);
        $('tr#' + $tr.attr('id')).remove();
        $tr.remove();
        $(document).trigger('postTrackRemove', closest_data_rep_table);
        $('div.mbc-collection-block div.panel-body').html('');
        $page_action_buttons.attr('disabled', false);
        deactivateCycleModification();
    });
});


function activateCycleModification($selectedElement) {
    $closest_action_buttons.addClass('disabled');
    $page_action_buttons.addClass('disabled');
    $selectedElement.addClass('focus fa-chevron-left').removeClass('fa-chevron-right');
    $selectedElement.closest('tr').addClass('mbc-focus');
    $modifying_cycle = $selectedElement.closest('tr');

    var modules_content = $selectedElement.closest('td').find(mbc_collection);
    //show("slide", { direction: "left" }, 1000);

    $(mbc_collection_block + ' div.panel-body').append(modules_content).show("slow");
    //$('div.panel-block-courseModules div.panel-body').animate({left: '100px', top: '100px'},1500);
}

export function deactivateCycleModification(is_display_toggle) {
    // below function resides in sv_course_edition.js
    if (areLessonsInfoValid()) {
        if (is_display_toggle) {
            $(mbc_collection_block + ' div.panel-body').hide("slow");
            $(mbc_selector).removeClass('focus fa-chevron-left').addClass('fa-chevron-right');
            //$modifying_cycle.removeClass('mbc-focus');

            executeCycleModificationDeactivateTasks();
            //isLastCycleComplete(); // function resides in sv_course_edition.js
            assignLessonSequenceNum(); // function resides in sv_course_edition.js

        } else {
            $(mbc_collection_block + ' div.panel-body').css('display', 'none');
            executeCycleModificationDeactivateTasks();
        }

        // $closest_action_buttons.removeClass('disabled');
        $page_action_buttons.removeClass('disabled');

        return true;
    }

    return false;
}

function executeCycleModificationDeactivateTasks() {
    if ($modifying_cycle) {

        $(mbc_selector).removeClass('focus fa-chevron-left').addClass('fa-chevron-right');
        $modifying_cycle.removeClass('mbc-focus');
        var modules_content = $(mbc_collection_block + ' div.panel-body').find(mbc_collection);

        $modifying_cycle.find('> td.data-td').append(modules_content);

        $modifying_cycle = null;
        $(mbc_collection_block).css({'margin-top': '0px'});

        // below function resides in sv_lesson_repeater.js
        //startDateEndDateSyncronizer();
    }
}