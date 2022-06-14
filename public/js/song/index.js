/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
/*!************************************!*\
  !*** ./resources/js/song/index.js ***!
  \************************************/
var selectCategory = $('#songFilter #selectCategory');
var selectVersion = $('#songFilter #selectVersion');
$(document).ready(function () {
  // selectCategory.selectpicker();
  // selectVersion.selectpicker();
  // DataTable
  datatableSearch();
});

function datatableSearch() {
  var dataTable = $('#song-table');
  var url = dataTable.data('url');
  dataTable.DataTable({
    "processing": true,
    "serverSide": true,
    "ajax": {
      "url": url,
      "dataType": "json",
      "type": "POST",
      "data": function data(_data) {
        _data._token = $('meta[name="csrf-token"]').attr('content');
        _data.valState = selectVersion.val();
        _data.valStatus = selectCategory.val();
      }
    },
    "columns": [{
      "data": 'rec_sort'
    }, {
      'data': 'cover'
    }, {
      "data": 'song'
    }, {
      "data": 'artist'
    }, {
      "data": 'category'
    }, {
      "data": 'version'
    }, {
      "data": 'bpm'
    }],
    "order": [[0, "asc"]],
    "columnDefs": [{
      "targets": 0,
      "className": 'd-none'
    }]
  });
}
/******/ })()
;
//# sourceMappingURL=index.js.map