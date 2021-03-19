import runAllForms from './form-plugin-initialization';

//$( "table.data-representation-table.sortable tbody" ).sortable();
//$( "table.data-representation-table tbody" ).disableSelection();
var $backup_tracks = [];

$('table.data-representation-table tbody td section').each(function(e) {
    if ($(this).height() > 20 ) {
        $(this).addClass('overflowing').attr('title', $(this).html());
    }
});


$(document).ready(function(){
    $('label.state-error, div.state-error').each(function() {
        _widget_error_handler($(this), $(this).closest('div.panel-body').attr('id'));
    });


    // **********************************************************************//
    // START - EDIT                                                          //
    // **********************************************************************//
    $(document).on('click', 'table.data-representation-table a.collection-widget-edit', function(e) {
        e.preventDefault();
        var id = $(this).attr('id'); // The 'edit' anchor has the same id as its parent tr.

        var curDataRepresentationTable = $(this).closest('table');
        var relevantFormDataTableId = curDataRepresentationTable.attr('target_table');

        if ($(this).hasClass('editing')) {
            $(this).removeClass('editing');
            $('table#'+ relevantFormDataTableId +' tr#' + id+' a.collection-widget-cancel-modification').click();
        }
        else {
            curDataRepresentationTable.find('a.editing').removeClass('editing');
            $(this).addClass('editing');

            // Cancel all the other pending modifications before starting the
            // current modification.
            $('table#'+ relevantFormDataTableId +' tr.currently-editing a.collection-widget-cancel-modification').click();

            // First remove 'currently-editing' class from all the tracks
            // and add it to the currently modifying track.
            curDataRepresentationTable.find('tr').removeClass('currently-editing');
            $(this).closest('tr').addClass('currently-editing');

            // Makes a copy of the track being edited.
            var editing_track = $('table#'+ relevantFormDataTableId +' tr#' + id).clone();
            // Removal of the two classes is necessary to reactivate ui-autocomplete after restoration.
            //editing_track.find('input[name^="autocompleter_"]').removeClass('ui-autocomplete-input existing-dependent-places');
            editing_track.find('input.sv-autocomplete-input').removeClass('processed existing-dependent-places');
            // Removal of 'hasDatepicker' is necessary to reactivate datepicker after restoration.
            editing_track.find('input.datepicker').removeClass('hasDatepicker');
            editing_track.find('span.ui-helper-hidden-accessible').remove();
            // Save the editing track  in order to restore when necessary. E.g. cancel modifications.
            $backup_tracks[id] = editing_track;

            // First hide all the tracks of the form data table (including the track contains 'Aggiungi') and
            // then display only the track needs to be modified.
            $('table#'+ relevantFormDataTableId +' > tbody > tr').addClass('tmp-display-none');
            $('table#'+ relevantFormDataTableId +' > tfoot > tr').addClass('tmp-display-none');

            $('table#'+ relevantFormDataTableId +' tr#' + id + ' select.multiselect').each(function() {
                $(this).multiselect();
                var multiselect_item = $(this);
                $(this).closest('div.multiselect-wrapper').find('div.btn-group').each(function(index) {
                    if (index != 0) {
                        $(this).remove();
                    }
                });

            });
            $('table#'+ relevantFormDataTableId +' tr#' + id).addClass('currently-editing').removeClass('tmp-display-none');
            $(document).trigger('formDataTableRecordIsBeingEdited', id);
        }
    });
    // END - EDIT
    // ----------------------------------------------------------------------


    // **********************************************************************//
    //                      START - CANCEL MODIFICATION                      //
    // **********************************************************************//
    $(document).on('click', 'table.form-data-table a.collection-widget-cancel-modification', function(e) {
        //console.log($backup_tracks);
        e.preventDefault();
        var closest_tr = $(this).closest('tr');
        closest_tr.removeClass('currently-editing');
        var $collection_widget_wrapper = closest_tr.closest('div.collection-widget-wrapper');

        // check if the tr is new entry.
        if ($(this).hasClass('new-collection-entry')) {
            $(this).closest('tr.new-entry').remove();
        }
        else {

            closest_tr.addClass('tmp-display-none');

            // get the relevant data representation table
            // (there might be more than one data representation tables).
            var $data_representation_table = $collection_widget_wrapper.find('table.data-representation-table');

            $data_representation_table.find('tr.currently-editing').removeClass('currently-editing');
            $data_representation_table.find('a.editing').removeClass('editing');

            // restore tr as the modification is cancelled.
            //console.log(closest_tr);
            //console.log($backup_tracks[closest_tr.attr('id')]);
            closest_tr.replaceWith($backup_tracks[closest_tr.attr('id')]);

        }
        // Display 'aggiunti' button
        $collection_widget_wrapper.find('tr.add-new-item').removeClass('tmp-display-none');
        runAllForms();
    });

    // END - CANCEL MODIFICATION
    // ----------------------------------------------------------------------


    //**********************************************************************//
    //                      START - SAVE MODIFICATION                       //
    //**********************************************************************//
    $(document).on('click', 'table.form-data-table a.collection-widget-save-modification', function(e) {
        e.preventDefault();
        var $new_entry = false;

        $(this).closest('td').find('table.form-data-table:first tr.currently-editing a.collection-widget-save-modification, table.form-data-table:first tr.new-entry a.collection-widget-save-modification').click();

        var $data_representation_table = $(this).closest('div.collection-widget-wrapper').find('table.data-representation-table:first');
        //console.log($data_representation_table);
        $data_representation_table.find('a.editing').removeClass('editing');
        $data_representation_table.find('tr.currently-editing').removeClass('currently-editing');
        var index = $(this).closest('tr').attr('index');

        var modified_tr = $(this).closest('tr');
        var modified_elements = modified_tr.children('td').children('.form-group');

        // This is to check if annoying 'contact' form has been used as a polycollection
        if(modified_elements.length == 0) {
            var modified_elements = modified_tr.children('td').children('div.row').children('div.col').children('.form-group');
        }
        // This condition is needed to detect newly added record modifications.
        if(modified_elements.length == 0) {
            modified_elements = modified_tr.children('td').children('div').children('.form-group');
        }
        if(modified_tr.children('td').find('.panel-block-contact').length) {
            var modified_elements = modified_tr.children('td').find('.panel-block-contact > .panel-body').children('.form-group');
        }

        if ($(this).hasClass('new-collection-entry')) {
            $new_entry = true;
            //console.log('new');
            var dummy_track = $data_representation_table.find('tfoot').html();
            dummy_track = dummy_track.replace(/_ID_/g, index).replace('new-collection-entry', '').replace('dummy-track', '');

            var track_id = $(dummy_track).attr('id');
            // Remove 'new-collection-entry' class from the save button to avoid recreation of
            // tracks repeatedly.
            $('table.form-data-table tr#'+track_id+' a.new-collection-entry').removeClass('new-collection-entry');

            // Check if the data representation table already has records.
            if ($data_representation_table.find('tbody tr').length) {
                $data_representation_table.find('tbody tr:last').after(dummy_track);
            }
            else {
                //console.log($data_representation_table);
                $data_representation_table.find('tbody').html(dummy_track);
            }

            // Modified elements have to be selected in a different manner as new-entry
            // contains an extra section tag.
            modified_elements = modified_tr.children('td').children('.form-group');
            // This is to check if annoying 'contact' form has been used as a polycollection
            if(modified_elements.length == 0) {
                var modified_elements = modified_tr.children('td').children('div.row').children('div.col').children('.form-group');
            }
            if(modified_tr.children('td').find('.panel-block-contact').length) {
                var modified_elements = modified_tr.children('td').find('.panel-block-contact > .panel-body').children('.form-group');

            }
        }

        // Hide the modified tr
        modified_tr.removeClass('currently-editing new-entry').addClass('tmp-display-none');
        // Display relevant 'Aggiungi' button
        modified_tr.closest('table.form-data-table').find('tr.add-new-item').removeClass('tmp-display-none');


        // console.log(modified_elements.length);

        //TODO: find a dynamic solution
        modified_elements.each(function(e) {
            if ($(this).children('div').children('.form-group').length) {

                $(this).children('div').children('.form-group:not(.polycollection)').each(function(e) {
                    _copy_values($(this), index);
                });
            }
            else {
                _copy_values($(this), index);
            }

        });

        $(document).trigger('postCollectionWidgetSaveModification', [modified_tr]);
    });
    // END - SAVE MODIFICATION
    // ----------------------------------------------------------------------

    $(document).on('click', 'tr.add-new-item:not(tmp-display-none) a', function(e) {
        $(this).closest('tr').addClass('tmp-display-none');
    });

});

// Cancel all the pending modifications before submitting the form
/*$("#admin-form").submit(function(event) {
  $('table.form-data-table tr.currently-editing a.collection-widget-cancel-modification, table.form-data-table tr.new-entry a.collection-widget-cancel-modification').each(function() {
    $(this).click();
  });
});*/

function _widget_error_handler(cur_field, jarviswidget_id) {
    if (cur_field.closest('tr')) {
        if (cur_field.closest('tr').hasClass('no-data-table')) {
            var tr_id = cur_field.closest('table').closest('tr').attr('id');
        }
        else {
            var tr_id = cur_field.closest('tr').attr('id');
        }
    }

    if (typeof tr_id === "undefined") {
        tr_id = cur_field.closest('table').closest('tr').attr('id');
    }

    var collection_widget_wrapper = cur_field.parent().closest('div#'+jarviswidget_id+' div.collection-widget-wrapper');
    if (collection_widget_wrapper.length === 0) {
        var collection_widget_wrapper = cur_field.parent().closest('div.collection-widget-wrapper');
    }

    collection_widget_wrapper.find('table.data-representation-table:first tr#'+tr_id).addClass('coll-widget-error');
    if (collection_widget_wrapper.attr('id') !== undefined) {
        _widget_error_handler(collection_widget_wrapper, jarviswidget_id);
    }
}

function _copy_values(cur_field, index) {
    var default_value = '';
    if ($('label.select', cur_field).length) {
        var select_element = $('select', cur_field);
        if (select_element.length == 1) {
            var select_element_id = select_element.attr('id');
            //default_value = select_element.find(":selected").text();
            //Selected item/items are collected in an array to handle multiselect.
            var selected_items = [];
            select_element.find(":selected").each(function( index ) {
                selected_items.push($( this ).text());
                if ($('#'+select_element_id).val() !== '') {
                    $('select#' + select_element_id + ' option[value=' + $(this).val() + ']').attr('selected', 'selected');
                }
            });

            if (selected_items.length === 0) {
                if (select_element.attr('data-empty-value')) {
                    default_value = select_element.data('empty-value');
                }
            }
            else {
                default_value = selected_items.join(', ');
            }
            $('section#_'+select_element_id).html(default_value);
        }
    }
    else if ($('label.input', cur_field).length) {
        if ($('input[type="number"]', cur_field).length) {
            var input_number_element = $('input[type="number"]', cur_field);
            default_value = input_number_element.val();

            $('section#_'+input_number_element.attr('id')).html(default_value);
        }
        else if ($('input:text', cur_field).length) {
            var input_element = $('input:text', cur_field);
            if (input_element.hasClass('sv-autocomplete-input')) {
                var element_id = input_element.attr('id').replace('autocompleter_', '');
                default_value = $('input:text.sv-autocomplete-input', cur_field).val();
                $('section#_'+ element_id).html(default_value);
            } else {
                // var input_element = $('input:text', cur_field);
                default_value = input_element.val();
                if ($('input:text', cur_field).hasClass('sv-color-picker')) {
                    default_value = '<div class="picked-color-sample" style="background-color:'+default_value+'">'+default_value+'</div>';
                }

                $('section#_' + input_element.attr('id')).html(default_value);
            }
        }
        else if ($('input[type="file"]', cur_field).length) {
            var element_id = '';
            var input_file_element = $('input[type="file"]', cur_field);
            if (input_file_element.attr('data-file-name-field')) { // check if file name field is set
                var file_name_field = input_file_element.attr('data-file-name-field');
                var id_parts = input_file_element.attr('id').split('_'+index+'_');
                element_id = id_parts[0]+'_'+index+'_'+file_name_field;

                default_value = input_file_element[0].files[0].name;
                $('section#_'+ element_id).html(default_value);
            }
        }
        else if ($('input[type="date"]', cur_field).length) {
            var input_element = $('input[type="date"]', cur_field);
            default_value = input_element.val();
            $('section#_'+input_element.attr('id')).html(default_value);
        }
        else if ($('input[type="time"]', cur_field).length) {
            var input_element = $('input[type="time"]', cur_field);
            default_value = input_element.val();
            $('section#_'+input_element.attr('id')).html(default_value);
        }
    }
    else if ($('label.checkbox', cur_field).length) {
        var input_element = $('input[type="checkbox"]', cur_field);
        default_value = input_element.is(":checked") ? 'Sì' : 'No';
        $('section#_'+input_element.attr('id')).html(default_value);
    }
    else if ($('label.textarea', cur_field).length) {
        var input_element = $('textarea', cur_field);
        default_value = input_element.val();
        $('section#_'+input_element.attr('id')).html(default_value);
    }
    else {
        $('input[type="radio"]', cur_field).each(function() {
            if($(this).is(':checked')) {
                var input_element = $(this).closest('div');
                default_value = $("label[for='"+$(this).attr('id')+"']:not(.radio)").text().replace(/&nbsp;/g,' ').trim();
                $('section#_'+input_element.attr('id')).html(default_value);
            }
        });
    }

    var cur_selection_element = $('section#_'+ element_id);
    cur_selection_element.removeClass('overflowing').attr('title', '');
    if (cur_selection_element.height() > 20 ) {
        cur_selection_element.addClass('overflowing').attr('title', default_value);
    }
}

//When one track is being modified 'Add new' button is hidden and
//in this moment the track is deleted 'Add new' button remains hidden.
//This event resolves that problem.
$(document).on( "preTrackRemove", function( event, $tr ) {
    var curDataRepresentationTable = $tr.closest('table');
    var relevantFormDataTableId = curDataRepresentationTable.attr('target_table');
    $('table#'+ relevantFormDataTableId +' tfoot tr.add-new-item').removeClass('tmp-display-none');
});


