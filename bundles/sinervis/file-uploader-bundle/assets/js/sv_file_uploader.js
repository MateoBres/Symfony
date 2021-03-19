import $ from 'jquery';
import filesize from 'filesize';
import Swal from "sweetalert2";

import '../css/sv_file_uploader.scss';

// Fake file upload field
$(document).on('click', 'div.sinervis-fake-file-group input.file-field-stunt, div.sinervis-fake-file-group button.select-file', function(e) {
    const self = this;
    if (($(self).hasClass('file-field-stunt') && $(self).val() === '') || $(self).hasClass('select-file')) {
        $(this).closest('div.sinervis-file-wrapper').find('div.sinervis-custom-file label input[type=file]').click();
    }
});

$(document).on('click', 'div.sinervis-fake-file-group button.remove-file', function() {
    const self = this;
    const $sinervisFileWrapper = $(self).closest('.sinervis-file-wrapper');

    Swal.fire({
        title: 'Sei sicuro?',
        text: "Sei sicuro di voler eliminare il file!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sì',
        cancelButtonText: 'No',
    }).then((result) => {
        if (result.value === true) {
            $sinervisFileWrapper.find('input.sinervis-file-soft-delete').prop('checked', true);
            $(self).closest('.sinervis-fake-file-group').find('.fake-file-field').val('');
            $(self).closest('.sinervis-fake-file-group').find('.select-file').removeClass('d-none');
            $(self).closest('.sinervis-fake-file-group').find('.remove-file').addClass('d-none');
            $sinervisFileWrapper.find('.image-preview').remove();
        }
    })

});


$(document).on('change', '.sinervis-file-wrapper input[type=file]', function() {
    const self = this;
    const $sinervisFileWrapper = $(self).closest('.sinervis-file-wrapper');
    var file_name = $.trim($(this).val());
    file_name = file_name.replace(/^.*\\/, "");
    file_name = file_name.length ? file_name : '';

    let uploadedFile = $(this).prop('files')[0];

    if($(this).hasClass('show-image-preview')) {
        renderPreview($sinervisFileWrapper, uploadedFile);
    }

    let formData = new FormData();
    formData.append("sv_file", uploadedFile);
    formData.append('data_class', $sinervisFileWrapper.find('input.data-class').val());
    formData.append('property_name', $sinervisFileWrapper.find('input.property-name').val());

    $(self).closest('div.sinervis-file-wrapper').find('div.sinervis-fake-file-group input.file-field-stunt').val('in caricamento ...');
    $sinervisFileWrapper.find('.select-file').addClass('d-none');
    $sinervisFileWrapper.find('i.loading').removeClass('d-none');

    fetch('/sinervis-file-upload-api',  {method: "POST", body: formData})
        .then(response => response.json())
        .then(data => {
            if (data.error !== undefined) {
                fetchErrorHandler(self, data.error);
            } else {
                const formattedFileSize = filesize(data.size, {base: 10});
                const file_name_with_size = file_name + ' ['+ formattedFileSize +']'
                $(self).closest('div.sinervis-file-wrapper').find('div.sinervis-fake-file-group input.file-field-stunt').val(file_name_with_size);

                $sinervisFileWrapper.find('.sinervis-file-name').val(data.name);
                $sinervisFileWrapper.find('.sinervis-file-original-name').val(data.originalName);
                $sinervisFileWrapper.find('.sinervis-file-mimetype').val(data.mimeType);
                $sinervisFileWrapper.find('.sinervis-file-size').val(data.size);
                $sinervisFileWrapper.find('.sinervis-file-uri').val(data.uri);
                $sinervisFileWrapper.find('input.sinervis-file-soft-delete').prop('checked', false);
                $sinervisFileWrapper.find('i.loading').addClass('d-none');
                $sinervisFileWrapper.find('.select-file').addClass('d-none');
                $sinervisFileWrapper.find('.remove-file').removeClass('d-none');
            }
        }).catch(function (error) {
            fetchErrorHandler(self, error);
        });
});

function fetchErrorHandler(fileField, errors) {
    let errorMsgs = `<div class="file-load-error">${errors}</div>`;
    if (Array.isArray(errors)) {
        errorMsgs = errors.map(function (errorMsg) {
            return `<div class="file-load-error">${errorMsg}</div>`;
        }).join('');
    }
    Swal.fire({
        title: 'Caricamento file fallito',
        icon: 'error',
        html: errorMsgs,
    });
    $(fileField).closest('div.sinervis-file-wrapper').find('div.sinervis-fake-file-group input.file-field-stunt').val('');
    $(fileField).closest('.sinervis-file-wrapper').find('.select-file').removeClass('d-none');
    $(fileField).closest('.sinervis-file-wrapper').find('i.loading').addClass('d-none');
}

function renderPreview($sinervisFileWrapper, uploadedFile) {
    $sinervisFileWrapper.append('<div class="image-preview"><img src=""/></div>');

    const fileReader = new FileReader();
    fileReader.addEventListener('load', function () {
        $sinervisFileWrapper.find('.image-preview img').attr('src', fileReader.result);
    }, false);

    fileReader.readAsDataURL(uploadedFile);
}