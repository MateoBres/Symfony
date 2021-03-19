import 'babel-polyfill';
import Highcharts from 'highcharts';
import Highstock from 'highcharts/highstock';
import 'highcharts/modules/no-data-to-display';
// Alternatively, this is how to load Highstock. Highmaps is similar.
// import Highcharts from 'highcharts/highstock';

// Load the exporting module.
import Exporting from 'highcharts/modules/exporting';
import Routing from "./sv_router";

// Initialize exporting module.
Exporting(Highcharts);
Exporting(Highstock);

export default class SvHighchart {
    constructor(remoteUrl, canvasId, query = null, chartOptions = null, chartNormalizer = null, requestParams = null) {
        this.remoteUrl = remoteUrl;
        this.canvasId = canvasId;
        this.chart = null;
        this.query = query;
        this.chartOptions = chartOptions;
        this.chartNormalizer = chartNormalizer;
        this.requestParams = requestParams;
        this.seriesData = {}; // used when data is load in chunks
        this.seriesSvOptions = {}; // used when data is load in chunks
        this.chartUpdateIntervalList = []; // used when data is load in chunks
        this.chartHasData = false;
    }

    async draw(callback) {
        $('.dynamic-chart-loader').removeClass('d-none');
        const options = await this.fetchOptions();
        this.chartLibrary = options.chartLibrary !== undefined ? options.chartLibrary : 'highcharts';
        const chartOptions = options.chartOptions !== undefined ? options.chartOptions : {};
        const chartOptionsForReload = Object.assign({}, chartOptions);
        this.checkChartHasData(chartOptions.series)

        if (this.chartLibrary === 'highstock') {
            Highstock.setOptions(getDefaultHighchartOptions());
            this.chart = Highstock.stockChart(this.canvasId, chartOptions, callback);
        } else {
            Highcharts.setOptions(getDefaultHighchartOptions());
            this.chart = Highcharts.chart(this.canvasId, chartOptions, callback);
        }

        if (chartOptions.series.length === 0) {
            this.setNoDataMessage();
        }
        this.handleSeriesNeedLoadingInChunks(chartOptionsForReload);
    }


    async fetchOptions(dataToBeAppended) {
        const response = await fetch(this.remoteUrl, {
            method: 'POST',
            body: this.getParamsForFetch(dataToBeAppended)
        });

        return await response.json()
    }


    getParamsForFetch(dataToBeAppended) {
        dataToBeAppended = dataToBeAppended || {}
        let formData = null;
        // check if the chart is being rendered in a dashboard. If so get the closest filter_form
        if ($('div#'+this.canvasId).closest('div.sv-dashboard-col').find('form#filter_form').length) {
            formData = new FormData($('div#'+this.canvasId).closest('div.sv-dashboard-col').find('form#filter_form')[0]);
        } else {
            formData = $('form#filter_form').length ? new FormData($('form#filter_form')[0]) : new FormData();
        }

        for (let [key, value] of Object.entries(dataToBeAppended)) {
            formData.append(`sv_options[${key}]`, value);
        }

        if (this.query !== null) {
            formData.append(`query`, this.query);
        }

        if (this.chartOptions !== null) {
            formData.append(`chartOptions`, this.chartOptions);
        }

        if (this.chartNormalizer !== null) {
            formData.append(`chartNormalizer`, this.chartNormalizer);
        }

        if (this.requestParams !== null) {
            for (var attr in this.requestParams) {
                formData.append(attr, this.requestParams[attr]);
            }
        }
        return formData;
    }


    handleSeriesNeedLoadingInChunks (chartOptions) {
        const self = this;

        if (chartOptions.series !== undefined && chartOptions.series !== null) {
            if (chartOptions.sv_options !== undefined && chartOptions.sv_options.load_in_chunks !== undefined) { // load all series simultaneously
                for (let [series_index, single_series] of Object.entries(chartOptions.series)) {
                    self.seriesData[series_index] = single_series.data;
                }
                self.seriesSvOptions = chartOptions.sv_options;
                self.loadDataInChunks()
            } else {
                for (let [series_index, single_series] of Object.entries(chartOptions.series)) {
                    if (single_series.sv_options !== undefined) {
                        if (single_series.sv_options.load_in_chunks !== undefined && single_series.sv_options.load_in_chunks) {
                            self.seriesData[series_index] = single_series.data;
                            self.seriesSvOptions[series_index] = single_series.sv_options;
                            self.loadDataInChunks(series_index)
                        }
                    }
                }
            }
        } else {
            $('.dynamic-chart-loader').addClass('d-none');
            this.setNoDataMessage();
        }
    }


    async loadDataInChunks(series_index) {
        const self = this;

        const svOptions = series_index !== undefined ? this.seriesSvOptions[series_index] : this.seriesSvOptions;
        const updatedOptions = await this.fetchOptions(svOptions);

        if (updatedOptions.chartOptions.series !== undefined) {
            if (series_index) {
                if (updatedOptions.chartOptions.series[series_index] !== undefined) {
                    if (updatedOptions.chartOptions.series[series_index].data.length === 0) {
                        // clearInterval(this.chartUpdateIntervalList[series_index])
                        self.seriesData[series_index] = null;
                        self.seriesSvOptions[series_index] = null;
                        $('.dynamic-chart-loader').addClass('d-none');
                        this.setNoDataMessage();
                    } else {
                        self.seriesData[series_index] = self.seriesData[series_index].concat(updatedOptions.chartOptions.series[series_index].data)
                        if (self.chart.series[series_index] !== undefined) {
                            self.chart.series[series_index].setData(self.seriesData[series_index])
                            self.seriesSvOptions[series_index] = updatedOptions.chartOptions.series[series_index].sv_options
                            self.loadDataInChunks(series_index);
                        }
                    }
                }
            } else {
                updatedOptions.chartOptions.series.forEach(function (data, series_index) {
                    self.seriesData[series_index] = self.seriesData[series_index].concat(updatedOptions.chartOptions.series[series_index].data)
                    if (self.chart.series[series_index] !== undefined) {
                        self.chart.series[series_index].setData(self.seriesData[series_index])
                    }
                })

                if (updatedOptions.chartOptions.sv_options !== undefined) {
                    self.seriesSvOptions = updatedOptions.chartOptions.sv_options
                    self.loadDataInChunks();
                } else {
                    $('.dynamic-chart-loader').addClass('d-none');
                    this.setNoDataMessage();
                }
            }
        } else if (updatedOptions.chartOptions.sv_options !== undefined) {
            // data might be empty for the given data period, but if sv_options are set remake the call
            self.seriesSvOptions = updatedOptions.chartOptions.sv_options
            self.loadDataInChunks();
        } else {
            $('.dynamic-chart-loader').addClass('d-none');
            this.setNoDataMessage();
        }

    }

    checkChartHasData(series) {
        const self = this;
        if (self.chartHasData === false && series !== undefined) {
            series.forEach(function (data, series_index) {
                if (series[series_index].data && series[series_index].data.length > 0) {
                    self.chartHasData = true;
                }
            })
        }
    }

    setNoDataMessage() {
        if (this.chartHasData === false) {
            const noDataMsgBlock = $('div.no-chart-data-msg-block').removeClass('d-none');
            $('div#'+this.canvasId).html(noDataMsgBlock)
        }
    }
}

function getDefaultHighchartOptions() {
    return {
        lang: {
            months: ["Gennaio", "Febbraio", "Marzo", "Aprile", "Maggio", "Giugno", "Luglio", "Agosto", "Settembre", "Ottobre", "Novembre", "Dicembre"],
            weekdays: ["Domenica", "Lunedì", "Martedì", "Mercoledì", "Giovedì", "Venerdì", "Sabato"],
            shortMonths: ["Gen", "Feb", "Mar", "Apr", "Mag", "Giu", "Lug", "Ago", "Set", "Ott", "Nov", "Dic"],
            rangeSelectorFrom: "Da",
            rangeSelectorTo: "A",
            rangeSelectorZoom: "Periodo",
            downloadPNG: "Scarica immagine PNG",
            downloadJPEG: "Scarica immagine JPEG",
            downloadPDF: "Scarica documento PDF",
            downloadSVG: "Scarica immagine SVG",
            thousandsSep: ".",
            decimalPoint: ',',
            millisecond: "%A, %b %e, %H:%M:%S.%L",
            second: "%A, %b %e, %H:%M:%S",
            minute: "%A, %b %e, %H:%M",
            hour: "%A, %b %e, %H:%M",
            day: "%A, %b %e, %Y",
            week: "Settimana del %A, %b %e, %Y",
            month: "%B %Y",
            year: "%Y",
            noData: "Nichts zu anzeigen"
        },
        noData: {
            style: {
                fontWeight: 'bold',
                fontSize: '15px',
                color: '#303030'
            }
        },
    }
}