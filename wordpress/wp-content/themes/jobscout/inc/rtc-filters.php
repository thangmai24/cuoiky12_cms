<?php
/**
 * Filter to modify functionality of RTC plugin.
 *
 * @package JobScout
 */

if( ! function_exists( 'jobscout_cta_section_bgcolor_filter' ) ){
	/**
	 * Filter to add bg color of cta section widget
	 */    
	function jobscout_cta_section_bgcolor_filter(){
		return '#2ace5e';
	}
}
add_filter( 'rrtc_cta_bg_color', 'jobscout_cta_section_bgcolor_filter' );

if( ! function_exists( 'jobscout_cta_btn_alignment_filter' ) ){
	/**
	 * Filter to add btn alignment of cta section widget
	 */    
	function jobscout_cta_btn_alignment_filter(){
		return 'centered';
	}
}
add_filter( 'rrtc_cta_btn_alignment', 'jobscout_cta_btn_alignment_filter' );

if( ! function_exists( 'jobscout_theme_slug' ) ){
	/**
	 * Filter to add theme slug
	 */    
	function jobscout_theme_slug(){
		return 'jobscout';
	}
}
add_filter( 'theme_slug', 'jobscout_theme_slug' );

if( ! function_exists( 'jobscout_cta_widget_content_filter' ) ){
	/**
	 * Filter to override CTA widget content
	 */    
	function jobscout_cta_widget_content_filter( $html, $args, $instance ){
		// Check if this is the CTA widget we want to modify
		if( ! empty( $instance['title'] ) && $instance['title'] === 'Build Your Online Team' ){
			// New content
			$new_title = 'CAREER WITH US';
			$new_content = 'Plan Do See Global is a hospitality group founded in Japan and rooted in "Omotenashi", the Japanese principle of selfless hospitality. We strive to deliver unforgettable and bespoke experiences, to understand local cultures like natives, to provide service that is warm but not intrusive, and to foresee our guests\' every need at all times. That is our sole mission and purpose.

We are experts in all stages of project development: concept, design, implementation and management. We love to find unique ways to create experiences that surprise and delight guests and customers.';
			$new_button_text = 'MORE ABOUT US';
			
			// Get button URL from instance or use default
			$button_url = ! empty( $instance['button1_url'] ) ? $instance['button1_url'] : '#';
			$target = ! empty( $instance['target'] ) ? ' target="_blank"' : '';
			
			// Get background settings
			$bgcolor = apply_filters('rrtc_cta_bg_color','#fff');
			$widget_bg_color = ! empty($instance['widget-bg-color']) ? esc_attr($instance['widget-bg-color']) : $bgcolor;
			$widget_bg_image = !empty($instance['widget-bg-image']) ? esc_attr($instance['widget-bg-image']) : '';
			$button_alignment = ! empty( $instance['button_alignment'] ) ? $instance['button_alignment'] : 'centered';
			
			// Always use the specific Japanese garden image
			$japanese_image_url = '';
			$japanese_image_id = '';
			$upload_dir = wp_upload_dir();
			
			// Method 1: Try direct URL first (most reliable)
			$image_path = $upload_dir['basedir'] . '/2025/11/15-dia-diem-ngam-hoa-anh-dao-dep-nhat-nhat-ban-2024-c7b-7131848.jpg';
			if( file_exists( $image_path ) ){
				$japanese_image_url = str_replace( $upload_dir['basedir'], $upload_dir['baseurl'], $image_path );
			}
			
			// Method 2: If file doesn't exist, try to find by attachment ID in database
			if( ! $japanese_image_url ){
				global $wpdb;
				$sql = $wpdb->prepare(
					"SELECT post_id FROM $wpdb->postmeta 
					WHERE meta_key = '_wp_attached_file' 
					AND meta_value LIKE %s 
					LIMIT 1",
					'%15-dia-diem-ngam-hoa-anh-dao-dep-nhat-nhat-ban-2024-c7b-7131848%'
				);
				$result = $wpdb->get_var( $sql );
				if( $result ){
					$japanese_image_id = $result;
					$japanese_image_url = wp_get_attachment_image_url( $japanese_image_id, 'full' );
				}
			}
			
			// Method 3: Try to find by other search terms
			if( ! $japanese_image_url ){
				$search_terms = array( 'dia-diem-ngam-hoa-anh-dao', 'nhat-ban-2024' );
				foreach( $search_terms as $term ){
					$sql = $wpdb->prepare(
						"SELECT post_id FROM $wpdb->postmeta 
						WHERE meta_key = '_wp_attached_file' 
						AND meta_value LIKE %s 
						ORDER BY post_id DESC
						LIMIT 1",
						'%' . $wpdb->esc_like( $term ) . '%'
					);
					$result = $wpdb->get_var( $sql );
					if( $result ){
						$japanese_image_id = $result;
						$japanese_image_url = wp_get_attachment_image_url( $japanese_image_id, 'full' );
						break;
					}
				}
			}
			
			// Use Japanese image if found, otherwise use widget image
			$final_image_id = $japanese_image_id ? $japanese_image_id : $widget_bg_image;
			$final_image_url = $japanese_image_url ? $japanese_image_url : ( $final_image_id ? wp_get_attachment_image_url( $final_image_id, 'full' ) : '' );
			
			// Build background style
			$ctaclass = '';
			$bg = '';
			if( $final_image_url ){
				$ctaclass = ' bttk-cta-bg';
				$bg = ' style="background:url(' . esc_url( $final_image_url ) . ') no-repeat; background-size: cover; background-position: center"';
			} else {
				$ctaclass = ' text';
				$bg = ' style="background:' . sanitize_hex_color( $widget_bg_color ) . '"';
			}
			
			// Build new HTML
			ob_start();
			?>
			<div class="<?php echo esc_attr( $button_alignment . $ctaclass ); ?>"<?php echo $bg;?>>
				<div class="raratheme-cta-container">
					<?php if( $new_title ) echo $args['before_title'] . esc_html( $new_title ) . $args['after_title']; ?>
					<div class="text-holder">
						<?php echo wpautop( wp_kses_post( $new_content ) ); ?>
						<div class="button-wrap">
							<?php if( $new_button_text && $button_url ) : ?>
								<a <?php echo $target; ?> href="<?php echo esc_url( $button_url ); ?>" class="btn-cta btn-1"><?php echo esc_html( $new_button_text ); ?></a>
							<?php endif; ?>
						</div>
					</div>
				</div>
			</div>
			<?php
			$html = ob_get_clean();
		}
		return $html;
	}
}
add_filter( 'raratheme_companion_cta_widget_filter', 'jobscout_cta_widget_content_filter', 10, 3 );

if( ! function_exists( 'jobscout_limit_top_jobs' ) ){
	/**
	 * Filter to limit number of jobs in top job section
	 */    
	function jobscout_limit_top_jobs( $args ){
		if( is_front_page() && isset( $args['post_type'] ) && $args['post_type'] === 'job_listing' ){
			$args['posts_per_page'] = 6;
		}
		return $args;
	}
}
add_filter( 'job_manager_get_listings_args', 'jobscout_limit_top_jobs' );