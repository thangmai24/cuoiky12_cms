<?php
/**
 * Template for page ID 954 (About Us)
 */
get_header();
?>

<div class="page-template-about">
    <div class="about-container">
        <!-- Title Section -->
        <div class="about-title-section">
            <h1 class="about-title">ABOUT US</h1>
        </div>

        <!-- Top Section: Image and Philosophy -->
        <div class="about-top-section">
            <div class="about-top-left">
                <div class="about-image-wrapper">
                    <?php 
                    $torii_image = get_template_directory_uri() . '/images/about-torii.jpg';
                    if (file_exists(get_template_directory() . '/images/about-torii.jpg')) : ?>
                        <img src="<?php echo esc_url($torii_image); ?>" alt="Japanese Torii Gate" class="about-image" />
                    <?php else : ?>
                        <div class="about-image-placeholder" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 400px; display: flex; align-items: center; justify-content: center; color: white; font-size: 18px;">Image: Japanese Torii Gate</div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="about-top-right">
                <div class="about-philosophy">
                    <div class="philosophy-item">
                        <h3 class="philosophy-label">Our Vision</h3>
                        <p class="philosophy-text">Create hotels and restaurants around the world that offer memorable experiences while building a lasting, positive relationship together with our guests, partners, team members and communities.</p>
                    </div>
                    <div class="philosophy-item">
                        <h3 class="philosophy-label">Our Mission</h3>
                        <p class="philosophy-text">Share "Omotenashi" with the world</p>
                    </div>
                    <div class="philosophy-item">
                        <h3 class="philosophy-label">Our Core Value</h3>
                        <p class="philosophy-text">"If I were the guest" To provide guests with the hospitality you would want to receive as a guest.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Middle Section: Operations -->
        <div class="about-middle-section">
            <h2 class="about-operations-title">Hotels, restaurants, banquets/weddings management</h2>
            <div class="about-operations-content">
                <p>Plan Do See developed and operates 37 properties worldwide including 3 award-winning resorts in Japan; 17 restaurants of diverse cuisines in cities including New York, Miami and Los Angeles; and other countries including Japan, Indonesia, Malaysia and Bali.</p>
                <p>Each venue carries its own concept and design. Many of them are originally historical landmarks that were loved by the local people.</p>
            </div>
        </div>

        <!-- Bottom Section: Company Info and Image -->
        <div class="about-bottom-section">
            <div class="about-bottom-left">
                <div class="about-company-info">
                    <div class="company-info-item">
                        <span class="company-info-label">Established since:</span>
                        <span class="company-info-value">April 1993</span>
                    </div>
                    <div class="company-info-item">
                        <span class="company-info-label">Head Office:</span>
                        <span class="company-info-value">Marunouchi 2-1-1, Chiyoda, Tokyo</span>
                    </div>
                    <div class="company-info-item">
                        <span class="company-info-label">Capital:</span>
                        <span class="company-info-value">200,000,000 JPY</span>
                    </div>
                    <div class="company-info-item">
                        <span class="company-info-label">CEO:</span>
                        <span class="company-info-value">Yutaka Noda</span>
                    </div>
                    <div class="company-info-item">
                        <span class="company-info-label">Number of Employees:</span>
                        <span class="company-info-value">Full time: 830 / Total: 1,500</span>
                    </div>
                </div>
            </div>
            <div class="about-bottom-right">
                <div class="about-image-wrapper">
                    <?php 
                    $tokyo_image = get_template_directory_uri() . '/images/about-tokyo-tower.jpg';
                    if (file_exists(get_template_directory() . '/images/about-tokyo-tower.jpg')) : ?>
                        <img src="<?php echo esc_url($tokyo_image); ?>" alt="Tokyo Tower" class="about-image" />
                    <?php else : ?>
                        <div class="about-image-placeholder" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); min-height: 400px; display: flex; align-items: center; justify-content: center; color: white; font-size: 18px;">Image: Tokyo Tower</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.page-template-about {
    background-color: #f5f5f5;
    padding: 0;
    margin: 0;
    width: 100%;
    min-height: 100vh;
}

.about-container {
    max-width: 100%;
    margin: 0;
    padding: 40px 20px;
}

/* Title Section */
.about-title-section {
    text-align: center;
    margin-bottom: 50px;
}

.about-title {
    font-size: 48px;
    font-weight: 700;
    color: #333;
    margin: 0;
    letter-spacing: 2px;
}

/* Top Section */
.about-top-section {
    display: flex;
    gap: 40px;
    margin-bottom: 60px;
    align-items: flex-start;
}

.about-top-left,
.about-top-right {
    flex: 1;
}

.about-image-wrapper {
    width: 100%;
    height: 100%;
    overflow: hidden;
    border-radius: 8px;
}

.about-image {
    width: 100%;
    height: auto;
    display: block;
    object-fit: cover;
}

.about-philosophy {
    display: flex;
    flex-direction: column;
    gap: 30px;
}

.philosophy-item {
    margin-bottom: 20px;
}

.philosophy-label {
    font-size: 20px;
    font-weight: 600;
    color: #333;
    margin: 0 0 10px 0;
}

.philosophy-text {
    font-size: 16px;
    line-height: 1.6;
    color: #555;
    margin: 0;
}

/* Middle Section */
.about-middle-section {
    margin: 60px -20px;
    padding: 60px 20px;
    background-color: #ffffff;
    text-align: center;
    width: calc(100% + 40px);
}

.about-operations-title {
    font-size: 28px;
    font-weight: 600;
    color: #ff6600;
    margin: 0 0 30px 0;
}

.about-operations-content {
    max-width: 900px;
    margin: 0 auto;
}

.about-operations-content p {
    font-size: 16px;
    line-height: 1.8;
    color: #555;
    margin: 0 0 20px 0;
}

.about-operations-content p:last-child {
    margin-bottom: 0;
}

/* Bottom Section */
.about-bottom-section {
    display: flex;
    gap: 40px;
    align-items: flex-start;
}

.about-bottom-left,
.about-bottom-right {
    flex: 1;
}

.about-bottom-left {
    text-align: center;
}

.about-company-info {
    display: flex;
    flex-direction: column;
    gap: 20px;
    align-items: center;
}

.company-info-item {
    display: flex;
    flex-direction: column;
    gap: 5px;
    text-align: center;
}

.company-info-label {
    font-size: 16px;
    font-weight: 600;
    color: #333;
}

.company-info-value {
    font-size: 16px;
    color: #555;
    line-height: 1.6;
}

/* Responsive Design */
@media (max-width: 768px) {
    .about-title {
        font-size: 36px;
    }

    .about-top-section,
    .about-bottom-section {
        flex-direction: column;
    }

    .about-top-left,
    .about-top-right,
    .about-bottom-left,
    .about-bottom-right {
        width: 100%;
    }

    .about-operations-title {
        font-size: 24px;
    }

    .about-middle-section {
        margin: 40px -20px;
        padding: 40px 20px;
        width: calc(100% + 40px);
    }
}
</style>

<?php
get_footer();
