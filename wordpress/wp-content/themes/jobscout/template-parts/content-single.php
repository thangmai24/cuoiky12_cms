<?php
/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package JobScout
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'news-single-wrapper' ); ?>>
    <div class="news-single__content entry-content" itemprop="text">
        <?php
        the_content();
        wp_link_pages(
            array(
                'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'jobscout' ),
                'after'  => '</div>',
            )
        );
        ?>
    </div>
</article><!-- #post-<?php the_ID(); ?> -->

