<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package JobScout
 */

get_header();
$news_current_id = 0;

if ( have_posts() ) :
	while ( have_posts() ) :
		the_post();
        $news_current_id = get_the_ID();
		$home_url      = home_url( '/' );
		$home_label    = get_theme_mod( 'breadcrumb_home_text', __( 'Home', 'jobscout' ) );
		$news_page_id  = get_option( 'page_for_posts' );
		$news_page_url = $news_page_id ? get_permalink( $news_page_id ) : '';
		$news_page_lbl = $news_page_id ? get_the_title( $news_page_id ) : __( 'All News', 'jobscout' );
		$permalink     = get_permalink();
		$share_url     = sprintf( 'https://www.facebook.com/sharer/sharer.php?u=%s', rawurlencode( $permalink ) );
		$location_meta = get_post_meta( get_the_ID(), '_job_location', true );
		$categories    = get_the_category();
		$category_text = '';

		if ( $categories ) {
			$category_names = wp_list_pluck( $categories, 'name' );
			$category_text  = implode( ', ', $category_names );
		}
		?>
		<div class="news-single__breadcrumb">
			<a href="<?php echo esc_url( $home_url ); ?>"><?php echo esc_html( $home_label ); ?></a>
			<span class="separator">/</span>
			<?php if ( $news_page_url ) : ?>
				<a href="<?php echo esc_url( $news_page_url ); ?>"><?php echo esc_html( $news_page_lbl ); ?></a>
				<span class="separator">/</span>
			<?php endif; ?>
			<span class="current"><?php the_title(); ?></span>
		</div>

		<div class="news-single__summary-card">
			<div class="news-single__thumbnail">
				<?php
				if ( has_post_thumbnail() ) {
					the_post_thumbnail( 'jobscout-single', array( 'itemprop' => 'image' ) );
				} elseif ( function_exists( 'jobscout_fallback_svg_image' ) ) {
					jobscout_fallback_svg_image( 'jobscout-single' );
				}
				?>
			</div>
			<div class="news-single__summary-body">
				<div class="news-single__summary-top">
					<h1 class="news-single__title"><?php the_title(); ?></h1>
				</div>
                        
				<div class="news-single__meta">
					<span class="news-single__meta-label"><?php esc_html_e( 'Posted:', 'jobscout' ); ?></span>
					<span class="news-single__date"><?php echo esc_html( get_the_date() ); ?></span>
				</div>
				<?php if ( $category_text || $location_meta ) : ?>
					<div class="news-single__category-row">
						<?php if ( $category_text ) : ?>
							<span class="news-single__category-label"><?php esc_html_e( 'Category name', 'jobscout' ); ?></span>
							<span class="news-single__category-label-divider" aria-hidden="true"></span>
							<span class="news-single__category-values"><?php echo esc_html( $category_text ); ?></span>
						<?php endif; ?>
						<?php if ( $category_text && $location_meta ) : ?>
							<span class="news-single__divider">|</span>
						<?php endif; ?>
						<?php if ( $location_meta ) : ?>
							<span class="news-single__location"><?php echo esc_html( $location_meta ); ?></span>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			</div>
                  <div class="news-single__summary-share">
                        <a class="news-single__share-button" href="<?php echo esc_url( $share_url ); ?>" target="_blank" rel="noopener">
                              <?php esc_html_e( 'Share', 'jobscout' ); ?>
                        </a>
                  </div>
		</div>
	<?php
	endwhile;
	rewind_posts();
endif;
?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main">

		<?php
		while ( have_posts() ) : the_post();
            get_template_part( 'template-parts/content', 'single' );

		endwhile; // End of the loop.
		?>

		</main><!-- #main -->
        
        <?php
        /**
         * @hooked jobscout_navigation    - 10 
         * @hooked jobscout_author        - 20
         * @hooked jobscout_comment       - 30
        */
        do_action( 'jobscout_after_post_content' );
        ?>
        
	</div><!-- #primary -->

    <?php
    if ( $news_current_id ) :
        $news_related_args = array(
            'post_type'           => 'post',
            'post_status'         => 'publish',
            'posts_per_page'      => 6,
            'ignore_sticky_posts' => true,
            'post__not_in'        => array( $news_current_id ),
        );
        $news_related_query = new WP_Query( $news_related_args );

        if ( $news_related_query->have_posts() ) :
            ?>
            <section class="news-related-posts">
                <h2 class="news-related-posts__title"><?php esc_html_e( 'Newest Blog Entries', 'jobscout' ); ?></h2>
                <div class="news-related-posts__grid">
                    <?php
                    while ( $news_related_query->have_posts() ) :
                        $news_related_query->the_post();
                        ?>
                        <article class="news-related-posts__card">
                            <div class="news-related-posts__thumb">
                                <a href="<?php the_permalink(); ?>">
                                    <?php
                                    if ( has_post_thumbnail() ) {
                                        the_post_thumbnail( 'jobscout-blog', array( 'itemprop' => 'image' ) );
                                    } else {
                                        jobscout_fallback_svg_image( 'jobscout-blog' );
                                    }
                                    ?>
                                </a>
                            </div>
                            <div class="news-related-posts__body">
                                <h3 class="news-related-posts__heading">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h3>
                                <p class="news-related-posts__excerpt">
                                    <?php echo wp_kses_post( wp_trim_words( get_the_excerpt(), 20, '&hellip;' ) ); ?>
                                </p>
                                <a class="news-related-posts__readmore" href="<?php the_permalink(); ?>">
                                    <?php esc_html_e( 'Read More', 'jobscout' ); ?>
                                </a>
                            </div>
                        </article>
                        <?php
                    endwhile;
                    wp_reset_postdata();
                    ?>
                </div>
            </section>
            <?php
        endif;
    endif;
    ?>

<?php
get_footer();
