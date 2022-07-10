$(document).on('click', '.btnDeletePool', function() {
    let confirm = window.confirm('Delete pool "' + $(this).data('name') + '"?');
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