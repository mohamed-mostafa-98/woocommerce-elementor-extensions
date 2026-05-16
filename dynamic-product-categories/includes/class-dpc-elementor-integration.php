<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class DPC_Elementor_Integration {

	public function __construct() {
		add_action( 'elementor/widgets/register', [ $this, 'register_widgets' ] );
	}

	public function register_widgets( $widgets_manager ) {
		require_once DPC_PLUGIN_DIR . 'widgets/class-dpc-elementor-widget.php';
		require_once DPC_PLUGIN_DIR . 'widgets/class-dpc-advanced-elementor-widget.php';
		
		$widgets_manager->register( new \DPC_Elementor_Widget() );
		$widgets_manager->register( new \DPC_Advanced_Elementor_Widget() );
	}
}

new DPC_Elementor_Integration();
