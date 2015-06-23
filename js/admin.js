var $ = jQuery.noConflict();
jQuery(document).ready(function ($) {
    var file_frame;

    jQuery.fn.uploadMediaFile = function( button, preview_media ) {
        var button_id = button.attr('id');
        var field_id = button_id.replace( 'upload', 'value' );
        var preview_id = button_id.replace( 'upload', 'preview' );

        // If the media frame already exists, reopen it.
        if ( file_frame ) {
            file_frame.open();
            return;
        }

        // Create the media frame.
        file_frame = wp.media.frames.file_frame = wp.media({
            title: jQuery( this ).data( 'uploader_title' ),
            button: {
                text: jQuery( this ).data( 'uploader_button_text' )
            },
            multiple: false,
            library: {
                type: 'image' //Only allow images
            }
        });

        // When an image is selected, run a callback.
        file_frame.on( 'select', function() {
            attachment = file_frame.state().get('selection').first().toJSON();
            jQuery("#"+field_id).val(attachment.url);
            if( preview_media ) {
                jQuery("#"+preview_id).attr('src',attachment.url).show();
            }
        });

        // Finally, open the modal
        file_frame.open();
    };

    jQuery('.image_upload_button').click(function() {
        jQuery.fn.uploadMediaFile( jQuery(this), true );
    });

    jQuery('.image_delete_button').click(function() {
        jQuery( '.image_data_field' ).val( '' );
        jQuery( '.image_preview' ).attr('src','').hide();
        return false;
    });
});