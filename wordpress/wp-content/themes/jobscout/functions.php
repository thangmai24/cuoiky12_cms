<?php
/**
 * JobScout functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package JobScout
 */

$jobscout_theme_data = wp_get_theme();
if( ! defined( 'JOBSCOUT_THEME_VERSION' ) ) define ( 'JOBSCOUT_THEME_VERSION', $jobscout_theme_data->get( 'Version' ) );
if( ! defined( 'JOBSCOUT_THEME_NAME' ) ) define( 'JOBSCOUT_THEME_NAME', $jobscout_theme_data->get( 'Name' ) );

/**
 * Implement Local Font Method functions.
 */
require get_template_directory() . '/inc/class-webfont-loader.php';

/**
 * Custom Functions.
 */
require get_template_directory() . '/inc/custom-functions.php';

/**
 * Standalone Functions.
 */
require get_template_directory() . '/inc/extras.php';

/**
 * Template Functions.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Custom functions for selective refresh.
 */
require get_template_directory() . '/inc/partials.php';

if( jobscout_is_rara_theme_companion_activated() ) :
	/**
	 * Modify filter hooks of RTC plugin.
	 */
	require get_template_directory() . '/inc/rtc-filters.php';
endif;

/**
 * Custom Controls
 */
require get_template_directory() . '/inc/custom-controls/custom-control.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer/customizer.php';

/**
 * Widgets
 */
require get_template_directory() . '/inc/widgets.php';

/**
 * Metabox
 */
require get_template_directory() . '/inc/metabox.php';

/**
 * Getting Started
*/
require get_template_directory() . '/inc/dashboard/dashboard.php';

/**
 * Plugin Recommendation
*/
require get_template_directory() . '/inc/tgmpa/recommended-plugins.php';

/**
 * Add theme compatibility function for woocommerce if active
*/
if( jobscout_is_woocommerce_activated() ){
    require get_template_directory() . '/inc/woocommerce-functions.php';    
}

/**
 * Modify filter hooks of WP Job Manager plugin.
 */
if( jobscout_is_wp_job_manager_activated() ) :
	require get_template_directory() . '/inc/wp-job-manager-filters.php';
endif;

/**
 * Customize single post layout.
 */
function jobscout_child_customize_single_layout() {
	remove_action( 'jobscout_after_post_content', 'jobscout_navigation', 10 );
}
add_action( 'after_setup_theme', 'jobscout_child_customize_single_layout' );

	/**
	 * One-time assign Contact Page template to page ID 968 if not set.
	 * Runs only in admin and only once (stores flag in options table).
	 */
	function jobscout_assign_contact_template_once() {
		if ( ! is_admin() ) {
			return;
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// Avoid running multiple times
		if ( get_option( 'jobscout_contact_template_assigned' ) ) {
			return;
		}

		// Assign Contact template to page 968
		$page_id = 968;
		$template = 'page-templates/template-contact.php';

		$current = get_post_meta( $page_id, '_wp_page_template', true );
		if ( $current !== $template ) {
			update_post_meta( $page_id, '_wp_page_template', $template );
		}

		// Set flag so we don't run again
		update_option( 'jobscout_contact_template_assigned', 1 );
    
		// Also assign Jobs All template to page ID 10 (one-time)
		$jobs_page_id = 10;
		$jobs_template = 'page-templates/template-jobsall.php';
		$current_jobs = get_post_meta( $jobs_page_id, '_wp_page_template', true );
		if ( $current_jobs !== $jobs_template ) {
			update_post_meta( $jobs_page_id, '_wp_page_template', $jobs_template );
		}
	}
	add_action( 'admin_init', 'jobscout_assign_contact_template_once' );

/**
 * Enqueue job detail script and register AJAX handler
 */
function jobscout_enqueue_job_detail_script(){
	if ( is_singular( 'job_listing' ) ){
		wp_enqueue_script( 'jobscout-job-detail', get_template_directory_uri() . '/js/job-detail.js', array( 'jquery' ), JOBSCOUT_THEME_VERSION, true );
		wp_localize_script( 'jobscout-job-detail', 'jobscout_job_detail', array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'nonce'    => wp_create_nonce( 'jobscout_apply_nonce' ),
		) );
	}
}
add_action( 'wp_enqueue_scripts', 'jobscout_enqueue_job_detail_script' );

function jobscout_handle_job_application(){
	check_ajax_referer( 'jobscout_apply_nonce', 'nonce' );

	$name    = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
	$email   = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	$message = isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '';
	$job_id  = isset( $_POST['job_id'] ) ? absint( $_POST['job_id'] ) : 0;

	if ( empty( $name ) || empty( $email ) || ! is_email( $email ) || empty( $job_id ) ) {
		wp_send_json_error( array( 'message' => __( 'Please provide a valid name, email and job reference.', 'jobscout' ) ) );
	}

	$job_title = get_the_title( $job_id );
	$job_link  = get_permalink( $job_id );

	$possible_keys = array( '_application', '_application_email', '_company_email', '_company_email_address' );
	$to = '';
	foreach ( $possible_keys as $k ){
		$val = get_post_meta( $job_id, $k, true );
		if ( is_email( $val ) ){
			$to = $val;
			break;
		}
	}

	if ( empty( $to ) ){
		$to = get_option( 'admin_email' );
	}

	$subject = sprintf( __( 'Job Application: %s', 'jobscout' ), $job_title );
	$body    = "Name: " . $name . "\n";
	$body   .= "Email: " . $email . "\n\n";
	$body   .= "Message:\n" . $message . "\n\n";
	$body   .= "Job: " . $job_title . "\n" . $job_link . "\n";

	$headers = array( 'From: ' . $name . ' <' . $email . '>' );

	$sent = wp_mail( $to, $subject, $body, $headers );

	if ( $sent ){
		wp_send_json_success( array( 'message' => __( 'Application submitted successfully.', 'jobscout' ) ) );
	}else{
		wp_send_json_error( array( 'message' => __( 'Failed to send application. Please try again later.', 'jobscout' ) ) );
	}
}
add_action( 'wp_ajax_jobscout_apply_job', 'jobscout_handle_job_application' );
add_action( 'wp_ajax_nopriv_jobscout_apply_job', 'jobscout_handle_job_application' );