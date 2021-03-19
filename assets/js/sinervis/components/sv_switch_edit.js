import $ from "jquery";
import swal from "sweetalert2";

(function($) {

    $.fn.switchEdit = function(args) {

        const SwitchEdit = this;

        SwitchEdit.Status = {
            EDIT: 1,
            LOCKED: 2
        }

        const defaults = {
            class: 'switch-edit',
            editIcon: 'fas fa-edit',
            undoIcon: 'fas fa-undo-alt',
            editTitle: 'Click here to unlock and change value',
            undoTitle: 'Click here to undo to default value',
            confirmTitle: 'Are You Sure?',
            confirmMessage: 'The value will revert to original one.',
            fieldName: '',
            confirmBeforeUndo: true,
            onUndo: null,
            onEdit: null,
            onClick: null,
            confirm: {
                title: 'Are You Sure?',
                html: 'The value will revert to original one.',
                type: "warning",
                showCancelButton: true,
                confirmButtonText: "Sì",
                cancelButtonText: 'No',
                imageWidth: '70',
                imageHeight: '70'
            }
        };

        const options = (typeof (args) != "object" ? defaults : $.extend(defaults, args));

        return this.each(function() {

            const input = this;
            const $input = $(input);

            if ($input.hasClass('processed')) {
                return;
            }

            const container = $input.closest('.form-group');
            const config = {...options, ...$input.data()};

            config.confirm.title = config.confirmTitle;
            config.confirm.html = config.confirmMessage;

            const $switch = $('<i/>', {
                'class': options.class,
                'data-toggle': 'tooltip'
            });

            $input.switch = $switch;

            $switch.setStatus = function(status) {
                switch (status) {
                    case SwitchEdit.Status.EDIT:
                        this
                            .removeClass('action-edit ' + config.editIcon)
                            .addClass('action-undo ' + config.undoIcon)
                            .attr('title', config.undoTitle).attr('data-original-title', config.undoTitle).tooltip('update');
                        this.closest('.form-group').find('.switch-open').attr('checked', true).trigger("click");
                        break;
                    case SwitchEdit.Status.LOCKED:
                        this
                            .removeClass('action-undo ' + config.undoIcon)
                            .addClass('action-edit ' + config.editIcon)
                            .attr('title', config.editTitle).attr('data-original-title', config.editTitle).tooltip('update');
                        this.closest('.form-group').find('.switch-close').attr('checked', true).trigger("click");
                        break;
                }
            }

            $input.setStatus = function(status) {
                config.status = status;
                this.data('status', status);
            }

            $input.switchToEdit = function() {
                this.removeAttr("readonly").prop('disabled', false);
                //Fernando: eliminato controllo perché faceva rimanere bloccati tutti i campi tranne le collection
                this.closest('.form-group').removeClass('readonly-area');

                this.switch.setStatus(SwitchEdit.Status.EDIT);
                this.setStatus(SwitchEdit.Status.EDIT);
                if (typeof options.onEdit == 'function') {
                    options.onEdit(input);
                }
                this.trigger('chosen:updated');
                this.focus().select();
            };

            $input.switchToUndo = function() {
                const chosen =  this.next('.chosen-container').find('.chosen-single');
                this.attr('readonly', 'readonly').addClass('field-loading');
                chosen.addClass('field-loading');
                this.closest('.form-group').addClass('readonly-area');
                if (typeof options.onUndo == 'function') {
                    options.onUndo(input, () => {
                        this.removeClass('field-loading');
                        this.change().trigger('chosen:updated');
                        chosen.removeClass('field-loading');
                    });
                } else {
                    this.removeClass('field-loading');
                    chosen.removeClass('field-loading');
                }

                this.setStatus(SwitchEdit.Status.LOCKED)
                this.switch.setStatus(SwitchEdit.Status.LOCKED);
            };

            $switch.initClickEvent = function($input) {
                this.on('click', event => {

                    if (typeof options.onClick == 'function') {
                        options.onClick(event);
                    }

                    if (config.status == SwitchEdit.Status.LOCKED) {
                        $input.switchToEdit();
                    } else {
                        if (options.confirmBeforeUndo && typeof swal !== 'undefined') {
                            swal.fire(config.confirm).then(result => {
                                if (result.value) {
                                    $input.switchToUndo();
                                }
                            });
                        } else if (options.confirmBeforeUndo) {
                            if (confirm(options.confirmTitle + ' ' + options.confirmMessage)) {
                                $input.switchToUndo();
                            }
                        } else {
                            $input.switchToUndo();
                        }
                    }
                });
            }

            $input.addClass('processed');
            $switch.setStatus(config.status);
            $switch.initClickEvent($input);
            container.append($switch);
        });
    }
})(jQuery);