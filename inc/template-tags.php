<?php
/**
 * Custom template tags for this theme
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package blast-2025
 */

if ( ! function_exists( 'blast_theme_wp_posted_on' ) ) :
	/**
	 * Prints HTML with meta information for the current post-date/time.
	 */
	function blast_theme_wp_posted_on() {
		$time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
		if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
			$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time>';
		}

		$time_string = sprintf(
			$time_string,
			esc_attr( get_the_date( DATE_W3C ) ),
			esc_html( get_the_date() )
		);

		$posted_on = sprintf(
			/* translators: %s: post date. */
			esc_html_x( '%s', 'post date', 'blast-2025' ),
			$time_string
		);

		echo '<span class="posted-on">' . $posted_on . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

	}
endif;

if ( ! function_exists( 'blast_theme_wp_posted_by' ) ) :
	/**
	 * Prints HTML with meta information for the current author.
	 */
	function blast_theme_wp_posted_by() {
		$byline = sprintf(
			/* translators: %s: post author. */
			esc_html_x( '%s', 'post author', 'blast-2025' ),
			'<span class="author vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author() ) . '</a></span>'
		);

		echo '<span class="byline"> ' . $byline . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

	}
endif;

if ( ! function_exists( 'blast_theme_wp_entry_footer' ) ) :
	/**
	 * Prints HTML with meta information for the categories, tags and comments.
	 */
	function blast_theme_wp_entry_footer() {
		// Hide category and tag text for pages.
		if ( 'post' === get_post_type() ) {
			/* translators: used between list items, there is a space after the comma */
			$categories_list = get_the_category_list( esc_html__( ', ', 'blast-2025' ) );
			if ( $categories_list ) {
				/* translators: 1: list of categories. */
				printf( '<span class="cat-links">' . esc_html__( 'Posted in %1$s', 'blast-2025' ) . '</span>', $categories_list ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}

			/* translators: used between list items, there is a space after the comma */
			$tags_list = get_the_tag_list( '', esc_html_x( ', ', 'list item separator', 'blast-2025' ) );
			if ( $tags_list ) {
				/* translators: 1: list of tags. */
				printf( '<span class="tags-links">' . esc_html__( 'Tagged %1$s', 'blast-2025' ) . '</span>', $tags_list ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
		}

		if ( ! is_single() && ! post_password_required() && ( comments_open() || get_comments_number() ) ) {
			echo '<span class="comments-link">';
			comments_popup_link(
				sprintf(
					wp_kses(
						/* translators: %s: post title */
						__( 'Leave a Comment<span class="screen-reader-text"> on %s</span>', 'blast-2025' ),
						array(
							'span' => array(
								'class' => array(),
							),
						)
					),
					wp_kses_post( get_the_title() )
				)
			);
			echo '</span>';
		}

		edit_post_link(
			sprintf(
				wp_kses(
					/* translators: %s: Name of current post. Only visible to screen readers */
					__( 'Edit <span class="screen-reader-text">%s</span>', 'blast-2025' ),
					array(
						'span' => array(
							'class' => array(),
						),
					)
				),
				wp_kses_post( get_the_title() )
			),
			'<span class="edit-link">',
			'</span>'
		);
	}
endif;

if ( ! function_exists( 'blast_theme_wp_post_thumbnail' ) ) :
	/**
	 * Displays an optional post thumbnail.
	 *
	 * Wraps the post thumbnail in an anchor element on index views, or a div
	 * element when on single views.
	 */
	function blast_theme_wp_post_thumbnail() {
		if ( post_password_required() || is_attachment() || ! has_post_thumbnail() ) {
			return;
		}

		if ( is_singular() ) :
			?>

			<div class="post-thumbnail">
				<?php the_post_thumbnail(); ?>
			</div><!-- .post-thumbnail -->

		<?php else : ?>

			<a class="post-thumbnail" href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">
				<?php
					the_post_thumbnail(
						'post-thumbnail',
						array(
							'alt' => the_title_attribute(
								array(
									'echo' => false,
								)
							),
						)
					);
				?>
			</a>

			<?php
		endif; // End is_singular().
	}
endif;

if ( ! function_exists( 'wp_body_open' ) ) :
	/**
	 * Shim for sites older than 5.2.
	 *
	 * @link https://core.trac.wordpress.org/ticket/12563
	 */
	function wp_body_open() {
		do_action( 'wp_body_open' );
	}
endif;

/**
 *  Function tries to retrieve "custom_avatar"
 *  custom field set for user profile with ACF Pro
 *
 *  If nothing is found, tries to get default WooCommerce
 *  Gravatar result for the given user ID
 *
 * @param int $user_id
 * @param int $size
 *
 * @return void
 */
function bs_get_user_avatar( int $user_id, int $size = 59 ): void
{
    // First, try returning Custom Avatar from ACF Fields
    if ( function_exists( 'get_field' ) ) {
        $avatar = get_field( 'bs_custom_avatar', "user_" . $user_id );
        if ( $avatar ) {
            echo wp_kses_post( "<img
                        src='{$avatar['url']}'
                        alt='{$avatar['title']}'
                        width='{$size}' width='{$size}'
                        class='avatar avatar-{$size} photo'>" );
        } else {
            echo get_avatar( $user_id, $size );
        }
    } else {
        echo get_avatar( $user_id, $size );
    }
}

/**
 * Calculate estimated reading time for a post
 * Based on average reading speed of 200 words per minute
 *
 * @param int|null $post_id Post ID (optional, defaults to current post)
 * @return int Reading time in minutes (minimum 1)
 */
function bs_get_reading_time( ?int $post_id = null ): int
{
    if ( ! $post_id ) {
        $post_id = get_the_ID();
    }

    $post = get_post( $post_id );
    if ( ! $post ) {
        return 1;
    }

    // Get post content and strip HTML tags
    $content = strip_tags( $post->post_content );
    $content = strip_shortcodes( $content );

    // Count words
    $word_count = str_word_count( $content );

    // Calculate reading time (200 words per minute)
    $reading_time = ceil( $word_count / 200 );

    // Minimum 1 minute
    return max( 1, $reading_time );
}
