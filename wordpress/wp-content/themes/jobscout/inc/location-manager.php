<?php
/**
 * Location Manager for JobScout Theme
 * 
 * This file provides an admin interface to manage job locations
 * that appear in the search form dropdown.
 * Locations are pulled from database but can be enabled/disabled and edited.
 *
 * @package JobScout
 */

/**
 * Add admin menu for Location Manager
 */
function jobscout_location_manager_menu() {
    add_options_page(
        __('Quản lý Địa điểm', 'jobscout'),
        __('Địa điểm Việc làm', 'jobscout'),
        'manage_options',
        'jobscout-location-manager',
        'jobscout_location_manager_page'
    );
}
add_action('admin_menu', 'jobscout_location_manager_menu');

/**
 * Register settings
 */
function jobscout_location_manager_register_settings() {
    register_setting('jobscout_location_settings', 'jobscout_location_settings', array(
        'sanitize_callback' => 'jobscout_sanitize_location_settings',
        'default' => array()
    ));
    
    register_setting('jobscout_location_settings', 'jobscout_default_location', array(
        'sanitize_callback' => 'sanitize_text_field',
        'default' => 'Tokyo'
    ));
}
add_action('admin_init', 'jobscout_location_manager_register_settings');

/**
 * Sanitize location settings
 */
function jobscout_sanitize_location_settings($input) {
    if (!is_array($input)) {
        return array();
    }
    
    $sanitized = array();
    foreach ($input as $location => $settings) {
        $clean_location = sanitize_text_field($location);
        $settings_array = array(
            'enabled' => isset($settings['enabled']) ? 1 : 0,
            'display_name' => isset($settings['display_name']) ? sanitize_text_field($settings['display_name']) : $clean_location
        );
        $sanitized[$clean_location] = $settings_array;
    }
    
    return $sanitized;
}

/**
 * Get all locations from database
 */
function jobscout_get_all_locations_from_db() {
    global $wpdb;
    
    $locations = $wpdb->get_col("
        SELECT DISTINCT meta_value 
        FROM {$wpdb->postmeta}
        WHERE meta_key = '_job_location'
        AND meta_value != ''
        ORDER BY meta_value ASC
    ");
    
    return $locations ? $locations : array();
}

/**
 * Admin page callback
 */
function jobscout_location_manager_page() {
    // Handle form submission
    if (isset($_POST['jobscout_save_locations']) && check_admin_referer('jobscout_location_nonce', 'jobscout_location_nonce')) {
        // Save default location
        if (isset($_POST['jobscout_default_location'])) {
            update_option('jobscout_default_location', sanitize_text_field($_POST['jobscout_default_location']));
        }
        
        // Get existing settings to merge
        $location_settings = get_option('jobscout_location_settings', array());
        
        // Process existing locations from form
        if (isset($_POST['location_settings']) && is_array($_POST['location_settings'])) {
            foreach ($_POST['location_settings'] as $location => $settings) {
                $clean_location = sanitize_text_field($location);
                // Merge with existing settings if any
                $existing = isset($location_settings[$clean_location]) ? $location_settings[$clean_location] : array();
                
                $location_settings[$clean_location] = array(
                    'enabled' => isset($settings['enabled']) ? 1 : 0,
                    'display_name' => isset($settings['display_name']) && !empty($settings['display_name']) 
                        ? sanitize_text_field($settings['display_name']) 
                        : (isset($existing['display_name']) ? $existing['display_name'] : $clean_location),
                    'manual' => isset($existing['manual']) ? $existing['manual'] : false
                );
            }
        }
        
        // Handle locations that were in form but checkbox was unchecked
        // They should be in POST but with enabled = 0
        // Actually, unchecked checkboxes don't send data, so we need to handle this differently
        // We'll preserve existing settings for locations not in POST
        
        // Handle new manual location
        if (isset($_POST['new_location']) && !empty(trim($_POST['new_location']))) {
            $new_location = sanitize_text_field(trim($_POST['new_location']));
            $new_display_name = isset($_POST['new_display_name']) && !empty(trim($_POST['new_display_name'])) 
                ? sanitize_text_field(trim($_POST['new_display_name'])) 
                : $new_location;
            
            if (!isset($location_settings[$new_location])) {
                $location_settings[$new_location] = array(
                    'enabled' => 1,
                    'display_name' => $new_display_name,
                    'manual' => true // Flag for manually added locations
                );
            }
        }
        
        update_option('jobscout_location_settings', $location_settings);
        
        echo '<div class="notice notice-success is-dismissible"><p>' . __('Đã lưu thành công!', 'jobscout') . '</p></div>';
    }
    
    // Handle toggle enable/disable
    if (isset($_GET['toggle']) && check_admin_referer('jobscout_toggle_location_' . $_GET['toggle'])) {
        $location = urldecode(sanitize_text_field($_GET['toggle']));
        $settings = get_option('jobscout_location_settings', array());
        
        if (isset($settings[$location])) {
            $settings[$location]['enabled'] = $settings[$location]['enabled'] ? 0 : 1;
        } else {
            $settings[$location] = array(
                'enabled' => 1,
                'display_name' => $location
            );
        }
        
        update_option('jobscout_location_settings', $settings);
        
        wp_redirect(admin_url('options-general.php?page=jobscout-location-manager&toggled=1'));
        exit;
    }
    
    // Handle delete location
    if (isset($_GET['delete']) && check_admin_referer('jobscout_delete_location_' . $_GET['delete'])) {
        $settings = get_option('jobscout_location_settings', array());
        $location = urldecode(sanitize_text_field($_GET['delete']));
        
        if (isset($settings[$location])) {
            unset($settings[$location]);
            update_option('jobscout_location_settings', $settings);
            
            wp_redirect(admin_url('options-general.php?page=jobscout-location-manager&deleted=1'));
            exit;
        }
    }
    
    // Handle sync with database
    if (isset($_GET['sync']) && check_admin_referer('jobscout_sync_locations')) {
        $db_locations = jobscout_get_all_locations_from_db();
        $settings = get_option('jobscout_location_settings', array());
        
        // Add new locations from database to settings (enabled by default)
        foreach ($db_locations as $location) {
            if (!isset($settings[$location])) {
                $settings[$location] = array(
                    'enabled' => 1,
                    'display_name' => $location
                );
            }
        }
        
        update_option('jobscout_location_settings', $settings);
        
        wp_redirect(admin_url('options-general.php?page=jobscout-location-manager&synced=1'));
        exit;
    }
    
    // Get all locations from database
    $db_locations = jobscout_get_all_locations_from_db();
    
    // Get current settings
    $location_settings = get_option('jobscout_location_settings', array());
    $default_location = get_option('jobscout_default_location', 'Tokyo');
    
    // Auto-sync on first visit: if settings is empty and we have DB locations, auto-enable them
    if (empty($location_settings) && !empty($db_locations)) {
        foreach ($db_locations as $location) {
            $location_settings[$location] = array(
                'enabled' => 1,
                'display_name' => $location
            );
        }
        update_option('jobscout_location_settings', $location_settings);
    }
    
    // Merge: database locations + manual locations
    $all_locations = array_unique(array_merge($db_locations, array_keys($location_settings)));
    sort($all_locations);
    
    // Ensure all DB locations are in settings array for display (but don't save unless user syncs)
    foreach ($db_locations as $location) {
        if (!isset($location_settings[$location])) {
            // Just add to array for display, but don't save yet (user needs to enable them)
            $location_settings[$location] = array(
                'enabled' => 0, // Disabled by default until user enables
                'display_name' => $location
            );
        }
    }
    
    // Get counts for each location
    global $wpdb;
    $location_counts = array();
    foreach ($all_locations as $location) {
        $count = $wpdb->get_var($wpdb->prepare("
            SELECT COUNT(DISTINCT post_id) 
            FROM {$wpdb->postmeta}
            WHERE meta_key = '_job_location'
            AND meta_value = %s
        ", $location));
        $location_counts[$location] = $count ? intval($count) : 0;
    }
    
    ?>
    <div class="wrap">
        <h1><?php echo esc_html__('Quản lý Địa điểm Việc làm', 'jobscout'); ?></h1>
        <p class="description">
            <?php echo esc_html__('Quản lý danh sách địa điểm hiển thị trong form tìm kiếm. Địa điểm được lấy từ database nhưng bạn có thể bật/tắt, chỉnh sửa tên hiển thị hoặc thêm địa điểm mới.', 'jobscout'); ?>
        </p>
        
        <p>
            <a href="<?php echo esc_url(admin_url('options-general.php?page=jobscout-location-manager&sync=1&_wpnonce=' . wp_create_nonce('jobscout_sync_locations'))); ?>" class="button">
                <?php echo esc_html__('Đồng bộ với Database', 'jobscout'); ?>
            </a>
        </p>
        
        <?php if (isset($_GET['deleted'])): ?>
            <div class="notice notice-success is-dismissible">
                <p><?php echo esc_html__('Đã xóa địa điểm thành công!', 'jobscout'); ?></p>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_GET['synced'])): ?>
            <div class="notice notice-success is-dismissible">
                <p><?php echo esc_html__('Đã đồng bộ với database thành công!', 'jobscout'); ?></p>
            </div>
        <?php endif; ?>
        
        <form method="post" action="">
            <?php wp_nonce_field('jobscout_location_nonce', 'jobscout_location_nonce'); ?>
            
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="jobscout_default_location"><?php echo esc_html__('Địa điểm mặc định', 'jobscout'); ?></label>
                    </th>
                    <td>
                        <input type="text" 
                               id="jobscout_default_location" 
                               name="jobscout_default_location" 
                               value="<?php echo esc_attr($default_location); ?>" 
                               class="regular-text" 
                               placeholder="Tokyo" />
                        <p class="description"><?php echo esc_html__('Địa điểm mặc định hiển thị đầu tiên trong dropdown', 'jobscout'); ?></p>
                    </td>
                </tr>
            </table>
            
            <h2><?php echo esc_html__('Danh sách Địa điểm', 'jobscout'); ?></h2>
            
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th style="width: 5%;">
                            <input type="checkbox" id="toggle-all" />
                        </th>
                        <th style="width: 30%;"><?php echo esc_html__('Tên trong Database', 'jobscout'); ?></th>
                        <th style="width: 30%;"><?php echo esc_html__('Tên hiển thị', 'jobscout'); ?></th>
                        <th style="width: 15%;"><?php echo esc_html__('Số lượng Job', 'jobscout'); ?></th>
                        <th style="width: 20%;"><?php echo esc_html__('Thao tác', 'jobscout'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($all_locations)): ?>
                        <?php foreach ($all_locations as $location): 
                            $is_manual = isset($location_settings[$location]['manual']) && $location_settings[$location]['manual'];
                            // If not in settings yet, default to disabled
                            $is_enabled = isset($location_settings[$location]) && $location_settings[$location]['enabled'] == 1;
                            $display_name = isset($location_settings[$location]['display_name']) && !empty($location_settings[$location]['display_name']) 
                                ? $location_settings[$location]['display_name'] 
                                : $location;
                            $job_count = isset($location_counts[$location]) ? $location_counts[$location] : 0;
                        ?>
                            <tr>
                                <td>
                                    <input type="hidden" 
                                           name="location_settings[<?php echo esc_attr($location); ?>][enabled]" 
                                           value="0" />
                                    <input type="checkbox" 
                                           name="location_settings[<?php echo esc_attr($location); ?>][enabled]" 
                                           value="1" 
                                           <?php checked($is_enabled, true); ?> />
                                </td>
                                <td>
                                    <strong><?php echo esc_html($location); ?></strong>
                                    <?php if ($is_manual): ?>
                                        <span class="description">(Thêm thủ công)</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <input type="text" 
                                           name="location_settings[<?php echo esc_attr($location); ?>][display_name]" 
                                           value="<?php echo esc_attr($display_name); ?>" 
                                           class="regular-text" />
                                </td>
                                <td>
                                    <?php echo esc_html($job_count); ?>
                                </td>
                                <td>
                                    <?php if ($is_manual): ?>
                                        <a href="<?php echo esc_url(wp_nonce_url(
                                            admin_url('options-general.php?page=jobscout-location-manager&delete=' . urlencode($location)),
                                            'jobscout_delete_location_' . urlencode($location)
                                        )); ?>" 
                                           class="button button-small" 
                                           onclick="return confirm('<?php echo esc_js__('Bạn có chắc chắn muốn xóa địa điểm này?', 'jobscout'); ?>');">
                                            <?php echo esc_html__('Xóa', 'jobscout'); ?>
                                        </a>
                                    <?php else: ?>
                                        <span class="description"><?php echo esc_html__('Từ database', 'jobscout'); ?></span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5">
                                <p><?php echo esc_html__('Chưa có địa điểm nào trong database. Vui lòng thêm địa điểm mới bên dưới.', 'jobscout'); ?></p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="5">
                            <h3><?php echo esc_html__('Thêm Địa điểm Mới', 'jobscout'); ?></h3>
                            <p class="description"><?php echo esc_html__('Thêm địa điểm mới ngay cả khi chưa có job nào.', 'jobscout'); ?></p>
                            <table style="width: 100%;">
                                <tr>
                                    <td style="width: 45%;">
                                        <label><?php echo esc_html__('Tên trong Database:', 'jobscout'); ?></label><br>
                                        <input type="text" 
                                               name="new_location" 
                                               id="new_location" 
                                               class="regular-text" 
                                               placeholder="<?php echo esc_attr__('Ví dụ: Ho Chi Minh City, Vietnam', 'jobscout'); ?>" />
                                    </td>
                                    <td style="width: 45%;">
                                        <label><?php echo esc_html__('Tên hiển thị (tùy chọn):', 'jobscout'); ?></label><br>
                                        <input type="text" 
                                               name="new_display_name" 
                                               id="new_display_name" 
                                               class="regular-text" 
                                               placeholder="<?php echo esc_attr__('Ví dụ: Hồ Chí Minh', 'jobscout'); ?>" />
                                        <p class="description"><?php echo esc_html__('Nếu để trống, sẽ dùng tên trong database', 'jobscout'); ?></p>
                                    </td>
                                    <td style="width: 10%; vertical-align: bottom;">
                                        <button type="button" class="button button-secondary" onclick="addNewLocation()">
                                            <?php echo esc_html__('+ Thêm', 'jobscout'); ?>
                                        </button>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </tfoot>
            </table>
            
            <p class="submit">
                <input type="submit" 
                       name="jobscout_save_locations" 
                       class="button button-primary" 
                       value="<?php echo esc_attr__('Lưu thay đổi', 'jobscout'); ?>" />
            </p>
        </form>
        
        <style>
            .wp-list-table input[type="text"] {
                width: 100%;
            }
            .wp-list-table tfoot h3 {
                margin-top: 20px;
                margin-bottom: 10px;
            }
        </style>
        
        <script>
        document.getElementById('toggle-all').addEventListener('change', function() {
            var checkboxes = document.querySelectorAll('tbody input[type="checkbox"]');
            checkboxes.forEach(function(checkbox) {
                checkbox.checked = this.checked;
            }, this);
        });
        
        function addNewLocation() {
            var locationInput = document.getElementById('new_location');
            var location = locationInput.value.trim();
            
            if (!location) {
                alert('<?php echo esc_js__('Vui lòng nhập tên địa điểm!', 'jobscout'); ?>');
                locationInput.focus();
                return;
            }
            
            // Submit form
            locationInput.form.submit();
        }
        </script>
    </div>
    <?php
}

/**
 * Get enabled locations for frontend use
 */
function jobscout_get_managed_locations() {
    $location_settings = get_option('jobscout_location_settings', array());
    $enabled_locations = array();
    
    foreach ($location_settings as $location => $settings) {
        if (isset($settings['enabled']) && $settings['enabled'] == 1) {
            $display_name = isset($settings['display_name']) && !empty($settings['display_name']) 
                ? $settings['display_name'] 
                : $location;
            $enabled_locations[$location] = $display_name;
        }
    }
    
    // If no settings exist yet, return all locations from database (backward compatibility)
    if (empty($enabled_locations)) {
        $db_locations = jobscout_get_all_locations_from_db();
        foreach ($db_locations as $location) {
            $enabled_locations[$location] = $location;
        }
    }
    
    return $enabled_locations;
}

/**
 * Get default location for frontend use
 */
function jobscout_get_default_location() {
    return get_option('jobscout_default_location', 'Tokyo');
}
