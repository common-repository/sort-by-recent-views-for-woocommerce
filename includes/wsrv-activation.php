<?php

/**
 * Create Product Views Database table
 */
function wsrv_create_views_table()
{
    global $table_prefix, $wpdb;

    $tblname = 'wsrv_views';
    $table_full_name = $table_prefix . "$tblname ";
    $charset_collate = $wpdb->get_charset_collate();

    if ($wpdb->get_var("show tables like '$table_full_name'") != $table_full_name) {

        $sql = "CREATE TABLE " . $table_full_name . " ( 
        id int(11) NOT NULL auto_increment,
        product_id int(128) NOT NULL,
        visit_datetime datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
        PRIMARY KEY (id)
        )" . $charset_collate . ";";

        require_once(ABSPATH . '/wp-admin/includes/upgrade.php');
        dbDelta($sql);
    } else {
    }
}

/**
 * Setup the WP cron to regenerate the recent view counts for all products
 */
function wsrv_setup_cron()
{
    if (!wp_next_scheduled('wsrv_update_views_count_hook')) {
        wp_schedule_event(time(), 'hourly', 'wsrv_update_views_count_hook');
    }
    if (!wp_next_scheduled('wsrv_automatic_maintenance_hook')) {
        wp_schedule_event(time(), 'daily', 'wsrv_automatic_maintenance_hook');
    }
}
add_action('wsrv_update_views_count_hook', 'wsrv_update_recent_views_count_field_all_products');
add_action('wsrv_automatic_maintenance_hook', 'wsrv_automatic_maintenance');

/**
 * Setup plugin options default values
 */
function wsrv_setup_plugin_options()
{
    update_option('wsrv_enable_sorting', 'yes');
    update_option('wsrv_custom_sorting_option_name', 'Sort by recent views');
    update_option('wsrv_timeframe', 7);
    update_option('wsrv_enable_automatic_maintenance', 'yes');
}
