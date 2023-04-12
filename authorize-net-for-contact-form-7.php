<?php

/**
 * Plugin Name: Authorize.net For Contact Form 7
 * Plugin URI: https://github.com/kahnu044/authorize-net-for-contact-form-7
 * Description: Handles Payments For Contact Form 7 Using Authorize.net
 * Version: 1.0.0
 * Author: Kahnu044
 * Author URI: https://github.com/kahnu044/
 * GitHub Plugin URI: https://github.com/kahnu044/authorize-net-for-contact-form-7
 */

define('AFCF7_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('AFCF7_TEMPLATE_PATH', plugin_dir_path(__FILE__) . 'template/');
define('AFCF7_PLUGIN_URL', plugins_url('', __FILE__));
define('AFCF7_ASSETS_URL',  plugins_url('assets/', __FILE__));
define('AFCF7_PLUGIN_VERSION',  '1.0.0');
define('AFCF7_META_PREFIX', 'afcf7_');

/**
 * Enqueue the custom style and script to the plugin.
 */

add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('afcf7-styles', AFCF7_ASSETS_URL . 'css/style.css', array(), AFCF7_PLUGIN_VERSION, 'all');
    wp_enqueue_style('afcf7-authorize-styles', AFCF7_ASSETS_URL . 'css/payment-form.css', array(), AFCF7_PLUGIN_VERSION, 'all');
    wp_enqueue_script('afcf7-script', AFCF7_ASSETS_URL . 'js/script.js', array('jquery'), AFCF7_PLUGIN_VERSION, true);
});


add_action('admin_enqueue_scripts', function () {

    // Only for contact form 7 edit pages
    if (isset($_GET['page']) && $_GET['page'] === 'wpcf7') {
        wp_enqueue_style('afcf7-admin-styles', AFCF7_ASSETS_URL . 'css/admin.style.css', array(), AFCF7_PLUGIN_VERSION, 'all');
        wp_enqueue_script('afcf7-admin-script', AFCF7_ASSETS_URL . 'js/admin-script.js', array('jquery'), AFCF7_PLUGIN_VERSION, true);
    }
});


require_once(AFCF7_PLUGIN_PATH . 'inc/admin/afcf7-authorize-setting-panel.php');
