<?php
/*
 * Template for page ID 10 (Jobs)
 */
get_header();
?>

<div class="page-template-jobsall">

    <!-- Hero banner -->
    <section class="jobs-hero" style="background-image: url('<?php echo esc_url(get_template_directory_uri()); ?>/images/banner_jobsall.jpg');">
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
                        <option value="date" <?php selected(isset($_GET['order_by']) ? $_GET['order_by'] : 'date', 'date'); ?>>Latest Jobs</option>
                        <option value="title" <?php selected(isset($_GET['order_by']) ? $_GET['order_by'] : 'date', 'title'); ?>>Title</option>
                    </select>
                </form>
            </div>
        </div>

        <?php
        $paged = get_query_var('paged') ? get_query_var('paged') : 1;
        $order_by = (isset($_GET['order_by']) && $_GET['order_by'] === 'title') ? 'title' : 'date';

        $args = array(
            'post_type' => 'job_listing',
            'posts_per_page' => 10,
            'paged' => $paged,
            'orderby' => $order_by,
            'order' => 'DESC',
        );

        $jobs = new WP_Query($args);
        if ($jobs->have_posts()) : ?>

            <div class="jobs-grid">
                <?php while ($jobs->have_posts()) : $jobs->the_post(); ?>
                    <article id="post-<?php the_ID(); ?>" <?php post_class('job-card'); ?>>
                        <div>
  <div class="job-card-inner">
                            <div class="job-card-left">
                                <?php if (has_post_thumbnail()) : ?>
                                    <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('thumbnail'); ?></a>
                                <?php else : ?>
                                    <div class="job-logo-placeholder"></div>
                                <?php endif; ?>
                            </div>
                            <div class="job-card-right">
                                <h3 class="job-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                                <div class="job-meta-date">
                                    <span class="job-date">Created: <?php echo get_the_date('M d, Y'); ?></span>
                                </div>
                                
                                <div class="job-info-details">
                                    <?php
                                    $employment_type = get_post_meta(get_the_ID(), '_employment_type', true);
                                    $job_category = '';
                                    $cat_terms = get_the_terms(get_the_ID(), 'job_listing_category');
                                    if ($cat_terms && ! is_wp_error($cat_terms)) {
                                        $categories = wp_list_pluck($cat_terms, 'name');
                                        $job_category = implode(', ', $categories);
                                    }
                                    $job_location = get_post_meta(get_the_ID(), '_job_location', true);
                                    ?>
                                    <div class="info-item"><?php echo esc_html($employment_type ? $employment_type : 'Fulltime'); ?></div>
                                    <div class="job-meta-type">
                                    <?php
                                    $terms = get_the_terms(get_the_ID(), 'job_listing_type');
                                    if ($terms && ! is_wp_error($terms)) {
                                        $types = wp_list_pluck($terms, 'name');
                                        echo '<span class="job-type">' . esc_html(implode(', ', $types)) . '</span>';
                                    }
                                    ?>
                                </div>
                                   
                                    <div class="info-item"><?php echo esc_html($job_location); ?></div>
                                </div>

                            </div>
                        </div>
                        <ul class="job-excerpt-list">
                            <?php
                            $excerpt = get_the_excerpt();
                            // Split by sentence or period
                            $sentences = array_filter(array_map('trim', preg_split('/[.!?]+/', $excerpt)));

                            // Show first 3 bullets
                            $count = 0;
                            foreach ($sentences as $sentence) {
                                if ($count >= 3) break;
                                if (! empty($sentence)) {
                                    echo '<li>' . esc_html($sentence) . '</li>';
                                    $count++;
                                }
                            }

                            // If less than 3 sentences, pad with original excerpt
                            if ($count < 3) {
                                $excerpt_words = wp_trim_words($excerpt, 30, '');
                                if ($count === 0) {
                                    echo '<li>' . esc_html($excerpt_words) . '</li>';
                                }
                            }
                            ?>
                        </ul>
                        </div>
                      
                    </article>
                <?php endwhile;
                wp_reset_postdata(); ?>
            </div>

            <div class="jobs-load-more">
                <?php
                if ($jobs->max_num_pages > $paged) {
                    $next_link = get_pagenum_link($paged + 1);
                    echo '<a class="btn load-more-btn" href="' . esc_url($next_link) . '">LOAD MORE JOBS</a>';
                }
                ?>
            </div>

        <?php else : ?>
            <p><?php esc_html_e('No jobs found.', 'jobscout'); ?></p>
        <?php endif; ?>

    </div>

</div>

<?php
get_footer();
