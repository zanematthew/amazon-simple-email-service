jQuery(document).ready(function($){

    $.ajaxSetup({
        type: "POST",
        url: ajaxurl
    });

    // var template_id = null;
    if ( $( '#bmx_re_email_template_select' ).length ){
        var template_id = $('#bmx_re_email_template_select option:selected' ).val();

        $('#bmx_re_email_template_select').on('change', function(e) {
            template_id = $('[value=' + $(this).val() + ']', this).val();
        });
    } else {
        var template_id = $( '#post_ID' ).val();
    }

    $('#dim').on('click', function(){

        var user_email = $('#bmx_re_test_email').val()

        data = {
            "action": "sendEmail",
            "user_email": user_email,
            "template_id": template_id
        };

        $.ajax({
            data: data,
            success: function( msg ){
                console.log( 'msg: ' + msg );
                    if ( msg == 1 ){
                        message = 'Successfully deployed Test email to <em>' + user_email + '</em>.';
                    } else {
                        message = 'At some point we\'ll track these, but for now...<br />Error: <strong>'+msg+'</strong>';
                    }

                    $('.status-target').fadeIn().html( message );
                    $('.status-target').delay( 2000 ).fadeOut();
                }
        });
    });

    $('.preview-handle').on('click', function(){
        data = {
            "action": $(this).attr('data-action'),
            "user_email": $('#bmx_re_test_email').val(),
            "template_id": template_id
        };

        $('iframe.preview-target').contents().find('body').empty();

        $.ajax({
            data: data,
            success: function( msg ){
                $('.preview-target').fadeIn().html( msg );
                $('iframe.preview-target').contents().find('body').fadeIn().html( msg );
                $('html,body').animate({
                    scrollTop: $(".preview-target").offset().top
                });
            }
        });
    });

    $('.deploy-handle').on('click', function( event ){

        event.preventDefault();

        var list = [];

        $('.deploy-meta-box-list li input[type="checkbox"]').each(function(){
            if ( $(this).is(':checked') ){
                list.push( $(this).val() );
            }
        });

        data = {
            "action": "deployEmails",
            "template_id": template_id,
            "list": list
        };

        $.ajax({
            data: data,
            success: function( msg ){
                console.log( msg );
            }
        });
    });

    $('#postexcerpt span').html('Plain Text Newsletter');
});