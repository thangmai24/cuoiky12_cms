<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package JobScout
 */

get_header(); ?>

	<div id="primary" class="content-area">
		
        <?php 
        /**
         * Before Posts hook
        */
        do_action( 'jobscout_before_posts_content' );
        ?>
        
        <?php $is_blog_listing = is_home(); ?>

        <?php if ( $is_blog_listing ) : ?>
            <section class="blog-hero-banner" style="background-image: url('<?php echo esc_url( get_theme_file_uri( 'images/banner-image.jpg' ) ); ?>');">
                <div class="blog-hero-inner">
                    <p class="eyebrow"><?php esc_html_e( 'Latest Updates', 'jobscout' ); ?></p>
                    <h1 class="hero-title"><?php esc_html_e( 'PDS News', 'jobscout' ); ?></h1>
                </div>
            </section>
        <?php endif; ?>

        <main id="main" class="site-main">

        <?php if ( $is_blog_listing ) : ?>
            <div class="blog-grid-header">
                <p class="section-label"><?php esc_html_e( 'Newest Blog Entries', 'jobscout' ); ?></p>
            </div>
            <div class="blog-grid">
        <?php endif; ?>

		<?php
		if ( have_posts() ) :

			/* Start the Loop */
			while ( have_posts() ) : the_post();

				/*
				 * Include the Post-Format-specific template for the content.
				 * If you want to override this in a child theme, then include a file
				 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
				 */
				get_template_part( 'template-parts/content', get_post_format() );

			endwhile;

		else :

			get_template_part( 'template-parts/content', 'none' );

		endif; ?>

        <?php if ( $is_blog_listing ) : ?>
            </div><!-- .blog-grid -->
        <?php endif; ?>

		</main><!-- #main -->
        
        <?php
        /**
         * After Posts hook
         * @hooked jobscout_navigation - 15
        */
        do_action( 'jobscout_after_posts_content' );
        ?>
        
	</div><!-- #primary -->

<?php
if ( ! $is_blog_listing ) {
    get_sidebar();
}
get_footer();
