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
$(document).ready(function () {
  $('.modal').on('shown.bs.modal', function (e) {
    $(this).find('.select2').select2({
      dropdownParent: $(this).find('.modal-content')
    });
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
        var html = '<div class="col-md-2">' + '<div class="card" style="width: 8rem;">' + '<img class="card-img-top chart-thumbnail chart-' + charts[i].difficulty + '" src="https://dp4p6x0xfi5o9.cloudfront.net/maimai/img/cover/' + charts[i].imageName + '" alt="Card image cap">' + '<img src="https://maimaidx-eng.com/maimai-mobile/img/music_' + kind + '.png" class="music_kind_icon ">' + '<div class="music_lv_back text-center">' + charts[i].level + '</div>' + '</div>' + '</div>';
        $('#song-pane').append(html);
      }
    }
  });
});
$(document).on('click', '#btnHideList', function () {
  $('songList').hide();
});
$(document).on('click', '#btnShowList', function () {
  $('songList').show();
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
        var html = '<div class="col-md-2">' + '<div class="card" style="width: 8rem;">' + '<img class="card-img-top chart-thumbnail chart-' + charts[i].difficulty + '" src="https://dp4p6x0xfi5o9.cloudfront.net/maimai/img/cover/' + charts[i].imageName + '" alt="Card image cap">' + '<img src="https://maimaidx-eng.com/maimai-mobile/img/music_' + kind + '.png" class="music_kind_icon ">' + '<div class="music_lv_back text-center">' + charts[i].level + '</div>' + '</div>' + '</div>';
        $('#song-pane').append(html);
      }
    }
  });
});
/******/ })()
;
//# sourceMappingURL=edit.js.map