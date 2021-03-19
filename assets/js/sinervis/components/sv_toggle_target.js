import $ from "jquery";

$(document).on('click', '.action-toggle-target', function(e) {
    e.preventDefault();
    const $target = $($(this).attr('href'));

    if ($(this).data('alternateLabel')) {
        let alternateLabel = $(this).data('alternateLabel');
        $(this).data('alternateLabel', $(this).html()).html(alternateLabel);
    }

    if ($(this).attr('data-alternate-title')) {
        let alternateTitle = $(this).attr('data-alternate-title');
        $(this).attr('data-alternate-title', $(this).attr('data-original-title'));
        if ($.fn.tooltip && $(this).data('toggle') === 'tooltip') {
            $(this).attr('data-original-title', alternateTitle);
            $(this).tooltip('update').tooltip('show');
        } else {
            $(this).attr('title', alternateTitle);
        }
    }

    if ($target.hasClass('show')) {
        $target.slideUp('fast').removeClass('show');
        $(this).attr('aria-expanded', 'false');
        $(this).removeClass('expanded');
    } else {
        $target.slideDown('fast').addClass('show');
        $(this).attr('aria-expanded', 'true');
        $(this).addClass('expanded');
    }
});