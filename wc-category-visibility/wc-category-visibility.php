<?php
/**
 * Plugin Name: WC Category Visibility
 * Description: Conditional display panel for Elementor elements based on WooCommerce product categories.
 * Version: 1.0.0
 * Author: Mohamed Mostafa
 * Text Domain: wc-category-visibility
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * Elementor tested up to: 3.20.0
 * Elementor requires at least: 3.18.0
 */

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

define('WCCV_VERSION', '1.0.0');
define('WCCV_DIR', plugin_dir_path(__FILE__));
define('WCCV_URL', plugin_dir_url(__FILE__));

/**
 * Main WC_Category_Visibility Class
 */
final class WC_Category_Visibility
{

	private static $instance = null;

	public static function instance()
	{
		if (is_null(self::$instance)) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct()
	{
		add_action('plugins_loaded', [$this, 'init']);
	}

	public function init()
	{
		// Check dependencies
		if (!did_action('elementor/loaded')) {
			add_action('admin_notices', [$this, 'admin_notice_missing_elementor']);
			return;
		}

		if (!class_exists('WooCommerce')) {
			add_action('admin_notices', [$this, 'admin_notice_missing_woocommerce']);
			return;
		}

		// Check Elementor version
		if (!version_compare(ELEMENTOR_VERSION, '3.18.0', '>=')) {
			add_action('admin_notices', [$this, 'admin_notice_elementor_version']);
			return;
		}

		// Load pure logic checker
		require_once WCCV_DIR . 'includes/class-wccv-checker.php';

		// Load Elementor integration on init
		add_action('elementor/init', [$this, 'init_elementor']);

		// Enqueue CSS
		add_action('wp_enqueue_scripts', [$this, 'enqueue_styles']);

		// Output JS evaluator to bypass Elementor caching
		add_action('wp_head', ['WCCV_Checker', 'output_js_evaluator'], 5);
	}

	public function init_elementor()
	{
		require_once WCCV_DIR . 'includes/class-wccv-elementor.php';
		new WCCV_Elementor();
	}

	public function enqueue_styles()
	{
		wp_enqueue_style('wccv-hidden', WCCV_URL . 'assets/css/wccv-hidden.css', [], WCCV_VERSION);
	}

	public function admin_notice_missing_elementor()
	{
		printf(
			'<div class="notice notice-warning is-dismissible"><p>%s</p></div>',
			esc_html__('WC Category Visibility requires Elementor to be installed and active.', 'wc-category-visibility')
		);
	}

	public function admin_notice_missing_woocommerce()
	{
		printf(
			'<div class="notice notice-warning is-dismissible"><p>%s</p></div>',
			esc_html__('WC Category Visibility requires WooCommerce to be installed and active.', 'wc-category-visibility')
		);
	}

	public function admin_notice_elementor_version()
	{
		printf(
			'<div class="notice notice-warning is-dismissible"><p>%s</p></div>',
			esc_html__('WC Category Visibility requires Elementor version 3.18.0 or greater.', 'wc-category-visibility')
		);
	}
}

WC_Category_Visibility::instance();
