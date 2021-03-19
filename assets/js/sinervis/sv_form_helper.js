import Swal from 'sweetalert2';
import runAllForms from './form-plugin-initialization';
import applyAutocompleteToCityField from './ContactFlock/taxCode.js';


$(function() {

    $(document).on('click', 'a.entity-save', function(e) {
        e.preventDefault();
        $(this).attr('disabled');
        $('#admin-form :disabled').prop("disabled", false);
        $('#admin-form').submit();
        return false;
    });

    $(document).on('click', '#save-and-new-button', function(e) {
        e.preventDefault();
        $(this).attr('disabled');
        $('#admin-form :disabled').prop("disabled", false);
        if (!$('input#save-and-new').length) {
            $('<input>').attr('type', 'hidden')
                .attr('name', 'save-and-new')
                .attr('id', 'save-and-new')
                .val('1')
                .appendTo('#admin-form');
        }
        $('#admin-form').submit();
        return false;
    });

    $(document).on('click', '.collection .delete', function(e) {
        e.preventDefault();
        const clicked_item = $(this);

        Swal(
            {
                title: "Sei sicuro?",
                text: "Questa riga verrà cancellata!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Sì",
                cancelButtonText: 'No',
            }).then(function(resp) {
                if (resp.value) {
                    const $tr = clicked_item.closest('tr');
                    const closest_data_rep_table = $tr.closest('table');
                    $(document).trigger('preTrackRemove', [$tr]);
                    $('tr#' + $tr.attr('id')).remove();
                    $tr.remove();
                    $(document).trigger('postTrackRemove', closest_data_rep_table);
                }
            }
        );
    });

    $(document).on('click', 'tr.add-new-item:not(tmp-display-none) .add, tr.without-data-table .add', function(e) {
        e.preventDefault();
        const $collection = $(this).closest('.collection');
        const $data_representation_table = $collection.find('div.collection-widget-wrapper:first').find('table.data-representation-table:first');

        let row_id = '';

        if ($data_representation_table.length) {
            row_id = $data_representation_table.find('tfoot tr.dummy-track').attr('id');
        } else {
            row_id = $(this).data('track-id');
        }

        const $addLink = $(this);

        const index = $collection.data('index');
        let prototype = $addLink.data('prototype');
        const parent_name = $('<div>' + prototype + '</div>').find('div:first').attr('parent-name');
        const entity_name = $('<div>' + prototype + '</div>').find('div:first').attr('entity-name');

        const firstRegExp = new RegExp(parent_name + '___name__', 'g');
        // var prototype = prototype.replace(/__name__label__/g, 'nuovo').replace(firstRegExp, parent_name+'_'+index);
        prototype = prototype.replace(/__name__label__/g, entity_name).replace(firstRegExp, parent_name + '_' + index);

        const secondRegExp = new RegExp(parent_name + '\\]\\[__name__', 'g');
        prototype = prototype.replace(secondRegExp, parent_name + '][' + index);

        const $tbody = $addLink.closest('table').find('> tbody');
        const sortable = ($addLink.closest('table').hasClass('sortable-table') ? '<td class="table-sortable-handler"><i class="glyphicon glyphicon-move"></i></td>' : '');

        row_id = row_id.replace('_ID_', index);
        let new_tr = null;
        if ($data_representation_table.length) {
            new_tr = '<tr id="' + row_id + '" rel="' + row_id + '_form" index="' + index + '" class="new-entry"><td class="data-td sv-row">' + prototype + '<div class="action-button-wrapper"><a class="btn btn-inverse btn-sm btn-labeled btn-labeled-right collection-widget-save-modification new-collection-entry" href="#">Conferma <span class="btn-label"><i class="fa fa-check"></i> </span></a><a class="btn btn-inverse btn-sm btn-labeled btn-labeled-right collection-widget-cancel-modification new-collection-entry" href="#">Annulla <span class="btn-label"><i class="fas fa-times"></i> </span></a></div></td></tr>';
        } else { // for collections without data table
            new_tr = '<tr id="' + row_id + '" rel="' + row_id + '_form" index="' + index + '" class="new-entry no-data-table">' + sortable + '<td class="data-td sv-row">' + prototype + '</td><td class="data-action"><a href="#" class="delete" data-toggle="tooltip" title="Rimuovi elemento"><i class="fas fa-trash text-danger"></i></a></td></tr>';
        }

        /*
        * duplicate last row
        * TODO: funziona per il caso particolare (lesson generator), in caso va generalizzato per le altre collection
        */
        const $new_tr = $(new_tr);
        if ($(this).hasClass('duplicate')) {
            const skippedFields = $(this).data('skipDuplication').split(',');

            let values = [];
            const $lastRow = $tbody.children('tr').last();

            $lastRow.find('.form-group').each(function() {
                $(this).find('input, textarea, select').each(function() {
                    if (new RegExp(skippedFields.join("|")).test($(this).attr('name')) === false) {
                        values.push($(this).val());
                    }
                });
            });

            let i = 0;
            $new_tr.find('.form-group').each(function() {
                $(this).find('input, textarea, select').each(function() {
                    if (new RegExp(skippedFields.join("|")).test($(this).attr('name')) === false) {
                        $(this).val(values[i++]);
                    }
                });
            });
        }


        // console.warn('if adding new fields create problems add the new files in tfoot instead of in tbody');
        $tbody.append($new_tr);
        $collection.data('index', index + 1);

        // Adds field redefining toggle button for 'Date Periods' and 'Time Periods'.
        $('.show-redefining-fields').parent().replaceWith('<section style="padding-top: 18px;" class="col col-md-1 service-redefined-show"><div class="btn btn-info btn-default"><i class="fa fa-chevron-down"></i></div></section>');

        runAllForms();
        applyAutocompleteToCityField();
        // Perform an action just after adding a new track
        $(document).trigger('postTrackAdd', [row_id]);
    });


// Polycollection item add
    $(document).on('click', '.creation-form-table .add', function(e) {
        e.preventDefault();
        var $collection = $(this).closest('.collection');
        var $addLink = $(this);
        var prototype = $addLink.data('prototype');
        var index = $collection.data('index');
        var prototype = prototype.replace(/__name__label__/g, '').replace(/__name__/g, index);
        var $tr = $addLink.closest('tr');
        $('<tr class="new-entry"><td class="action"><a href="#" class="delete"><i class="fas fa-trash text-danger"></i></a></td><td class="td-data">' + prototype + '</td></tr>').insertBefore($tr);
        $collection.data('index', index + 1);

        runAllForms();
    });


    /* Payment settlement date toggler for single occurence */
    $(document).on('ifChecked', 'div.sv-payment-settled input', function() {
        var pair_id = $(this).closest('div.sv-payment-settled').data('pair-id');
        var $settled_on_field = $("input[data-pair-id='" + pair_id + "']");
        if ($(this).val() == '1') {
            if (!$.trim($settled_on_field.val()).length) {
                $settled_on_field.val($.datepicker.formatDate('dd/mm/yy', new Date()));
            }
        } else {
            $settled_on_field.val('');
        }
    });

    /* Payment settlement date toggler for collections */
    $(document).on('change', 'table.form-data-table select.sv-payment-settled', function() {
        var $settled_on_field = $(this).closest('tr').find('input.sv-payment-settled-on');
        if ($(this).val() == '1') {
            if (!$.trim($settled_on_field.val()).length) {
                $settled_on_field.val($.datepicker.formatDate('dd/mm/yy', new Date()));
            }
        } else {
            $settled_on_field.val('');
        }
    });

})

