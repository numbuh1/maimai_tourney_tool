/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
/*!***********************************!*\
  !*** ./resources/js/pool/edit.js ***!
  \***********************************/
var selectCategory = $('#selectSongModal #selectCategory');
var selectVersion = $('#selectSongModal #selectVersion');
var selectDiff = $('#selectSongModal #selectDiff');
var selectLevelMin = $('#selectSongModal #selectLevelMin');
var selectLevelMax = $('#selectSongModal #selectLevelMax');
var selectRandomNumber = $('#selectSongModal #selectRandomNumber');
var confirmSongImage = $('#confirmSelectSongModal #songImage');
var confirmSongName = $('#confirmSelectSongModal #songName');
var confirmSongArtist = $('#confirmSelectSongModal #songArtist');
var confirmSongBPM = $('#confirmSelectSongModal #songBPM');
var confirmSongCategory = $('#confirmSelectSongModal #songCategory');
var confirmSongDifficulty = $('#confirmSelectSongModal #songDifficulty');
var confirmSongLevel = $('#confirmSelectSongModal #songLevel');
var confirmSongVersion = $('#confirmSelectSongModal #songVersion');
var poolTable = $('#tblPool');
$(document).ready(function () {
  $('.modal').on('shown.bs.modal', function (e) {
    $(this).find('.select2').select2({
      dropdownParent: $(this).find('.modal-content')
    });
  });
  poolTable.DataTable({
    "processing": true,
    "serverSide": true,
    'dom': 'lrtip',
    "bPaginate": false,
    "ajax": {
      "url": $('#tblPool').data('url'),
      "dataType": "json",
      "type": "POST",
      "data": function data(_data) {
        _data._token = $('meta[name="csrf-token"]').attr('content');
      }
    },
    "columns": [{
      "data": 'order'
    }, {
      "data": 'song'
    }, {
      'data': 'songAction'
    }, {
      "data": 'slotAction'
    }],
    "order": [[0, "asc"]],
    "columnDefs": [{
      "targets": 0,
      "className": 'd-none'
    }]
  });
});
$(document).on('click', '#btnAddSong', function () {
  $('#selectSongModal').modal('show');
});
$(document).on('click', '#btnShowSongs', function () {
  $.ajaxSetup({
    headers: {
      "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
    }
  });
  $.ajax({
    type: "POST",
    url: $(this).attr("data-action"),
    data: {
      category: selectCategory.val(),
      version: selectVersion.val(),
      difficulty: selectDiff.val(),
      levelMin: selectLevelMin.val(),
      levelMax: selectLevelMax.val()
    },
    dataType: 'json',
    complete: function complete(data) {
      console.log(JSON.parse(data.responseText));
      var charts = JSON.parse(data.responseText);
      $('#song-pane').html('');

      for (var i = 0; i < charts.length; i++) {
        var kind = charts[i].type == 'dx' ? 'dx' : 'standard';
        var html = '<div class="col-md-2 col-6">' + '<a href="#" class="song-select text-dark" data-id="' + charts[i].chart_id + '"><div class="card" style="width: 8rem;">' + '<img class="card-img-top chart-thumbnail chart-' + charts[i].difficulty + '" src="https://dp4p6x0xfi5o9.cloudfront.net/maimai/img/cover/' + charts[i].imageName + '" alt="Card image cap">' + '<img src="https://maimaidx-eng.com/maimai-mobile/img/music_' + kind + '.png" class="music_kind_icon ">' + '<div class="music_lv_back text-center">' + charts[i].level + '</div>' + '</div></a>' + '</div>';
        $('#song-pane').append(html);
      }
    }
  });
});
$(document).on('click', '#btnHideList', function () {
  $('#songList').hide();
});
$(document).on('click', '#btnShowList', function () {
  $('#songList').show();
});
$(document).on('click', '#btnRandomSongs', function () {
  $.ajaxSetup({
    headers: {
      "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
    }
  });
  $.ajax({
    type: "POST",
    url: $(this).attr("data-action"),
    data: {
      category: selectCategory.val(),
      version: selectVersion.val(),
      difficulty: selectDiff.val(),
      levelMin: selectLevelMin.val(),
      levelMax: selectLevelMax.val(),
      count: selectRandomNumber.val()
    },
    dataType: 'json',
    complete: function complete(data) {
      var charts = JSON.parse(data.responseText);
      $('#song-pane').html('');

      for (var i = 0; i < charts.length; i++) {
        var kind = charts[i].type == 'dx' ? 'dx' : 'standard';
        var html = '<div class="col-md-2 col-6">' + '<a href="#" class="song-select text-dark" data-id="' + charts[i].chart_id + '"><div class="card" style="width: 8rem;">' + '<img class="card-img-top chart-thumbnail chart-' + charts[i].difficulty + '" src="https://dp4p6x0xfi5o9.cloudfront.net/maimai/img/cover/' + charts[i].imageName + '" alt="Card image cap">' + '<img src="https://maimaidx-eng.com/maimai-mobile/img/music_' + kind + '.png" class="music_kind_icon ">' + '<div class="music_lv_back text-center">' + charts[i].level + '</div>' + '</div></a>' + '</div>';
        $('#song-pane').append(html);
      }
    }
  });
});
$(document).on('click', '.song-select', function () {
  var chartId = $(this).data('id');
  $.ajaxSetup({
    headers: {
      "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
    }
  });
  $.ajax({
    type: "POST",
    url: "/chart/" + $(this).data('id'),
    dataType: 'json',
    complete: function complete(data) {
      var res = data.responseJSON;
      var chart = res.chart;
      var song = res.song;
      $('#btnConfirmSelectSong').data('id', chartId);
      confirmSongImage.attr('src', 'https://dp4p6x0xfi5o9.cloudfront.net/maimai/img/cover/' + song.imageName);
      confirmSongName.html(song.title);
      confirmSongArtist.html(song.artist); // confirmSongBPM.html(song.bpm);
      // confirmSongCategory.html(song.category);
      // confirmSongDifficulty.html(chart.difficulty);
      // confirmSongLevel.html(chart.level);
      // confirmSongVersion.html(chart.version);

      $('#confirmSelectSongModal').modal('show');
    }
  });
});
$(document).on('show.bs.modal', '.modal', function () {
  var zIndex = 1040 + 10 * $('.modal:visible').length;
  $(this).css('z-index', zIndex);
  setTimeout(function () {
    return $('.modal-backdrop').not('.modal-stack').css('z-index', zIndex - 1).addClass('modal-stack');
  });
});
$(document).on('click', '#btnConfirmSelectSong', function () {
  $.ajaxSetup({
    headers: {
      "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
    }
  });
  $.ajax({
    type: "POST",
    url: $(this).attr("data-action"),
    data: {
      mapPoolId: $('#txtMapPoolId').val(),
      chartId: $('#btnConfirmSelectSong').data('id'),
      type: $('#selectMapPoolItemType').val()
    },
    dataType: 'json',
    complete: function complete(data) {
      $('#tblPool').DataTable().ajax.reload();
      $('#confirmSelectSongModal').modal('hide');
    }
  });
});
$(document).on('click', '.btn-select-song, .btn-ban-song, .btn-remove-song', function (event) {
  event.preventDefault();
  $.ajaxSetup({
    headers: {
      "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
    }
  });
  $.ajax({
    type: "POST",
    url: $(this).data("action"),
    dataType: 'json',
    complete: function complete(data) {
      $('#tblPool').DataTable().ajax.reload();
    }
  });
});
$(document).on('click', '.roulette', function (event) {
  var option = {
    speed: 10,
    duration: 3,
    stopImageNumber: $(this).data('id'),
    startCallback: function startCallback() {
      console.log('start');
    },
    slowDownCallback: function slowDownCallback() {
      console.log('slowDown');
    },
    stopCallback: function stopCallback($stopElm) {
      console.log('stop');
    }
  };
  $('div.roulette').roulette(option);
});
$(document).on('click', '#btnRoulette', function (event) {
  var option = {
    speed: 10,
    duration: 3,
    stopImageNumber: $(this).data('id'),
    startCallback: function startCallback() {
      console.log('start');
    },
    slowDownCallback: function slowDownCallback() {
      console.log('slowDown');
    },
    stopCallback: function stopCallback($stopElm) {
      console.log('stop');
    }
  };
  $('div.roulette').roulette(option);
});
/******/ })()
;
//# sourceMappingURL=edit.js.map