const selectCategory = $('#songFilter #selectCategory')
const selectVersion = $('#songFilter #selectVersion')

$(document).ready(function () {
    // selectCategory.selectpicker();
    // selectVersion.selectpicker();

    // DataTable
    datatableSearch();
});

function datatableSearch() {
    let dataTable = $('#song-table');
    let url = dataTable.data('url');

    dataTable.DataTable({
        "processing": true,
        "serverSide": true,
        "ajax":{
            "url": url,
            "dataType": "json",
            "type": "POST",
            "data": function (data) {
                data._token = $('meta[name="csrf-token"]').attr('content');
                data.valState = selectVersion.val();
                data.valStatus = selectCategory.val();
            }
        },
        "columns": [
            { "data": 'rec_sort' },
            { 'data': 'cover'},
            { "data": 'song' },
            { "data": 'artist' },
            { "data": 'category' },
            { "data": 'version' },
            { "data": 'bpm' },
        ],
        "order": [[ 0, "asc" ]],
        "columnDefs": [
            {
                "targets": 0,
                "className": 'd-none'
            }
        ]
    });
}