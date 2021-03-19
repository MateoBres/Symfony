import $ from "jquery";

(function($) {

    $.fn.blockLoader = function(args) {

        return this.each(function() {

            const panelBody = $(this).find('.ibox-content');

            const $loader = $('<div/>', {
                class: 'block-loader ' + (panelBody.height() > 0 ? 'overlay' : 'block'),
            });

            if (args === 'disable') {
                $(this).removeClass('block-loading');
                panelBody.find('.block-loader').remove();
            } else if (args === 'enable' || typeof args == 'undefined') {
                $(this).addClass('block-loading');
                panelBody.find('.block-loader').remove();
                panelBody.prepend($loader);
            }

        });

    }
})(jQuery);