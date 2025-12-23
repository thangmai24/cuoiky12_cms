<?php
/**
 * Template part for displaying single job posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package JobScout
 */
$job_id = get_the_ID();
$company_name = get_post_meta( $job_id, '_company_name', true );
$location = get_post_meta( $job_id, '_job_location', true );
?>
<!-- Breadcrumb Navigation -->
<div class="job-breadcrumb">
	<div class="container">
		<a href="<?php echo esc_url( home_url() ); ?>">Home</a>
		<span class="separator">/</span>
		<a href="<?php echo esc_url( home_url( '?post_type=job_listing' ) ); ?>">All Jobs</a>
		<span class="separator">/</span>
		<span class="current"><?php the_title(); ?></span>
	</div>
</div>
<article id="post-<?php the_ID(); ?>" <?php post_class('job-detail-wrapper'); ?>>
	
	<!-- Job Detail Header with Logo, Title, and Meta -->
	<div class="job-detail-header">
		<div class="job-header-content">
			<!-- Company Logo -->
			<div class="company-logo-section">
				<?php if ( has_post_thumbnail() ) : ?>
					<div class="company-logo">
						<?php the_post_thumbnail( 'medium' ); ?>
					</div>
				<?php else : ?>
					<div class="company-logo placeholder-logo">
						<span>Logo</span>
					</div>
				<?php endif; ?>
			</div>
			
			<!-- Job Title and Info -->
			<div class="job-title-meta">
				<h1 class="job-title"><?php the_title(); ?></h1>
				
				<div class="job-meta-secondary">
					<span><?php echo esc_html( 'Created: ' . get_the_date( 'M d, Y' ) ); ?></span>
				</div>
                                <div class="job-meta-row">
					<?php if ( $company_name ) : ?>
						<span class="meta-item">
							<strong>Fulltime</strong>
						</span>
					<?php endif; ?>
					
					<?php if ( get_option( 'job_manager_enable_types' ) ) : 
						$types = wpjm_get_the_job_types();
						if ( ! empty( $types ) ) : foreach ( $types as $type ) : ?>
							<span class="meta-item">
								<strong><?php echo esc_html( $type->name ); ?></strong>
							</span>
						<?php endforeach; endif;
					endif; ?>
					
					<?php if ( $location ) : ?>
						<span class="meta-item">
							<strong><?php echo esc_html( $location ); ?></strong>
						</span>
					<?php endif; ?>
				</div>
			</div>
		</div>
		
		<!-- Share and Apply Buttons -->
		<div class="job-actions">
			<button type="button" class="btn-share" data-url="<?php echo esc_url( get_permalink() ); ?>">SHARE</button>
			<button type="button" class="btn-apply" data-target="#apply">APPLY JOB</button>
		</div>
	</div>
	
	<!-- Main Content + Sidebar Layout -->
	<div class="job-detail-container">
		
		<!-- Main Content -->
		<div class="job-main-content">
			<div class="entry-content" itemprop="text">
				<?php the_content(); ?>
			</div>
		</div>
		
		<!-- Sidebar -->
		<aside class="job-detail-sidebar">
			
			<!-- Company Rating -->
			<div class="sidebar-widget rating-widget">
				<h3 class="widget-title">Staff Rating</h3>
				<div class="rating-display">
					<div class="stars">
						<span class="star active">★</span>
						<span class="star active">★</span>
						<span class="star active">★</span>
						<span class="star active">★</span>
						<span class="star inactive">★</span>
					</div>
					<span class="rating-value">4.0</span>
				</div>
			</div>
			
			<!-- Company Photos -->
			<div class="sidebar-widget company-photos-widget">
				<h3 class="widget-title">Company Photos</h3>
				<div class="company-photos-grid">
					<?php
					// Get gallery images from post content or use featured image
					$attachment_ids = array();
					
					// Try to get images from post content
					if ( preg_match_all( '/<img[^>]+src=[\'"]([^\'"]+)[\'"][^>]*>/', $post->post_content, $matches ) ) {
						$attachment_ids = $matches[1];
					}
					
					// If no images found, use featured image
					if ( empty( $attachment_ids ) && has_post_thumbnail() ) {
						$attachment_ids[] = get_post_thumbnail_id();
					}
					
					// Display first few images
					if ( ! empty( $attachment_ids ) ) {
						$count = 0;
						foreach ( $attachment_ids as $img_url ) {
							if ( $count < 3 ) {
								echo '<div class="photo-item">';
								if ( is_numeric( $img_url ) ) {
									echo wp_get_attachment_image( $img_url, 'medium' );
								} else {
									echo '<img src="' . esc_url( $img_url ) . '" alt="Company photo" />';
								}
								if ( $count === 2 && count( $attachment_ids ) > 3 ) {
									echo '<div class="photo-overlay">+' . ( count( $attachment_ids ) - 3 ) . '</div>';
								}
								echo '</div>';
								$count++;
							}
						}
					}
					?>
				</div>
			</div>
			
		</aside>
		
	</div>
	
</article> <!-- #article -->

<!-- Apply Section -->
<section id="apply" class="job-apply-section">
	<div class="container">
		<div class="job-apply-wrapper">
			<h2 class="section-title">Apply For This Job</h2>
			<div class="job-apply-body">
				<form id="job-apply-form" class="job-apply-form" method="post">
					<?php wp_nonce_field( 'jobscout_apply_nonce', 'jobscout_apply_nonce_field' ); ?>
					<input type="hidden" name="job_id" value="<?php echo esc_attr( get_the_ID() ); ?>">
					<p>
						<label for="applicant_name"><?php esc_html_e( 'Name', 'jobscout' ); ?></label>
						<input type="text" id="applicant_name" name="name" required>
					</p>
					<p>
						<label for="applicant_email"><?php esc_html_e( 'Email', 'jobscout' ); ?></label>
						<input type="email" id="applicant_email" name="email" required>
					</p>
					<p>
						<label for="applicant_message"><?php esc_html_e( 'Message', 'jobscout' ); ?></label>
						<textarea id="applicant_message" name="message" rows="6" required></textarea>
					</p>
					<p>
						<button type="submit" class="btn-apply-submit"><?php esc_html_e( 'Submit Application', 'jobscout' ); ?></button>
						<span class="apply-status" aria-live="polite"></span>
					</p>
				</form>
			</div>
		</div>
	</div>
</section>

<!-- Other Jobs Section -->
<section class="related-jobs-section">
	<h2 class="section-title">OTHER JOBS</h2>
	<div class="related-jobs-grid">
		<?php
		// Get other jobs - show all other jobs
		$args = array(
			'post_type' => 'job_listing',
			'posts_per_page' => 6,
			'post__not_in' => array( get_the_ID() ),
			'post_status' => 'publish',
			'orderby' => 'date',
			'order' => 'DESC',
		);
		
		$other_jobs = new WP_Query( $args );
		
		if ( $other_jobs->have_posts() ) :
			while ( $other_jobs->have_posts() ) : $other_jobs->the_post();
				?>
				<div class="related-job-card">
					<div class="job-card-header">
						<?php if ( has_post_thumbnail() ) : ?>
							<div class="job-card-logo">
								<?php the_post_thumbnail( 'thumbnail' ); ?>
							</div>
						<?php else : ?>
							<div class="job-card-logo placeholder-logo">
								<span>Logo</span>
							</div>
						<?php endif; ?>
						
						<div class="job-card-title-section">
							<h3 class="job-card-title">
								<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
							</h3>
							<p class="job-card-meta">
								<?php echo esc_html( 'Created: ' . get_the_date( 'M d, Y' ) ); ?>
							</p>
                                                        <div class="job-card-meta-info">
						<?php 
						$employment_type = 'Fulltime'; // Default employment type
						$job_types = wp_get_post_terms( get_the_ID(), 'job_listing_type', array( 'fields' => 'names' ) );
						$job_type = ! empty( $job_types ) ? $job_types[0] : 'Position';
						$location = get_post_meta( get_the_ID(), '_job_location', true );
						?>
						<span class="meta-item"><strong><?php echo esc_html( $employment_type ); ?></strong></span>
						<span class="meta-item"><strong><?php echo esc_html( $job_type ); ?></strong></span>
						<?php if ( $location ) : ?>
							<span class="meta-item"><strong><?php echo esc_html( $location ); ?></strong></span>
						<?php endif; ?>
					</div>
						</div>
					</div>
					
					
					
					<div class="job-card-description">
						<?php the_excerpt(); ?>
					</div>
				</div>
				<?php
			endwhile;
			wp_reset_postdata();
		else :
			echo '<p style="text-align: center; grid-column: 1 / -1; padding: 40px; color: #999;">No other jobs available at this time.</p>';
		endif;
		?>
	</div>
</section>