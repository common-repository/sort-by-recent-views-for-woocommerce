<?php
/*
  Plugin Name: Sort By Recent Views For WooCommerce
  Plugin URI: https://wordpress.org/plugins/sort-by-recent-views-for-woocommerce
  Description: Adds the "sort by recent views" sorting option to your shop.
  Author: J2FB
  Author URI: https://www.j2fb.com
  Version: 1.0
  WC tested up to: 4.8
  License: GPLv3
  License URI: https://www.gnu.org/licenses/gpl-3.0.html
  Text Domain: sort-by-recent-views-for-woocommerce
 */

if (!defined('ABSPATH')) {
    return;
}

/**
 * Check if WooCommerce is active
 */
if (!in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins'))) && !array_key_exists('woocommerce/woocommerce.php', apply_filters('active_plugins', get_site_option('active_sitewide_plugins', array())))) { // deactive if woocommerce in not active
    require_once(ABSPATH . 'wp-admin/includes/plugin.php');
    deactivate_plugins(plugin_basename(__FILE__));
}

/**
 * all code for the main admin page
 */
include_once('includes/wsrv-main-admin-page.php');

/**
 * all code for the plugin activation
 */
include_once('includes/wsrv-activation.php');

/**
 * all code for the generation of view counts
 */
include_once('includes/wsrv-logic.php');

/**
 * add settings link to plugin page
 */
add_filter("plugin_action_links_" . plugin_basename(__FILE__), 'wsrv_plugin_settings_link');

/**
 * register activation hooks
 */
register_activation_hook(__FILE__, 'wsrv_create_views_table');
register_activation_hook(__FILE__, 'wsrv_setup_cron');
register_activation_hook(__FILE__, 'wsrv_setup_plugin_options');

/**
 * setup ajax actions
 */
add_action('wp_ajax_update_product_views', 'wsrv_update_product_views');
add_action('wp_ajax_nopriv_update_product_views', 'wsrv_update_product_views');

/**
 * enqueue styles & scripts
 */
add_action('wp_enqueue_scripts', 'wsrv_enqueue_scripts');

function wsrv_enqueue_scripts()
{
    global $post;

    // only enqueue the JS file if we are visiting a single WC product page
    if (is_product()) {
        wp_register_script('wsrv-main-js',  plugin_dir_url(__FILE__) . 'assets/js/wsrv-main.js', ['jquery'], '1.0.0', false);
        wp_enqueue_script('wsrv-main-js');

        wp_localize_script('wsrv-main-js', 'ajax_object', array(
            'nonce' => wp_create_nonce('wsrv_nonce'),
            'ajax_url' => admin_url('admin-ajax.php'),
            'postID' => $post->ID,
        ));
    }
}

/**
 * Display settings link in plugins page
 */
function wsrv_plugin_settings_link($links)
{
    $settings_link = '<a href="' . admin_url('admin.php') . '?page=sort-by-recent-views-for-woocommerce">Settings</a>';
    array_unshift($links, $settings_link);
    return $links;
}
