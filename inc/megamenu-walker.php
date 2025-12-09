<?php

class Custom_Mega_Menu_Walker extends Walker_Nav_Menu {
    public function start_lvl( &$output, $depth = 0, $args = null ) {
        if ( $depth === 0 ) {
            $output .= '<div class="mega-menu"><div class="mega-menu-inner">';
        }
    }

    public function end_lvl( &$output, $depth = 0, $args = null ) {
        if ( $depth === 0 ) {
            $output .= '</div></div>';
        }
    }

    public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
        $is_mega_menu = get_field('enable_menga_menu', $item);
        $classes = implode( ' ', $item->classes );

        $classes .= $is_mega_menu ? ' is-mega-menu ' : '';
        if($is_mega_menu){
            $output .= "<li class='$classes'><span class='menu-link'>{$item->title}</span>";
        }else{
            $output .= "<li class='$classes'><a href='{$item->url}' class='menu-link'>{$item->title}</a>";
        }

        if ( $depth === 0 && $is_mega_menu ) {
            $structure = get_field('mega_menu_structure', $item);
            $columns = intval($structure['menu_columns']);
            $top_labels = [];
            $output .= '<div class="mega-columns columns-' . $columns . '">';

            for ( $i = 1; $i <= $columns; $i++ ) {
                $col = $structure["column_$i"];
                $style = $col["block_style_$i"] ?? 'menu';
                $featured_class = $style === 'featured' ? 'mega-column__featured-block' : '';
                $cta_bottom = $col['cta_bottom'] ?? null;

                $top_label_text = $col["title_label"] ?? null;

                if ($top_label_text) {
                    $top_labels[] = $top_label_text;
                }

                $output .= '<div class="mega-column column-' . $i . ' '. $featured_class .'">';
                if(!empty($top_labels && $style !== 'featured')){
                    $output .= '<div class="menu-top-title">' . $top_label_text . '</div>';
                }
                if ( $style === 'menu' && !empty($col['menu_items']) ) {
                    $output .= '<ul class="mega-menu-list">';
                    foreach ( $col['menu_items'] as $item_data ) {
                        $link = $item_data['link'];
                        $item_icon = $item_data['icon'] ?? null;
                        $item_icon_only = $item_data['icon_only'] ?? null;
                        $link_title = $link['title'] ?? '';
                        $link_url = $link['url'] ?? '#/';
                        $desc = $item_data['description'];
                        $sub_item = $item_data['sub_item'] ? 'subitem-menu-style' : '';
                        $class_with_icon = '';



                        if($item_icon){
                            $icon = wp_get_attachment_image(
                                $item_icon,
                                'full', // can change thumbnail size here.
                                false,
                                array(
                                    'class' => 'menu-sub-item__icon',
                                    'alt'   => wp_strip_all_tags( $link_title ),
                                )
                            );
                        } else {
                            $icon = '';
                        }

                        if($icon && $item_icon_only){
                            $class_with_icon = 'menu-sub-item__with-icon_only';
                        } else if($icon){
                            $class_with_icon = 'menu-sub-item__with-icon';
                        }

                        $output .= '<li class="menu-sub-item '. $class_with_icon .'">';
                        $output .= '<a href="' . esc_url($link_url) . '" class="menu-sub-item__wrapper">';
                        if ($icon) $output .= $icon;
                        $output .= '<div class="menu-sub-item__content">';
                        if ($link) $output .= '<span class="menu-sub-item__link '. $sub_item .'">' . esc_html($link['title']) . '</span>';
                        if ($desc) $output .= '<p class="menu-sub-item__description">' . esc_html($desc) . '</p>';
                        $output .= '</div>';
                        $output .= '</a>';
                        $output .= '</li>';
                    }
                    $output .= '</ul>';
                }

                if ( $style === 'featured' && !empty($col['feature_block_content']) ) {
                    $feature = $col['feature_block_content'];
                    $is_popup = $feature["is_popup_video_$i"] ?? false;
                    $image = wp_get_attachment_image(
                                    $feature['feature_image'],
                                    'full', // can change thumbnail size here.
                                    false,
                                    array(
                                        'class' => 'featured-image',
                                        'alt'   => wp_strip_all_tags( $feature['title'] ),
                                    )
                                );
                    if (!empty($feature['link']) && !$is_popup) {
                        $output .= '<a href="' . esc_url($feature['link']['url']) . '" class="cy-btn cy-btn--link-light-blue cy-btn--has-icon">';
                    }
                    $output .= '<div class="feature-block">';
                    if (!empty($feature['top_label_title'])) {
                        $output .= '<span class="top-label">' . esc_html($feature['top_label_title']) . '</span>';
                    }

                    if ( $is_popup && $image ) {
                        $output .= '<div class="featured-image-wrap" data-modal-opener>';
                        $output .= $image;
                        $output .= '<div class="play-btn">';
                        $output .= return_sprite_svg( 'icon-modal-play-btn', 62, 62 );
                        $output .= '</div>';
                        $output .= '</div>';
                    }

                    if( !$is_popup && $image ) {
                        $output .= $image;
                    }

                    if (!empty($feature['title'])) {
                        $output .= '<div class="h3 featured-title">' . esc_html($feature['title']) . '</div>';
                    }

                    if ($is_popup) {

                        $output .= '<a href="#!" rel="nofollow" data-modal-opener class="cy-btn cy-btn--link-light-blue cy-btn--has-icon">Watch now';
                        $output .= return_sprite_svg( 'icon-tabs-arrow-read-more', 22, 11 );
                        $output .= '</a>';

                        $output .= '<dialog data-modal-dialog data-modal-video data-video-source="' . esc_attr($feature['video_type']) . '" data-video-id="' . esc_attr($feature['video_id']) . '">';
                        $output .= '<button data-modal-closer>' .return_sprite_svg( "icon-dialog-close", 320, 512 ) .'</button><div data-modal-body></div></dialog>';

                    }

                    if (!empty($feature['link']) && !$is_popup) {
                        $output .= '</a">';
                    }

                    $output .= '</div>';
                }

                if($cta_bottom){
                    $cta_url = esc_url( $cta_bottom['url'] );
                    $cta_target =  $cta_bottom['target'] ?? '_self';
                    $cta_title =  $cta_bottom['title'] ?? 'View more';
                    $output .='<a href="'. $cta_url .'" target="'. $cta_target . '" class="cy-btn cy-btn--fill-light-blue menu-cta-bottom"> <span>'. $cta_title .'</span></a>';
                }

                $output .= '</div>'; // end column
            }

            $output .= '</div>'; // end mega-columns
        }
    }

    public function end_el( &$output, $item, $depth = 0, $args = null ) {
        $output .= '</li>';
    }
}
