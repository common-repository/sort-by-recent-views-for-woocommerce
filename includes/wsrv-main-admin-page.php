<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add admin menu option under 'WooCommerce'
 */
function wsrv_add_admin_menu()
{
    add_submenu_page(
        'woocommerce',
        'Sort By Recent Views',
        'Sort By Recent Views',
        'manage_options',
        'sort-by-recent-views-for-woocommerce',
        'wsrv_main_page'
    );
}
add_action('admin_menu', 'wsrv_add_admin_menu');


/**
 * Display the main page & process any submitted form data
 */
function wsrv_main_page()
{

    // admin security check
    if (!current_user_can('manage_options')) {
        wp_die("<h2>To view this page you must first log in.</h2>");
    }

    // save posted settings to database
    if (isset($_POST['submit_wsrv_settings_form'])) {

        if (isset($_POST['enable-sorting'])) {
            $enable_sorting = 'yes';
        } else {
            $enable_sorting = 'no';
        }

        if (isset($_POST['enable-automatic-maintenance'])) {
            $enable_automatic_maintenance = 'yes';
        } else {
            $enable_automatic_maintenance = 'no';
        }

        $custom_sorting_option_name = sanitize_text_field($_POST['custom-sorting-option-name']);
        $timeframe = sanitize_text_field($_POST['timeframe']);

        update_option('wsrv_enable_sorting', $enable_sorting);
        update_option('wsrv_custom_sorting_option_name', $custom_sorting_option_name);
        update_option('wsrv_timeframe', $timeframe);
        update_option('wsrv_enable_automatic_maintenance', $enable_automatic_maintenance);
    }

    // get settings from database

    $enable_sorting = get_option('wsrv_enable_sorting');
    if (!$enable_sorting) {
        $enable_sorting = 'yes';
    }
    $custom_sorting_option_name = get_option('wsrv_custom_sorting_option_name');
    if (!$custom_sorting_option_name) {
        $custom_sorting_option_name = 'Sort by recent views';
    }
    $timeframe = get_option('wsrv_timeframe');
    if (!$timeframe) {
        $timeframe = 7;
    }
    $enable_automatic_maintenance = get_option('wsrv_enable_automatic_maintenance');
    if (!$enable_automatic_maintenance) {
        $enable_automatic_maintenance = 'yes';
    }

?>

    <h1>
        <?php esc_html_e('Sort By Recent Views For WooCommerce', 'sort-by-recent-views-for-woocommerce'); ?>
    </h1>

    <div class="wrap">

        <div id="icon-options-general" class="icon32"></div>
        <h1><?php esc_attr_e('Sort By Recent Views For WooCommerce', 'WpAdminStyle'); ?></h1>

        <div id="poststuff">

            <div id="post-body">

                <!-- main content -->
                <div id="post-body-content">

                    <div class="meta-box-sortables ui-sortable">

                        <div class="postbox">

                            <div class="inside">

                                <h3><span>Information:</span></h3>
                                <div class="notice notice-info inline">
                                    <p> Please note that the plugin will only start tracking product views <strong>from the moment it is activated</strong>. In addition, the view counts are recalculated once every hour, so the "Sort by recent views" sorting option will only start showing real results <strong>1 hour after activating the plugin</strong>.</p>
                                </div>
                            </div>
                            <!-- .postbox -->

                        </div>

                        <div class="meta-box-sortables ui-sortable">

                            <div class="postbox">


                                <div class="inside">



                                    <form method="post" id='wsrv_settings_form' action="<?php echo admin_url('admin.php') . '?page=sort-by-recent-views-for-woocommerce'; ?>">

                                        <h3>Enable/Disable sorting option:</h3>
                                        <p>

                                            <input type="checkbox" id="enable-sorting" name="enable-sorting" <?php echo $enable_sorting == 'yes' ? 'checked' : ''; ?>>
                                            <label for="enable-sorting">Enable the "Sort by recent views" sorting option on my store.</label><br />
                                        </p>
                                        <h3>Custom sorting option text:</h3>
                                        <p>
                                            <label for="custom-sorting-option-name">Custom sorting option title shown in the store (default "Sort by recent views"): </label>
                                            <input name="custom-sorting-option-name" id="custom-sorting-option-names" value="<?php echo esc_html($custom_sorting_option_name); ?>" type="text" class="medium-text"></input>
                                        </p>
                                        <h3>Recent views tracking timeframe:</h3>
                                        <p><label for="timeframe">Sort products by total views in the last: </label>
                                            <select name="timeframe" id="timeframe">
                                                <option <?php echo $timeframe == 7 ? 'selected="selected"' : ''; ?> value="7">7 days</option>
                                                <option <?php echo $timeframe == 14 ? 'selected="selected"' : ''; ?> value="14">14 days</option>
                                                <option <?php echo $timeframe == 30 ? 'selected="selected"' : ''; ?> value="30">30 days</option>
                                                <option <?php echo $timeframe == 90 ? 'selected="selected"' : ''; ?> value="90">90 days</option>

                                            </select>
                                        </p>
                                        <h3>Enable/Disable automatic maintenance:</h3>
                                        <p>

                                            <input type="checkbox" id="enable-automatic-maintenance" name="enable-automatic-maintenance" <?php echo $enable_automatic_maintenance == 'yes' ? 'checked' : ''; ?>>
                                            <label for="enable-automatic-maintenance">Automatically delete the older views (> 90 days old) to keep database clean & fast (<strong>RECOMMENDED</strong>).</label><br />
                                        </p>

                                        <input class="button-primary" id="submit-form" type="submit" name="submit_wsrv_settings_form" value="Save" />
                                        </p>

                                    </form>


                                </div>
                                <!-- .postbox -->

                            </div>
                            <!-- .meta-box-sortables .ui-sortable -->

                        </div>

                        <!-- post-body-content -->


                    </div>
                    <!-- #post-body .metabox-holder .columns-2 -->

                    <br class="clear">
                </div>
                <!-- #poststuff -->

            </div> <!-- .wrap -->

        <?php

    }
