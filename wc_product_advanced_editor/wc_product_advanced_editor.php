<?php
/**
 * Plugin Name: WC Product Advanced Editor
 * Description: Extends WooCommerce to allow advanced updating of existing products (Name, Description, Images) with a rich text editor.
 * Version: 1.0.0
 * Author: Mohamed Mostafa Elsayed
 * Text Domain: wc-product-advanced-editor
 *
 * Requirements: WooCommerce
 */

if (!defined('ABSPATH')) {
    exit;
}

define('WC_PAE_VERSION', '1.0.1');
define('WC_PAE_PATH', plugin_dir_path(__FILE__));
define('WC_PAE_URL', plugin_dir_url(__FILE__));

/**
 * Initialize the plugin.
 */
function wc_pae_init()
{
    if (!class_exists('WooCommerce')) {
        return;
    }

    require_once WC_PAE_PATH . 'includes/class-wc-pae-admin.php';
    new WC_PAE_Admin();
}
add_action('plugins_loaded', 'wc_pae_init');
