// /js/admin-posts-script.js
(function ( $ ) {
    $( document ).on( 'click', '.bs-featured-toggle', function ( e ) {
        e.preventDefault();

        var $btn = $( this );
        var postId = $btn.data( 'post-id' );
        var nonce = $btn.data( 'nonce' );

        if (!postId || !nonce || typeof bsFeaturedPosts === 'undefined') {
            return;
        }

        $btn.prop( 'disabled', true );

        $.post(
            bsFeaturedPosts.ajax_url,
            {
                action: 'bs_toggle_post_featured',
                post_id: postId,
                nonce: nonce
            }
        ).done( function ( response ) {
            if (!response || !response.success || !response.data) {
                return;
            }

            var isFeatured = !!response.data.is_featured;
            var $icon = $btn.find( '.dashicons' );

            $icon
                .removeClass( 'dashicons-star-empty dashicons-star-filled' )
                .addClass( isFeatured ? 'dashicons-star-filled' : 'dashicons-star-empty' );

            var label = isFeatured ? 'Unmark as featured' : 'Mark as featured';
            $btn.attr( 'aria-label', label ).attr( 'title', label );

            // If you want to be extra safe, you could also update data-nonce here
            // using a fresh nonce sent back from PHP.
        } ).always( function () {
            $btn.prop( 'disabled', false );
        } );
    } );
})( jQuery );
