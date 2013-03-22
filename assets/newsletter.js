jQuery(document).ready(function( $ ){
    $('.email-form').on( 'submit', function(){
        event.preventDefault();
        $.ajax({
            data: $( this ).serialize(),
            global: false,
            success: function( msg ){
                $('.zm-msg-target').fadeIn().html( msg ).delay(2000).fadeOut();
            }
        });
    });

    $('.email-form-settings').on('submit', function(){
        event.preventDefault();
        $.ajax({
            data: $( this ).serialize(),
            global: false,
            success: function( msg ){
                $('.zm-msg-target').fadeIn().html( msg ).delay(2000).fadeOut();
                // @todo clean up later
                location.reload();
            }
        });
    });
});