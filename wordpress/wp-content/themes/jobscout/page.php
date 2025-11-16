<?php
/**
 * The template for displaying all pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site may use a
 * different template.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package JobScout
 */
get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main">

			<?php
			while ( have_posts() ) : the_post();

				get_template_part( 'template-parts/content', 'page' );

				/**
                 * Comment Template
                 * 
                 * @hooked jobscout_comment
                */
                do_action( 'jobscout_after_page_content' );

			endwhile; // End of the loop.
			?>

		</main><!-- #main -->
	</div><!-- #primary -->

// Do not load sidebar on Contact page (by slug 'contact' or IDs 135, 968)
// Add any page IDs here that should display full-width without the sidebar.
if ( ! is_page( array( 'contact', 135, 968 ) ) ) {
	get_sidebar();
}
get_footer();
get_footer();
