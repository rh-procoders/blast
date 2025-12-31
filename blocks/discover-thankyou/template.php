<?php
/**
 * Features Section Block Template
 *
 * A features section with gradient background, heading, and feature cards with icons
 *
 * @param array $block The block settings and attributes.
 * @param string $content The block inner HTML (empty).
 * @param bool $is_preview True during AJAX preview.
 * @param (int|string) $post_id The post ID this block is saved to.
 */

// Create id attribute allowing for custom "anchor" value.
$block_name = 'discover-ty';
$id         = $block_name . '-' . $block['id'];
if ( ! empty( $block['anchor'] ) ) {
    $id = $block['anchor'];
}

// Create class attribute allowing for custom "className" and "align" values.
$classes = $block_name;
if ( ! empty( $block['align'] ) ) {
    $classes .= ' align' . $block['align'];
}
if ( ! empty( $block['className'] ) ) {
    $classes .= ' ' . $block['className'];
}

// ACF Fields.
$fields  = get_field( $block_name );
$heading = $fields["{$block_name}__heading"] ?? null;
$posts   = $fields["{$block_name}__posts"] ?? null;

$wrapper_attributes = get_block_wrapper_attributes( [
        'class' => $classes,
        'id'    => $id
] );
?>

<?php if ( isset( $block['data']['preview_image_help'] ) ): ?>
    <?php
    $fileUrl = str_replace( get_stylesheet_directory(), '', dirname( __FILE__ ) );
    echo '<img src="' . get_stylesheet_directory_uri() . $fileUrl . '/' . $block['data']['preview_image_help'] . '" style="width:100%; height:auto;">';
    ?>
<?php else: ?>

    <section <?php echo $wrapper_attributes; ?>>
        <div class="<?= $block_name ?>__wrapper">
            <div class="container">
                <div class="<?= $block_name ?>__content">
                    <?php
                    if ( $heading ) : ?>
                        <h2 class="<?= $block_name ?>__heading">
                            <?= wp_kses_post( $heading ); ?>
                        </h2>
                    <?php
                    endif; ?>

                    <?php
                    if ( $posts && is_array( $posts ) ) :
                        // Limit to first 3 posts if more are selected
                        $post_ids = array_slice( $posts, 0, 3 );

                        // Query the selected posts
                        $posts_query = new WP_Query( [
                                'post_type'           => 'post',
                                'post_status'         => 'publish',
                                'post__in'            => $post_ids,
                                'orderby'             => 'post__in',
                                'posts_per_page'      => 3,
                                'ignore_sticky_posts' => true,
                        ] );

                        if ( $posts_query->have_posts() ) : ?>
                            <div class="<?= $block_name ?>__posts">
                                <?php
                                while ($posts_query->have_posts()) :
                                    $posts_query->the_post();

                                    // Get post categories (exclude Uncategorized)
                                    $categories = get_the_category();
                                    if ( $categories ) {
                                        $categories = array_filter( $categories, function ( $cat ) {
                                            return $cat->slug !== 'uncategorized';
                                        } );
                                    }

                                    // Get reading time
                                    $reading_time = bs_get_reading_time();
                                    ?>

                                    <a href="<?= esc_url( get_permalink() ) ?>" class="<?= $block_name ?>__post-item-link">
                                        <article class="<?= $block_name ?>__post-item card-block">
                                            <!-- Thumbnail -->
                                            <div class="<?= $block_name ?>__post-item-image-wrapper">
                                                <?php if ( has_post_thumbnail() ): ?>
                                                    <figure class="<?= $block_name ?>__post-item-image-figure">
                                                        <?php the_post_thumbnail( 'large', [ 'class' => $block_name . '__post-item-image' ] ); ?>
                                                    </figure>
                                                <?php else: ?>
                                                    <div class="<?= $block_name ?>__post-item-image-placeholder">
                                                        <!-- Placeholder for posts without featured image -->
                                                    </div>
                                                <?php endif; ?>
                                            </div>

                                            <!-- Content -->
                                            <div class="<?= $block_name ?>__post-item-content">
                                                <!-- Meta: Categories + Read Time -->
                                                <div class="<?= $block_name ?>__post-item-meta">
                                                    <?php if ( ! empty( $categories ) ): ?>
                                                        <div class="<?= $block_name ?>__post-item-categories">
                                                            <?php foreach ($categories as $category): ?>
                                                                <span class="<?= $block_name ?>__post-item-category">
                                                                    <?= esc_html( $category->name ) ?>
                                                                </span>
                                                            <?php endforeach; ?>
                                                        </div>
                                                    <?php endif; ?>

                                                    <span class="<?= $block_name ?>__post-item-read-time">
                                                        <?php
                                                        /* translators: %d: reading time in minutes */
                                                        printf( esc_html__( '%d min read', 'blast-2025' ), $reading_time );
                                                        ?>
                                                    </span>
                                                </div>

                                                <!-- Title -->
                                                <h3 class="<?= $block_name ?>__post-item-title">
                                                    <?= esc_html( get_the_title() ) ?>
                                                </h3>

                                                <!-- Excerpt -->
                                                <div class="<?= $block_name ?>__post-item-excerpt">
                                                    <?= wp_kses_post( get_the_excerpt() ) ?>
                                                </div>

                                                <!-- Footer: Author (inline name) -->
                                                <div class="<?= $block_name ?>__post-item-footer">
                                                    <?php
                                                    get_template_part( 'template-parts/components/author', null, [
                                                            'user_id'     => get_the_author_meta( 'ID' ),
                                                            'avatar_size' => 50,
                                                            'inline'      => true
                                                    ] );
                                                    ?>
                                                </div>
                                            </div>
                                        </article>
                                    </a>

                                <?php
                                endwhile;
                                wp_reset_postdata();
                                ?>
                            </div>
                        <?php
                        endif;
                    endif; ?>
                </div>
            </div>
        </div>
    </section>

<?php endif; ?>
