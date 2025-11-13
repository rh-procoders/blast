<?php
/**
 * Leadership Block Template
 *
 * A leadership team grid with member profiles and expandable bio information
 *
 * @param   array $block The block settings and attributes.
 * @param   string $content The block inner HTML (empty).
 * @param   bool $is_preview True during AJAX preview.
 * @param   (int|string) $post_id The post ID this block is saved to.
 */

// Create id attribute allowing for custom "anchor" value.
$id = 'leadership-' . $block['id'];
if ( ! empty($block['anchor'] ) ) {
    $id = $block['anchor'];
}

// Create class attribute allowing for custom "className" and "align" values.
$classes = 'leadership-block';
if ( ! empty( $block['align'] ) ) {
    $classes .= ' align' . $block['align'];
}
if ( ! empty( $block['className'] ) ) {
    $classes .= ' ' . $block['className'];
}

// Get block fields
$heading = get_field('leadership_heading') ?: 'Our leadership';
$members = get_field('leadership_members');

$wrapper_attributes = get_block_wrapper_attributes([
	'class' => $classes,
	'id' => $id
]);
?>

<?php if (isset( $block['data']['preview_image_help'] )  ): ?>
	<?php 
	$fileUrl = str_replace(get_stylesheet_directory(), '', dirname(__FILE__), );
	echo '<img src="' . get_stylesheet_directory_uri() . $fileUrl . '/' . $block['data']['preview_image_help'] .'" style="width:100%; height:auto;">';
	?>
<?php else: ?>

<div <?php echo $wrapper_attributes; ?>>
    <?php if ($heading): ?>
        <div class="leadership-block__header">
            <h3 class="has-coral-color has-text-color has-link-color has-tomorrow-font-family has-fs-medium-paragraph-font-size"><?php echo esc_html($heading); ?></h3>
        </div>
    <?php endif; ?>

    <?php if ($members): ?>
        <div class="leadership-block__grid">
            <?php foreach ($members as $index => $member): ?>
                <?php 
                $photo = $member['member_photo'];
                $name = $member['member_name'];
                $title = $member['member_title'];
                $bio = $member['member_bio'];
                $linkedin = $member['member_linkedin'];
                ?>
                
                <div class="leadership-member" data-member-index="<?php echo $index; ?>">
                    <div class="leadership-member__card">
                        <div class="leadership-member__image-wrapper">
                            <?php if ($photo): ?>
                                <img 
                                    src="<?php echo esc_url($photo['sizes']['medium'] ?? $photo['url']); ?>" 
                                    alt="<?php echo esc_attr($name); ?>"
                                    class="leadership-member__image"
                                    width="<?php echo esc_attr($photo['width']); ?>"
                                    height="<?php echo esc_attr($photo['height']); ?>"
                                >
                            <?php endif; ?>
                            
                            <div class="leadership-member__overlay">
                                <button 
                                    class="leadership-member__info-btn" 
                                    aria-label="View <?php echo esc_attr($name); ?>'s bio"
                                    data-member="<?php echo $index; ?>"
                                >
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <rect x="0.75" y="0.75" width="22.5" height="22.5" rx="7.25" stroke="white" stroke-width="1.5"/>
                                        <path d="M12 11.25V17.25" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M12.0431 7.04167C11.8821 7.04167 11.7514 7.17233 11.7526 7.33333C11.7526 7.49433 11.8833 7.625 12.0443 7.625C12.2053 7.625 12.3359 7.49433 12.3359 7.33333C12.3359 7.17233 12.2053 7.04167 12.0431 7.04167" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>

                                </button>
                            </div>

                             <?php if ($bio): ?>
                                <div class="leadership-member__bio" data-member-bio="<?php echo $index; ?>">
                                    <div class="leadership-bio-container">
                                        <div class="leadership-member__bio-header">
                                            
                                            <button 
                                                class="leadership-member__bio-close" 
                                                aria-label="Close bio"
                                                data-close-bio="<?php echo $index; ?>"
                                            >
                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                    <path d="M18 6L6 18M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                            </button>
                                        </div>
                                        <div class="leadership-member__bio-content">
                                            <?php echo $bio; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="leadership-member__content">
                            <div class="leadership-member__content-info">
                                <h3 class="leadership-member__name"><?php echo esc_html($name); ?></h3>
                                <p class="leadership-member__title"><?php echo esc_html($title); ?></p>
                            </div>
                            <?php if ($linkedin): ?>
                                <a 
                                    href="<?php echo esc_url($linkedin); ?>" 
                                    class="leadership-member__linkedin-btn"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    aria-label="View <?php echo esc_attr($name); ?>'s LinkedIn profile"
                                >
                                <svg class="leadership-member__linkedin-icon" width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M14.8755 14.8755H11.8191V9.66969C11.8191 8.2424 11.2767 7.44478 10.147 7.44478C8.91805 7.44478 8.27596 8.27482 8.27596 9.66969V14.8755H5.33039V4.95851H8.27596V6.29433C8.27596 6.29433 9.16162 4.65553 11.2661 4.65553C13.3696 4.65553 14.8755 5.94004 14.8755 8.59668V14.8755ZM1.81634 3.65995C0.81302 3.65995 0 2.84055 0 1.82998C0 0.8194 0.81302 0 1.81634 0C2.81967 0 3.6322 0.8194 3.6322 1.82998C3.6322 2.84055 2.81967 3.65995 1.81634 3.65995ZM0.29538 14.8755H3.36685V4.95851H0.29538V14.8755Z" fill="currentColor"/>
                                </svg>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="leadership-block__empty">
            <p>No team members added yet. Add some leadership team members in the block settings.</p>
        </div>
    <?php endif; ?>
</div>

<?php endif; ?>