<?php
/**
 * Template part: Post Author
 * Displays post author with avatar and name
 *
 * @param int $user_id User ID (required)
 * @param int $avatar_size Avatar size in pixels (optional, default: 48)
 * @package blast-2025
 *
 */

$user_id     = $args['user_id'] ?? get_the_author_meta( 'ID' );
$avatar_size = $args['avatar_size'] ?? 50;

if ( ! $user_id ) {
    return;
}

$first_name   = get_the_author_meta( 'first_name', $user_id );
$last_name    = get_the_author_meta( 'last_name', $user_id );
$display_name = get_the_author_meta( 'display_name', $user_id );

if ( $first_name !== '' && $last_name !== '' ) {
    // both present: "First<br/>Last"
    $author_name = sprintf( '%s<br />%s', esc_html( $first_name ), esc_html( $last_name ) );
} else {
    // fallback: display name
    $author_name = wp_kses_post( $display_name );
}
?>

<div class="post-author">
    <div class="post-author__avatar">
        <?php bs_get_user_avatar( $user_id, $avatar_size ); ?>
    </div>
    <span class="post-author__name">
        <?= wp_kses_post( $author_name ) ?>
    </span>
</div>
