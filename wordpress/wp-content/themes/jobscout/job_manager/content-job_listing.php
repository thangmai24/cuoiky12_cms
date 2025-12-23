<?php

/**
 * Job listing in the loop.
 *
 * This template can be overridden by copying it to yourtheme/job_manager/content-job_listing.php.
 *
 * @see         https://wpjobmanager.com/document/template-overrides/
 * @author      Automattic
 * @package     WP Job Manager
 * @category    Template
 * @since       1.0.0
 * @version     1.27.0
 */

if (! defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

global $post;
$job_salary   = get_post_meta(get_the_ID(), '_job_salary', true);
$job_featured = get_post_meta(get_the_ID(), '_featured', true);
$company_name = get_post_meta(get_the_ID(), '_company_name', true);
$job_location = get_post_meta(get_the_ID(), '_job_location', true);
$job_excerpt  = get_the_excerpt();

?>
<article <?php job_listing_class(); ?> data-longitude="<?php echo esc_attr($post->geolocation_lat); ?>" data-latitude="<?php echo esc_attr($post->geolocation_long); ?>">
	<div class="job-main">
		<figure class="company-logo">
			<?php the_company_logo('thumbnail'); ?>
		</figure>

		<div class="job-title-wrap">

			<h2 class="entry-title">
				<a href="<?php the_job_permalink(); ?>"><?php wpjm_the_job_title(); ?></a>
			</h2>

			<div class="job-created-date">
				<?php echo esc_html__('Created: ', 'jobscout') . get_the_date('M d, Y'); ?>
			</div>

			<div class="job-tags-wrapper">
				<?php
				if (get_option('job_manager_enable_types')) {
					$types = wpjm_get_the_job_types();
					if (! empty($types)) : foreach ($types as $jobtype) : ?>
							<span class="job-type-tag <?php echo esc_attr(sanitize_title($jobtype->slug)); ?>"><?php echo esc_html($jobtype->name); ?></span>
				<?php endforeach;
					endif;
				}

				// Hiển thị job category nếu có
				$job_categories = wp_get_post_terms(get_the_ID(), 'job_listing_category');
				if (! empty($job_categories) && ! is_wp_error($job_categories)) {
					foreach ($job_categories as $category) {
						echo '<span class="job-type-tag">' . esc_html($category->name) . '</span>';
					}
				}

				// Hiển thị location/recruit area
				if ($job_location) {
					echo '<span class="job-type-tag">' . esc_html($job_location) . '</span>';
				}

				// Hiển thị tên công ty nếu có
				if ($company_name) {
					echo '<div class="job-type-tag">' . esc_html(get_the_company_name()) . '</div>';
				}
				?>
			</div>

		

			<?php ?>



			<div class="entry-meta">
				<?php
				do_action('job_listing_meta_start');
				do_action('job_listing_meta_end');
				?>
			</div>
		</div>

		<?php if ($job_featured) { ?>
			<div class="featured-label"><?php esc_html_e('Featured', 'jobscout'); ?></div>
		<?php } ?>

	</div>
	<?php if ($job_excerpt) { ?>
		<div class="job-short-description">
			<?php
			// Chuyển đổi excerpt thành danh sách bullet points nếu có dấu xuống dòng hoặc dấu chấm
			$description_lines = preg_split('/[\r\n]+|\.\s+/', $job_excerpt, -1, PREG_SPLIT_NO_EMPTY);
			if (count($description_lines) > 1) {
				echo '<ul>';
				foreach (array_slice($description_lines, 0, 3) as $line) {
					$line = trim($line);
					if (! empty($line)) {
						echo '<li>' . esc_html($line) . '</li>';
					}
				}
				echo '</ul>';
			} else {
				echo '<p>' . esc_html(wp_trim_words($job_excerpt, 20)) . '</p>';
			}
			?>
		</div>
	<?php } ?>

</article>