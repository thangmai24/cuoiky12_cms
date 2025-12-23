<?php
/*
Template Name: Contact Page
Template Post Type: page
*/

get_header();
?>

<div class="page-template-contact">

    <!-- Hero banner with dark overlay and centered title -->
    <section class="contact-hero" style="background-image: url('<?php echo esc_url( get_template_directory_uri() ); ?>/images/banner_contact.jpg');">
        <div class="contact-hero-overlay"></div>
        <div class="container">
            <h1 class="contact-hero-title">CONTACT US</h1>
        </div>
    </section>

    <!-- White strip with headquarters address, centered -->
    <section class="contact-address-strip">
        <div class="container">
            <div class="contact-address-inner">
                <h3 class="contact-address-title">Our Headquarters Address</h3>
                <p class="contact-address-text">60 Nguyen Van Thu, Ward Da Kao, District 1, Ho Chi Minh City, Viet Nam</p>
            </div>
        </div>
    </section>

    <!-- Grey section with contact blocks (For Employers / For Jobseekers) -->
    <section class="contact-info-section">
        <div class="container">
            <div class="contact-info-grid">
                <div class="contact-info-block">
                    <h4>For Employers</h4>
                    <p class="muted">Call our Sales Hotline</p>
                    <p class="contact-phone"><strong>Ho Chi Minh</strong><br>+84 977 460 519</p>
                    <p class="contact-phone"><strong>Ha Noi</strong><br>+84 983 131 351</p>
                    <p class="contact-note">Request a call from one of our Customer Love Account Managers. We're ready to help you grow!</p>
                </div>

              

                <div class="contact-info-block">
                    <h4>For Jobseekers</h4>
                    <p class="muted">Ask a question on our <a href="#">Facebook</a> page</p>
                    <p class="muted">Read our <a href="#">blog posts</a> on interview and CV tips</p>
                    <p class="contact-phone"><strong>Call us at</strong><br>+84 28 6681 1397</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Optional place for form/content if present in Page editor (keeps page editable) -->
    <section class="contact-page-content container">
        <?php
        while ( have_posts() ) : the_post();
            // show page content if any (e.g., Contact Form 7 shortcode)
            if ( trim( get_the_content() ) ) {
                echo '<div class="contact-form-wrap">';
                the_content();
                echo '</div>';
            }
        endwhile;
        ?>
    </section>

</div>

<?php get_footer();
