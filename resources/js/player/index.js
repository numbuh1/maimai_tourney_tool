const playerModal = $('#playerModal')

$(document).ready(function () {

});

$(document).on('click', '.btnEditPlayer', function() {
    $('#playerEditId').val($(this).data('id'));
    $('#txtPlayerName').val($(this).data('name'));
    $('#chkEliminated').prop('checked', $(this).data('elim'));
    playerModal.modal('toggle');
});

$(document).on('click', '#btnAddPlayer', function() {
    $('#playerEditId').val('');
    $('#chkEliminated').prop('checked', false);
    playerModal.modal('toggle');
});

$(document).on('click', '#btnSubmitPlayer', function() {
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });
    $.ajax({
        type: "POST",
        url: $(this).data('url'),
        data: {
            id: $('#playerEditId').val(),
            name: $('#txtPlayerName').val(),
            is_eliminated: $('#chkEliminated').val(),
        },
        dataType: 'json',
        complete: function(data) {
            location.reload();
        },
    });
});

$(document).on('click', '.btnDeletePlayer', function() {
    let confirm = window.confirm('Delete player "' + $(this).data('name') + '"?');
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