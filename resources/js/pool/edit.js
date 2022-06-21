const selectCategory = $('#selectSongModal #selectCategory')
const selectVersion = $('#selectSongModal #selectVersion')
const selectDiff = $('#selectSongModal #selectDiff')
const selectLevelMin = $('#selectSongModal #selectLevelMin')
const selectLevelMax = $('#selectSongModal #selectLevelMax')
const selectRandomNumber = $('#selectSongModal #selectRandomNumber')

const confirmSongImage = $('#confirmSelectSongModal #songImage')
const confirmSongName = $('#confirmSelectSongModal #songName')
const confirmSongArtist = $('#confirmSelectSongModal #songArtist')
const confirmSongBPM = $('#confirmSelectSongModal #songBPM')
const confirmSongCategory = $('#confirmSelectSongModal #songCategory')
const confirmSongDifficulty = $('#confirmSelectSongModal #songDifficulty')
const confirmSongLevel = $('#confirmSelectSongModal #songLevel')
const confirmSongVersion = $('#confirmSelectSongModal #songVersion')

let poolTable = $('#tblPool')

$(document).ready(function() {
    $('.modal').on('shown.bs.modal', function (e) {
        $(this).find('.select2').select2({
            dropdownParent: $(this).find('.modal-content')
        });
    })

    poolTable.DataTable({
        "processing": true,
        "serverSide": true,
        'dom': 'lrtip',
        "bPaginate": false,
        "ajax":{
            "url": $('#tblPool').data('url'),
            "dataType": "json",
            "type": "POST",
            "data": function (data) {
                data._token = $('meta[name="csrf-token"]').attr('content');
            }
        },
        "columns": [
            { "data": 'order' },
            { "data": 'song' },
            { 'data': 'songAction' },
            { "data": 'slotAction' },
        ],
        "order": [[ 0, "asc" ]],
        "columnDefs": [
            {
                "targets": 0,
                "className": 'd-none'
            }
        ]
    });
});

$(document).on('click', '#btnAddSong', function() {
    $('#selectSongModal').modal('show');
});

$(document).on('click', '#btnShowSongs', function() {
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
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
        complete: function(data) {
            console.log(JSON.parse(data.responseText));
            let charts = JSON.parse(data.responseText);
            $('#song-pane').html('');
            for (var i = 0; i < charts.length; i++) {
                let kind = charts[i].type == 'dx' ? 'dx' : 'standard';
                let html =  '<div class="col-md-2 col-6">' +
                                '<a href="#" class="song-select text-dark" data-id="' + charts[i].chart_id + '"><div class="card" style="width: 8rem;">' +
                                    '<img class="card-img-top chart-thumbnail chart-' + charts[i].difficulty + '" src="https://dp4p6x0xfi5o9.cloudfront.net/maimai/img/cover/' + charts[i].imageName + '" alt="Card image cap">' +
                                    '<img src="https://maimaidx-eng.com/maimai-mobile/img/music_' + kind + '.png" class="music_kind_icon ">' +
                                    '<div class="music_lv_back text-center">' + charts[i].level + '</div>' +
                                '</div></a>' +
                            '</div>';

                $('#song-pane').append(html);
            }
        },
    });
});

$(document).on('click', '#btnHideList', function() {
    $('#songList').hide();
});

$(document).on('click', '#btnShowList', function() {
    $('#songList').show();
});

$(document).on('click', '#btnRandomSongs', function() {
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
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
        complete: function(data) {
            let charts = JSON.parse(data.responseText);
            $('#song-pane').html('');
            for (var i = 0; i < charts.length; i++) {
                let kind = charts[i].type == 'dx' ? 'dx' : 'standard';
                let html =  '<div class="col-md-2 col-6">' +
                                '<a href="#" class="song-select text-dark" data-id="' + charts[i].chart_id + '"><div class="card" style="width: 8rem;">' +
                                    '<img class="card-img-top chart-thumbnail chart-' + charts[i].difficulty + '" src="https://dp4p6x0xfi5o9.cloudfront.net/maimai/img/cover/' + charts[i].imageName + '" alt="Card image cap">' +
                                    '<img src="https://maimaidx-eng.com/maimai-mobile/img/music_' + kind + '.png" class="music_kind_icon ">' +
                                    '<div class="music_lv_back text-center">' + charts[i].level + '</div>' +
                                '</div></a>' +
                            '</div>';

                $('#song-pane').append(html);
            }
        },
    });
});

$(document).on('click', '.song-select', function() {
    let chartId = $(this).data('id');
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });
    $.ajax({
        type: "POST",
        url: "/chart/" + $(this).data('id'),
        dataType: 'json',
        complete: function(data) {
            let res = data.responseJSON;
            let chart = res.chart;
            let song = res.song;
            
            $('#btnConfirmSelectSong').data('id', chartId);
            confirmSongImage.attr('src','https://dp4p6x0xfi5o9.cloudfront.net/maimai/img/cover/' + song.imageName);
            confirmSongName.html(song.title);
            confirmSongArtist.html(song.artist);
            // confirmSongBPM.html(song.bpm);
            // confirmSongCategory.html(song.category);
            // confirmSongDifficulty.html(chart.difficulty);
            // confirmSongLevel.html(chart.level);
            // confirmSongVersion.html(chart.version);

            $('#confirmSelectSongModal').modal('show');
        },
    });
});

$(document).on('show.bs.modal', '.modal', function() {
  const zIndex = 1040 + 10 * $('.modal:visible').length;
  $(this).css('z-index', zIndex);
  setTimeout(() => $('.modal-backdrop').not('.modal-stack').css('z-index', zIndex - 1).addClass('modal-stack'));
});

$(document).on('click', '#btnConfirmSelectSong', function() {
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });
    $.ajax({
        type: "POST",
        url: $(this).attr("data-action"),
        data: {
            mapPoolId: $('#txtMapPoolId').val(),
            chartId: $('#btnConfirmSelectSong').data('id'),
            type: $('#selectMapPoolItemType').val(),
        },
        dataType: 'json',
        complete: function(data) {
            $('#tblPool').DataTable().ajax.reload();
            $('#confirmSelectSongModal').modal('hide');
        },
    });
});

$(document).on('click', '.btn-select-song, .btn-ban-song, .btn-remove-song', function(event) {
    event.preventDefault();
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });
    $.ajax({
        type: "POST",
        url: $(this).data("action"),
        dataType: 'json',
        complete: function(data) {
            $('#tblPool').DataTable().ajax.reload();
        },
    });
});

$(document).on('click', '.roulette', function(event) {
    var option = {
        speed : 10,
        duration : 3,
        stopImageNumber : $(this).data('id'),
        startCallback : function() {
            console.log('start');
        },
        slowDownCallback : function() {
            console.log('slowDown');
        },
        stopCallback : function($stopElm) {
            console.log('stop');
        }
    }
    $('div.roulette').roulette(option);
});

$(document).on('click', '#btnRoulette', function(event) {
    var option = {
        speed : 10,
        duration : 3,
        stopImageNumber : $(this).data('id'),
        startCallback : function() {
            console.log('start');
        },
        slowDownCallback : function() {
            console.log('slowDown');
        },
        stopCallback : function($stopElm) {
            console.log('stop');
        }
    }
    $('div.roulette').roulette(option);
});

$('body').on('hidden.bs.modal', function () {
    if($('.modal.in').length > 0)
    {
        $('body').addClass('modal-open');
    }
});