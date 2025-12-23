<?php
/*
Template Name: Jobs All
Template Post Type: page
*/

get_header();
?>

<div class="page-template-jobsall">

    <!-- Hero banner -->
    <section class="jobs-hero" style="background-image: url('<?php echo esc_url( get_template_directory_uri() ); ?>/images/banner_jobsall.jpg');">
        <div class="jobs-hero-overlay"></div>
        <div class="container">
            <h1 class="jobs-hero-title">CAREER WITH US</h1>
        </div>
    </section>

    <div class="container jobs-content">
        <div class="jobs-heading">
            <h2>ALL JOBS</h2>
            <div class="jobs-controls">
                <form method="get">
                    <select name="order_by" onchange="this.form.submit()">
                        <option value="date" <?php selected( isset($_GET['order_by']) ? $_GET['order_by'] : 'date', 'date' ); ?>>Latest Jobs</option>
                        <option value="title" <?php selected( isset($_GET['order_by']) ? $_GET['order_by'] : 'date', 'title' ); ?>>Title</option>
                    </select>
                </form>
            </div>
        </div>

        <?php
        // Determine pagination and ordering
        $paged = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;
        $order_by = ( isset( $_GET['order_by'] ) && $_GET['order_by'] === 'title' ) ? 'title' : 'date';

        $args = array(
            'post_type' => 'job_listing',
            'posts_per_page' => 10,
            'paged' => $paged,
            'orderby' => $order_by,
            'order' => 'DESC',
        );

        $jobs = new WP_Query( $args );
        if ( $jobs->have_posts() ) : ?>

            <div class="jobs-grid">
                <?php while ( $jobs->have_posts() ) : $jobs->the_post(); ?>
                    <article id="post-<?php the_ID(); ?>" <?php post_class( 'job-card' ); ?>>
                        <div class="job-card-inner">
                            <div class="job-card-left">
                                <?php if ( has_post_thumbnail() ) : ?>
                                    <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail( 'thumbnail' ); ?></a>
                                <?php else : ?>
                                    <div class="job-logo-placeholder"></div>
                                <?php endif; ?>
                            </div>
                            <div class="job-card-right">
                                <h3 class="job-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                                <div class="job-meta">
                                    <span class="job-date"><?php echo get_the_date(); ?></span>
                                    <?php
                                    $terms = get_the_terms( get_the_ID(), 'job_listing_type' );
                                    if ( $terms && ! is_wp_error( $terms ) ) {
                                        $types = wp_list_pluck( $terms, 'name' );
                                        echo ' <span class="job-type">' . esc_html( implode( ', ', $types ) ) . '</span>';
                                    }
                                    ?>
                                </div>
                               
                            </div>
                        </div>
                         <div class="job-excerpt"><?php echo wp_trim_words( get_the_excerpt(), 30 ); ?></div>
                    </article>
                <?php endwhile; wp_reset_postdata(); ?>
            </div>

            <div class="jobs-load-more">
                <?php
                // Link to next page if exists
                if ( $jobs->max_num_pages > $paged ) {
                    $next_link = get_pagenum_link( $paged + 1 );
                    echo '<a class="btn load-more-btn" href="' . esc_url( $next_link ) . '">LOAD MORE JOBS</a>';
                }
                ?>
            </div>

        <?php else : ?>
            <p><?php esc_html_e( 'No jobs found.', 'jobscout' ); ?></p>
        <?php endif; ?>

    </div><!-- .container -->

</div><!-- .page-template-jobsall -->

<?php get_footer();
