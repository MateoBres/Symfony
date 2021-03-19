import $ from "jquery";
import '../../css/sinervis/sv_data_import.scss';


$(document).on('change', 'input[type=file]', function() {
    // var file_name = $.trim($(this).val());
    // file_name = file_name.replace(/^.*\\/, "");
    // file_name = file_name.length ? file_name : 'Nessun file selezionato';
    // $(this).closest('div.sv-file-wrapper').find('div.sv-fake-file-group input').val(file_name);
});

$(function() {
    $('.import-modal').each(function() {
        new ImportManager($(this));
    });
});

class ImportManager {

    /* PROPERTIES */
    form = 'form[name=data_import_type]';
    statusMsg = 'div.status-msg';
    formErrors = 'div.form-errors';
    importButton = 'button.data-import-btn';
    closeButton = 'button#js-close, .close';
    closeButtons = '#js-close, .modal button.close';
    validationCheckBox = 'div.data-validated input';
    progressBar = '.progress-bar';
    progressBarWrapper = '.progress-wrapper';
    progress = 'div.progress div.data-import-progress';
    progressStats = 'div.progress-wrapper div.stats';
    pageReloadButton = 'button.page-reload';
    inputDataImportType = 'input#data_import_type_excelFile';
    fileWrapperInput = '.sv-file-wrapper input';

    $modal = {};
    updateProgressTimeout = null;

    /* CONSTRUCTOR */

    constructor($modal) {
        this.$modal = $modal;
        this.initializeButtons();
        this.hideProgressBar();
        this.setDataAsInvalid();
        this.clearFileInput();
    }

    /* METHODS */

    initializeButtons() {
        this.$modal.on('click', this.importButton, () => {
            this.$(this.formErrors).html('');
            this.submitImportForm();
        });

        this.$modal.on('click', this.closeButton, () => {
            this.resetImportForm();
        });
    }

    submitImportForm() {
        const additionalInfo = $('div.additional-info').html();
        this.$(this.form).find('input.additional-info').val(additionalInfo);
        const formData = new FormData(this.$(this.form)[0]);
        this.clearFormErrors();

        $.ajax({
            type: "POST",
            url: this.formAction,
            data: formData,
            contentType: false, // NEEDED, DON'T OMIT THIS (requires jQuery 1.6+)
            processData: false, // NEEDED, DON'T OMIT THIS,
            beforeSend: () => {
                console.log('before')
                this.disableImportButton();
                if (this.areDataValidated) {
                    this.showProgressBar();
                    this.showStatusMsg('Importazione dati in corso...');
                } else {
                    this.hideProgressBar();
                    this.showStatusMsg('Validazione dati in corso...');
                }
            },
            complete: () => {
                this.clearFileInput();
                this.setDataAsInvalid();
            },
            success: response => {
                this.hideStatusMsg();
                if (response.valid !== undefined) {
                    if (response.valid) {
                        this.setDataAsValid();
                        this.$(this.form).find('input.import-id').val(response.importId);
                        this.disableCloseButtons();
                        this.updateImportProgress(response.importId);
                        this.submitImportForm();
                    } else {
                        this.setDataAsInvalid();
                        this.enableImportButton();
                        this.clearFileInput();
                        this.showFormErrors('<i class="ti-face-sad"></i> ' + response.msg)
                    }
                } else if (response.completed !== undefined) {
                    this.setDataAsInvalid();
                    location.reload();
                } else {
                    this.setDataAsInvalid();
                    clearTimeout(this.updateProgressTimeout);
                }
            },
            error: error => {
                clearTimeout(this.updateProgressTimeout);
                this.setDataAsInvalid();
                this.hideProgressBar();
                this.hideStatusMsg();
                this.showFormErrors('<i class="ti-face-sad"></i> Qualcosa è andato storto. Riprova (' + error.status + ' ' + error.statusText+ ')');
                this.enableImportButton();
                this.clearFileInput();
                this.enableCloseButtons();
            }
        });
    }

    updateImportProgress(importId) {
        this.animateProgressBar();
        $.ajax({
            type: "POST",
            url: this.$(this.form).attr('action') + '/' + importId,
            data: {},
            success: response => {
                if (response.imported_records !== undefined) {
                    const importedRecs = response.imported_records
                    const totalRecs = response.total_records
                    const importPercentage = (importedRecs / totalRecs * 100).toFixed(2);
                    this.$(this.progress).css('width', importPercentage + '%');
                    this.$(this.progressStats).html(`${importedRecs}/${totalRecs}`);

                    let messages = JSON.parse(response.message)
                    if (messages && messages.length) {

                        const numExistingMsgs = this.$(this.progressBarWrapper + ' div.progress-message').length
                        let messagesToBeShown = messages.slice(numExistingMsgs)

                        messagesToBeShown.forEach(msg => {
                            this.$(this.progressBarWrapper).append(`<div class="progress-message bg-${msg.type} p-xs b-r-sm"><i class="ti-face-sad"></i> ${msg.message}</div>`);
                        });

                        messages = null;
                        messagesToBeShown = null;
                    }
                }

                if (response !== false) {
                    this.updateProgressTimeout = setTimeout(() => {
                        this.updateImportProgress(importId);
                    }, 1000)
                } else {
                    this.$(this.importButton).addClass('d-none');
                    this.$(this.pageReloadButton).removeClass('d-none');
                    this.stopAnimatingProgressBar();
                }
            }
        });
    }

    showProgressBar() {
        console.log('progress bar')
        this.$(this.progressBarWrapper).fadeIn('fast');
    }

    hideProgressBar() {
        this.$(this.progressBarWrapper).fadeOut('fast');
    }

    showStatusMsg(msg) {
        this.$(this.statusMsg).html(msg);
        this.$(this.statusMsg).removeClass('d-none');
    }

    hideStatusMsg() {
        this.$(this.statusMsg).addClass('d-none');
        this.$(this.statusMsg).html('');
    }

    disableCloseButtons() {
        this.$(this.closeButtons).addClass('d-none');
    }

    enableCloseButtons() {
        this.$(this.closeButtons).removeClass('d-none');
    }

    resetImportForm() {
        clearTimeout(this.updateProgressTimeout);
        this.setDataAsInvalid();
        this.clearFileInput();
        this.enableImportButton();
        this.hideStatusMsg();
        this.enableImportButton();
        this.hideProgressBar();
        this.clearFormErrors();
    }

    clearFormErrors() {
        this.$(this.formErrors).html('');
        this.$(this.formErrors).addClass('d-none');
    }

    showFormErrors(html) {
        this.$(this.formErrors).html('');
        this.$(this.formErrors).removeClass('d-none');
        this.$(this.formErrors).html(html);
    }

    enableImportButton() {
        this.$(this.importButton).attr('disabled', false)
        this.$(this.importButton).find('i').addClass('d-none');
    }

    disableImportButton() {
        this.$(this.importButton).attr('disabled', true)
        this.$(this.importButton).find('i').removeClass('d-none');
    }

    clearFileInput() {
        this.$(this.form).find(this.inputDataImportType).val('');
        this.$(this.form).find(this.fileWrapperInput).val('')
    }

    setDataAsValid() {
        console.log('validation')
        console.log((this.form)+this.validationCheckBox)
        this.$(this.form).find(this.validationCheckBox).prop("checked", true).closest('div').addClass('checked');
    }

    setDataAsInvalid() {
        this.$(this.form).find(this.validationCheckBox).prop("checked", false).closest('div').removeClass('checked');
    }

    animateProgressBar() {
        this.$(this.progressBar).addClass('progress-bar-striped progress-bar-animated');
    }

    stopAnimatingProgressBar() {
        this.$(this.progressBar).removeClass('progress-bar-striped progress-bar-animated');
    }

    $(selector) {
        return this.$modal.find(selector);
    }

    get areDataValidated() {
        return this.$(this.form).find(this.validationCheckBox).prop('checked');
    }

    get formAction() {
        return this.$(this.form).attr('action');
    }
}
