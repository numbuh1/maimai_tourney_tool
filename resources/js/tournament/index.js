const tourneyModal = $('#tourneyModal')

$(document).ready(function () {

});

$(document).on('click', '.btnEditTourney', function() {
    $('#tourneyEditId').val($(this).data('id'));
    $('#txtTourneyName').val($(this).data('name'));
    $('#chkEliminated').prop('checked', $(this).data('elim'));
    tourneyModal.modal('toggle');
});

$(document).on('click', '#btnAddTourney', function() {
    $('#tourneyEditId').val('');
    $('#chkEliminated').prop('checked', false);
    tourneyModal.modal('toggle');
});

$(document).on('click', '#btnSubmitTourney', function() {
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });
    $.ajax({
        type: "POST",
        url: $(this).data('url'),
        data: {
            id: $('#tourneyEditId').val(),
            name: $('#txtTourneyName').val(),
            is_eliminated: $('#chkEliminated').prop('checked'),
        },
        dataType: 'json',
        complete: function(data) {
            location.reload();
        },
    });
});

$(document).on('click', '.btnDeleteTourney', function() {
    let confirm = window.confirm('Delete tourney "' + $(this).data('name') + '"?');
    if(!confirm)
        return;

    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });
    $.ajax({
        type: "POST",
        url: $(this).data('url'),
        dataType: 'json',
        complete: function(data) {
            location.reload();
        },
    });
});