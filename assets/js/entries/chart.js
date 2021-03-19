import SvHighchart from "../sinervis/components/SvHighchart";
import "../sinervis/ChartFlock/filter_form";
import "../../css/sinervis/sv_chart.scss";

$('.highchart-canvas').each(function(index) {
    const chartUrl = $(this).data('chart-url');
    const dynamicCanvasId = 'chart-canvas-' + index;
    $(this).attr('id', dynamicCanvasId);
    if (chartUrl && chartUrl !== undefined) {
        let newChart = new SvHighchart(chartUrl, dynamicCanvasId);
        newChart.draw();
    }
})

function moveContextMenuInsideHeader(dynamicCanvasId) {
    return;
}