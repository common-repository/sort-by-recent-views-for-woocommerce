<?php

/**
 * Records a product view in the database
 */
function wsrv_update_product_views()
{
    global $wpdb;

    check_ajax_referer('wsrv_nonce', 'ajax_nonce');

    $product_id = sanitize_text_field($_POST['product_id']);

    if (get_post_type($_POST['product_id']) == 'product') {
        $id = get_the_ID();

        $table_name = $wpdb->prefix . "wsrv_views";

        $wpdb->insert(
            $table_name,
            array(
                'product_id' => $product_id,
                'visit_datetime' => current_time('mysql'),
            )
        );
    }

    echo json_encode(1);
    die();
}

/**
 *  Add these new sorting arguments to the sortby options on the frontend
 */
function wsrv_add_new_postmeta_orderby($sortby)
{

    $enable_sorting = get_option('wsrv_enable_sorting');
    if ($enable_sorting == 'yes') {

        $custom_sorting_option_name = get_option('wsrv_custom_sorting_option_name');
        if (!$custom_sorting_option_name) {
            $custom_sorting_option_name = 'Sort by recent views';
        }

        $sortby['recent_views'] = __($custom_sorting_option_name, 'woocommerce');
    }
    return $sortby;
}
add_filter('woocommerce_catalog_orderby', 'wsrv_add_new_postmeta_orderby');

/**
 *  Add the code to sort the products by recent views
 */
function wsrv_add_postmeta_ordering_args($sort_args)
{

    $orderby_value = isset($_GET['orderby']) ? wc_clean($_GET['orderby']) : apply_filters('woocommerce_default_catalog_orderby', get_option('woocommerce_default_catalog_orderby'));
    switch ($orderby_value) {

        case 'recent_views':
            $sort_args['orderby'] = 'meta_value_num';
            // We use meta_value_num here because points are a number and we want to sort in numerical order
            $sort_args['order'] = 'desc';
            $sort_args['meta_key'] = 'recent_views';
            break;
    }

    return $sort_args;
}
add_filter('woocommerce_get_catalog_ordering_args', 'wsrv_add_postmeta_ordering_args');


/**
 *  Go through all products and update the recent view count
 */
function wsrv_update_recent_views_count_field_all_products()
{

    global $wpdb;

    //error_log('updated the view counters for all products');

    set_time_limit(60 * 60);

    $timeframe = get_option('wsrv_timeframe');
    if (!$timeframe) {
        $timeframe = 7;
    }

    $timeframe_string = date('Y-m-d H:i:s', strtotime("-" . $timeframe . " days"));

    $recent_views_db_results = $wpdb->get_results("SELECT product_id, COUNT(id) as count FROM wp_wsrv_views WHERE visit_datetime > '" . $timeframe_string . "' GROUP BY product_id");

    $recent_views_array = array();

    foreach ($recent_views_db_results as $recent_views_db) {
        $recent_views_array[$recent_views_db->product_id] = $recent_views_db->count;
    }

    $args = array(
        'post_type' => 'product',
        'posts_per_page' => -1
    );
    $loop = new WP_Query($args);

    if ($loop->have_posts()) : while ($loop->have_posts()) : $loop->the_post();

            global $product;

            if (array_key_exists($product->get_id(), $recent_views_array)) {
                update_post_meta($product->get_id(), 'recent_views', $recent_views_array[$product->get_id()]);
            } else {
                update_post_meta($product->get_id(), 'recent_views', 0);
            }

        endwhile;
    endif;
}

/**
 *  Perform automatic maintenance if that option is enabled
 */
function wsrv_automatic_maintenance()
{
    $enable_automatic_maintenance = get_option('wsrv_enable_automatic_maintenance');
    if ($enable_automatic_maintenance == 'yes') {

        global $wpdb;

        $timeframe_string = date('Y-m-d H:i:s', strtotime("-90 days"));

        $older_views_db_results = $wpdb->get_results("DELETE FROM wp_wsrv_views WHERE visit_datetime < '" . $timeframe_string . "'");

        //error_log('performed automatic maintenance');
    }
}
