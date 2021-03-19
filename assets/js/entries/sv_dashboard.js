import '../../css/sinervis/sv_dashboard.scss';

import 'jquery-match-height';
// import 'datatables.net-bs4';

$(document).ready(function () {
    // $('.sv-dashboard-row').each(function () {
    //     $(this).find('> div .ibox-content').matchHeight();
    // });

    $('a.print-menu').on('click', function () {
        if ($(this).hasClass('pressed')) {
            $(this).removeClass('pressed');
            $(this).closest('div').click();
        } else {
            $(this).addClass('pressed');
            $(this).closest('div.sv-dashboard-col').find('g.highcharts-contextbutton').click();
        }
    });
    $('a.print-menu').on('mouseout', function () {
        $(this).removeClass('pressed');
        $(this).closest('div').click();
    });
});

$(document).on('DOMNodeInserted', function(e) {
    if ( $(e.target).hasClass('highcharts-contextmenu') && !$(e.target).hasClass('sv-processed') ) {
        $(e.target).addClass('sv-processed');
        const offset = $(e.target).closest('div.sv-chart-widget-box').offset().top - $(e.target).closest('div.highcharts-container').offset().top
        const height = $(e.target).closest('div.sv-chart-widget-box').height() + offset + 30
        $(e.target).wrap('<div class="context-menu-wrapper" style="position: relative; top: -'+height+'px; right: -16px;"></div>');
    }
});