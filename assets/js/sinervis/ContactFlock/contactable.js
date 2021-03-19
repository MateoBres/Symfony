import $ from 'jquery';
import runAllForms from './../form-plugin-initialization';
import {getFormMethod} from './../../sinervis/components/get_form_method';
import Routing from "./../components/sv_router";

$(document).ready(function () {
    toggleBranchesBlockTitle();
});

$(document).on('change', 'input.invoice-data-same-as-contact', function (e) {
    if ($(this).prop("checked")) {
        $('.form-group.invoice-data-toggle').addClass('invoice-data-hide');
    } else {
        $('.form-group.invoice-data-toggle').removeClass('invoice-data-hide');
    }
});

/*$('#admin-form').submit(function () {
    $("#admin-form :disabled").removeAttr('disabled');
});*/

$(document).on('change', 'div.contact_type_choice input', function(event){

  var route_name = 'contact_flock_contact_person_new';
  if ($(this).val() == 'c') {
    route_name = 'contact_flock_contact_company_new';
  }

  var url = Routing.generate(route_name);
  window.location.replace(url + location.search);
});


$(document).on('change paste keyup', 'input.cf-firstname, input.cf-lastname, input.businessname', function () {
    let fullname = '';
    if ($(this).hasClass('businessname')) {
        fullname = $(this).val()
    } else {
        fullname = $('input.cf-firstname').val() + ' ' + $('input.cf-lastname').val();
    }
    $('div.card-header .page-title span.full-name').html(fullname)
})

// $(document).on('change', 'div.contact_type_choice input', function () {
//     var contact_type = $(this).val();
//
//     // wrapper is important to avoid replacing non relevant blocks. e.g. blocks in another fieldset.
//     // see for example students in customer form
//     var $wrapper = $('form > fieldset').length === 0 ? $(this).closest('form') : $(this).closest('fieldset')
//
//     if (contact_type !== undefined) {
//         var $form = $('form#admin-form');
//
//         var data = {};
//         data[$(this).attr('name')] = contact_type;
//         data['_ajax_request'] = true;
//
//         $.ajax({
//             url: $form.attr('action'),
//             type: getFormMethod($form),
//             data: data,
//             beforeSend: function () {
//                 $('div.contact_type_choice').append('<i style="color:#2489C5;" class="fa fa-refresh fa-spin"></i>');
//             },
//             success: function (html) {
//                 replaceBlockContent(html, contact_type, $wrapper);
//                 toggleBranchesBlockTitle();
//                 runAllForms();
//             }
//         });
//     }
// });

function replaceBlockContent(html, contact_type, $wrapper) {
    // console.log($wrapper.attr('id'))
    var $contactBlock = getValidationErrorRemovedElements($(html).find('form#admin-form div.panel-block-contact'));
    $wrapper.find('div.panel-block-contact').replaceWith($contactBlock);

    var $branchesBlock = getValidationErrorRemovedElements($(html).find('form#admin-form div.panel-block-branches'));
    $wrapper.find('div.panel-block-branches').replaceWith($branchesBlock);

    if (contact_type == 'p') {
        $wrapper.find("input[value='p']").prop('checked', true);
    } else {
        $wrapper.find("input[value='c']").prop('checked', true);
    }
}

function toggleBranchesBlockTitle() {
    var contact_type = $('.contact_type_choice input:checked').val()

    // if contact_type field is undefined, most probably the field is not rendered.
    // In this case the type is predefined. So use block title overriding mechanism to print the correct block title.
    if (contact_type !== undefined) {
        var title = 'Abitazioni';
        var icon_name = 'fa-home'
        if (contact_type == 'c') {
            title = 'Sedi';
            icon_name = 'fa-building';
        }

        $('div.panel-block-branches header h5').html('<i class="fa fa-fw '+icon_name+'"></i>' + title);
    }
}

function getValidationErrorRemovedElements($elements) {
    $elements.find('.state-error').removeClass('state-error');
    $elements.find('div.note.note-error').remove();
    return $elements;
}