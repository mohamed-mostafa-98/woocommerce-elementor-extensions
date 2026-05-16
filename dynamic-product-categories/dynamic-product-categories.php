<?php
/**
 * Plugin Name: Dynamic Product Categories
 * Plugin URI:  https://example.com/
 * Description: Dynamically displays product categories based on the top-level parent category of the current product. Includes an Elementor widget and a shortcode.
 * Version:     1.0.0
 * Author:      Mohamed Mostafa
 * Author URI:  https://www.linkedin.com/in/mohamed-hella/
 * Text Domain: dynamic-product-categories
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'DPC_VERSION', '1.0.0' );
define( 'DPC_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'DPC_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * Main Plugin Class
 */
final class Dynamic_Product_Categories {

	private static $instance = null;

	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		$this->includes();
		$this->init_hooks();
	}

	private function includes() {
		require_once DPC_PLUGIN_DIR . 'includes/class-dpc-core.php';
		require_once DPC_PLUGIN_DIR . 'includes/class-dpc-shortcode.php';
		
		// Check if Elementor is installed and active
		if ( did_action( 'elementor/loaded' ) ) {
			require_once DPC_PLUGIN_DIR . 'includes/class-dpc-elementor-integration.php';
		} else {
			// Hook into plugins loaded if we are checking earlier
			add_action( 'elementor/init', function() {
				require_once DPC_PLUGIN_DIR . 'includes/class-dpc-elementor-integration.php';
			} );
		}
	}

	private function init_hooks() {
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_styles' ] );
	}

	public function enqueue_styles() {
		wp_enqueue_style( 'dpc-style', DPC_PLUGIN_URL . 'assets/css/style.css', [], DPC_VERSION );
	}
}

function dpc_plugin() {
	return Dynamic_Product_Categories::get_instance();
}

add_action( 'plugins_loaded', 'dpc_plugin' );
