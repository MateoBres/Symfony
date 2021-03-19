import Routing from "../components/sv_router";
import SvHighchart from "./SvHighchart";
import SvDataTable from "./sv_data_tables";

$(function () {
    const pollingBlocks = $('.long-poll');
    if (pollingBlocks.length > 0) {
        new SvLongPoller(pollingBlocks);
    }
});

export default class SvLongPoller {
    constructor(pollingBlocks) {
        this.pollingUrl = Routing.generate('utility_long_poll');
        this.pollingBlocks = pollingBlocks;
        this.poll();
    }

    async poll() {
        const self = this;
        console.log('polling...')
        fetch(this.pollingUrl)
            .then(response => response.json())
            .then(data => {
                console.log(data)
                self.replaceContents(data);
                self.poll();
            })
            .catch((error) => {
                if (error != 'TypeError: Failed to fetch') {
                    alert(error);
                }
            });
    }

    async replaceContents(updatedChannels) {
        const self = this;
        this.pollingBlocks.each(function () {
            const $block = $(this);
            const pollingChannel = $block.data('polling-channel');
            if (updatedChannels[pollingChannel] !== undefined) {
                self.toggleBlockLoader($block, 'enable');
                if ($block.hasClass('highchart-canvas')) {
                    self.replaceChartContent($block);
                } else if ($block.hasClass('sv-data-table')) {
                    self.replaceTableContent($block);
                } else {
                    self.replaceBlockContent($block)
                }
                self.toggleBlockLoader($block, 'disable');
            }
        });
    }

    toggleBlockLoader($block, status) {
        $('#' + $block.attr('id')).blockLoader(status);
    }

    replaceChartContent($block) {
        const chartUrl = $block.data('chart-url');
        const dynamicCanvasId = $block.attr('id');
        let newChart = new SvHighchart(chartUrl, dynamicCanvasId);
        newChart.draw();
    }

    replaceTableContent($table) {
        $table.DataTable().ajax.reload();
    }

    replaceBlockContent($block) {
        let contentFetchingUri = window.location.href;
        if ($(this).data('polling-source-uri') !== undefined) {
            contentFetchingUri = $block.data('polling-source-uri');
        }
        const blockId = $block.attr('id');

        $.ajax({
            url: contentFetchingUri,
            success: function (html) {
                $('#' + blockId).replaceWith(
                    $(html).find('#' + blockId)
                );
            }
        });
    }
}