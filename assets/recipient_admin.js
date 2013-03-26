jQuery().ready(function( $ ){

    if ( jQuery().chosen ){
        $(".chzn-select").chosen();
    }

    $('#titlewrap label').html('Recipients Email Here');

    $.ajaxSetup({
        type: "POST",
        url: ajaxurl
    });

    $('.add-recipient-form').on('submit', function( event ){
        event.preventDefault();
        $.ajax({
            data: '&action=addRecipient&' + $(this).serialize(),
            dataType: 'json',
            success: function( msg ){
                console.log( msg );
                html = "<tr><td>"+msg.first_name+"</td><td>"+msg.last_name+"</td><td>"+msg.email+"</td><td>list</td></tr>";
                $( '.recipient-table tbody' ).prepend( html );
            }
        });
    });
});
