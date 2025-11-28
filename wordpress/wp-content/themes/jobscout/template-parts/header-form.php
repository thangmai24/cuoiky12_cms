<?php

/**
 *
 * Creating a custom job search form for homepage
 * The [jobs] shortcode is will use search_location and search_keywords variables from the query string.
 *
 * @link https://wpjobmanager.com/document/tutorial-creating-custom-job-search-form/
 *
 * @package JobScout
 */
$find_a_job_link = get_option('job_manager_jobs_page_id', 0);
$post_slug = get_post_field('post_name', $find_a_job_link);
$ed_job_category = get_option('job_manager_enable_categories');

if ($post_slug) {
  $action_page = home_url('/' . $post_slug);
} else {
  $action_page = home_url('/');
}
?>

<div class="job_listings">

  <form class="jobscout_job_filters" method="GET" action="<?php echo esc_url( $action_page ) ?>">
    <div class="search_jobs">

      <div class="search_keywords">
        <label for="search_keywords"><?php esc_html_e('Keywords', 'jobscout'); ?></label>
        <div class="input-wrapper">
          <svg class="icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
            <path fill="currentColor"
              d="M10 2a8 8 0 015.29 13.71l4.5 4.5-1.42 1.42-4.5-4.5A8 8 0 1110 2zm0 2a6 6 0 100 12 6 6 0 000-12z" />
          </svg>


          <input type="text" id="search_keywords" name="search_keywords"
            placeholder="<?php esc_attr_e('Search by job, companies, skills', 'jobscout'); ?>">
        </div>
      </div>

      <div class="search_location">
        <label for="search_location"><?php esc_html_e('Location', 'jobscout'); ?></label>
        <div class="input-wrapper">
          <svg class="icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
            <path fill="currentColor"
              d="M12 2a7 7 0 00-7 7c0 5.25 7 13 7 13s7-7.75 7-13a7 7 0 00-7-7zm0 9.5a2.5 2.5 0 112.5-2.5 2.5 2.5 0 01-2.5 2.5z" />
          </svg>
          <select id="search_location" name="search_location">
            <?php
            // Get default location and managed locations from admin
            $default_location = jobscout_get_default_location();
            $managed_locations = jobscout_get_managed_locations();
            
            // Display default location as first option
            printf('<option value="">%s</option>', esc_html($default_location));
            
            // Display managed locations from admin (enabled locations only)
            if (!empty($managed_locations)) {
              foreach ($managed_locations as $location_key => $display_name) {
                // Skip if it's the same as default location
                if ($location_key !== $default_location && $display_name !== $default_location) {
                  printf('<option value="%1$s">%2$s</option>', esc_attr($location_key), esc_html($display_name));
                }
              }
            }
            ?>
          </select>

        </div>
      </div>

      <?php if ($ed_job_category) { ?>
        <div class="search_categories custom_search_categories">
          <label for="search_category"><?php esc_html_e('Job Category', 'jobscout'); ?></label>
          <select id="search_category" class="robo-search-category" name="search_category">
            <option value=""><?php _e('Select Job Category', 'jobscout'); ?></option>
            <?php foreach (get_job_listing_categories() as $jobcat) : ?>
              <option value="<?php echo esc_attr($jobcat->term_id); ?>"><?php echo esc_html($jobcat->name); ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      <?php } ?>

      <div class="search_submit">
        <input type="submit" value="<?php esc_attr_e('SEARCH JOB', 'jobscout'); ?>" />
      </div>

    </div>
  </form>

</div>