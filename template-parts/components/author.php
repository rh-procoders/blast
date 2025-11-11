<?php
/**
 * Template part: Post Author
 * Displays post author with avatar and name
 *
 * @param int  $user_id          User ID (required)
 * @param int  $avatar_size      Avatar size in pixels (optional, default: 50)
 * @param bool $inline           Display name inline with space instead of line break (optional, default: false)
 * @param bool $show_job_position Show job position from ACF field (optional, default: false)
 * @param bool $show_bio         Show biographical info (optional, default: false)
 * @package blast-2025
 *
 */

$user_id           = $args['user_id'] ?? get_the_author_meta( 'ID' );
$avatar_size       = $args['avatar_size'] ?? 50;
$inline            = $args['inline'] ?? false;
$show_job_position = $args['show_job_position'] ?? false;
$show_bio          = $args['show_bio'] ?? false;

if ( ! $user_id ) {
    return;
}

// Get author data
$first_name   = get_the_author_meta( 'first_name', $user_id );
$last_name    = get_the_author_meta( 'last_name', $user_id );
$display_name = get_the_author_meta( 'display_name', $user_id );

// Build author name
if ( $first_name !== '' && $last_name !== '' ) {
    // Conditional separator: space for inline, <br/> for multi-line
    $separator   = $inline ? ' ' : '<br />';
    $author_name = sprintf( '%s%s%s', esc_html( $first_name ), $separator, esc_html( $last_name ) );
} else {
    // fallback: display name
    $author_name = wp_kses_post( $display_name );
}

// Get optional fields
$job_position = $show_job_position ? get_field( 'bs_author__job_position', 'user_' . $user_id ) : '';
$bio          = $show_bio ? get_the_author_meta( 'description', $user_id ) : '';
?>

<div class="post-author">
    <div class="post-author__avatar">
        <?php bs_get_user_avatar( $user_id, $avatar_size ); ?>
    </div>

    <div class="post-author__info">
        <span class="post-author__name">
            <?= wp_kses_post( $author_name ) ?>
        </span>

        <?php if ( $show_job_position && ! empty( $job_position ) ) : ?>
            <span class="post-author__job-position">
                <?= esc_html( $job_position ) ?>
            </span>
        <?php endif; ?>
    </div>

    <?php if ( $show_bio && ! empty( $bio ) ) : ?>
        <div class="post-author__bio">
            <?= wp_kses_post( wpautop( $bio ) ) ?>
        </div>
    <?php endif; ?>
</div>
