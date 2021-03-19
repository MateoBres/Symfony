import 'datatables.net-bs4';
import Router from './sv_router';
import 'jquery-match-height';

$(function () {
    $('table.sv-data-table').each(function () {
        new SvDataTable($(this));
    });
});

export default class SvDataTable {
    constructor($svTable) {
        this.$svTable = $svTable;
        this.tableContentUrl = Router.generate($svTable.data('table-content-url'));
        this.tableColumns = this.getTableColumns();
        if (this.tableContentUrl) {
            this.drawTable();
        }
    }

    async drawTable() {
        const self = this;
        const $currentTable = $(this);

        this.$svTable.DataTable({
            'ajax': self.tableContentUrl,
            // 'deferRender': true,
            "columns": self.tableColumns,
            "order": [[0, "desc"]],
            "lengthChange": false,
            "language": self.getLanguageSettings(),
            "initComplete": function( settings ) {
                self.$svTable.closest('.sv-dashboard-row').find('.ibox-content').matchHeight({
                    property: 'min-height',
                });
            }
        });
    }

    getTableColumns($table) {
        const columns = [];
        this.$svTable.find('th').each(function () {
            columns.push({'data': $(this).data('column-name')})
        })

        return columns
    }

    getLanguageSettings() {
        return {
            "sEmptyTable": "Nessun dato presente nella tabella",
            "sInfo": "Vista da _START_ a _END_ di _TOTAL_ elementi",
            "sInfoEmpty": "Vista da 0 a 0 di 0 elementi",
            "sInfoFiltered": "(filtrati da _MAX_ elementi totali)",
            "sInfoThousands": ".",
            "sLengthMenu": "Visualizza _MENU_ elementi",
            "sLoadingRecords": "Caricamento...",
            "sProcessing": "Elaborazione...",
            "sSearch": "Cerca:",
            "sZeroRecords": "La ricerca non ha portato alcun risultato.",
            "oPaginate": {
                "sFirst": "Inizio",
                "sPrevious": "Precedente",
                "sNext": "Successivo",
                "sLast": "Fine"
            },
            "oAria": {
                "sSortAscending": ": attiva per ordinare la colonna in ordine crescente",
                "sSortDescending": ": attiva per ordinare la colonna in ordine decrescente"
            }
        };
    }
}