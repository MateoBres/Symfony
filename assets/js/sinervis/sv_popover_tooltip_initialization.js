/*
 *   initialize tooltip and popover
 */

import $ from "jquery";

export const TOOLTIP_DELAY = {
    show: 400,
    hide: 20
};

if ($.fn.tooltip) {
    $('[data-toggle="tooltip"], [rel="tooltip"]').tooltip({
        html: true,
        trigger: 'hover',
        placement: 'top',
        delay: TOOLTIP_DELAY
    });
}

if ($.fn.popover) {
    $('[data-toggle="popover"], [rel="popover"]').each(function() {
        let $this = $(this);
        $this.popover({
            html: true,
            trigger: 'hover',
            placement: 'top',
            container: $this,
            delay: TOOLTIP_DELAY
        });
    });
}


//force opening popover one at a time
$(document).on('click', '*[rel="popover"]', function(e) {
    $('*[rel="popover"]').not(this).popover('hide');
});
