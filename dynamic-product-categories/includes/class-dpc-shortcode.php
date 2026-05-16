<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class DPC_Shortcode {

	public function __construct() {
		add_shortcode( 'dynamic_product_categories', [ $this, 'render_shortcode' ] );
	}

	public function render_shortcode( $atts ) {
		// Only run on single product pages or if a specific product ID is passed
		$product_id = get_the_ID();

		if ( ! is_product() && empty( $atts['product_id'] ) ) {
			return '';
		}

		if ( ! empty( $atts['product_id'] ) ) {
			$product_id = intval( $atts['product_id'] );
		}

		$atts = shortcode_atts(
			[
				'columns'           => '3',
				'show_count'        => 'true',
				'fallback'          => '',
				'custom_links_json' => '',
			],
			$atts,
			'dynamic_product_categories'
		);

		$top_level_cat = DPC_Core::get_top_level_category( $product_id );

		if ( ! $top_level_cat ) {
			return esc_html( $atts['fallback'] );
		}

		$subcategories = DPC_Core::get_subcategories( $top_level_cat->term_id );
		
		$custom_links = [];
		if ( ! empty( $atts['custom_links_json'] ) ) {
			$decoded = json_decode( html_entity_decode( $atts['custom_links_json'] ), true );
			if ( is_array( $decoded ) ) {
				foreach ( $decoded as $item ) {
					$condition = isset( $item['condition_category'] ) ? trim( $item['condition_category'] ) : '';
					if ( empty( $condition ) || $condition === $top_level_cat->slug ) {
						$custom_links[] = [
							'title' => $item['custom_title'] ?? '',
							'url'   => $item['custom_url'] ?? '',
							'count' => $item['custom_count'] ?? '',
						];
					}
				}
			}
		}

		if ( empty( $subcategories ) && empty( $custom_links ) ) {
			return esc_html( $atts['fallback'] );
		}

		return DPC_Core::render_grid( $subcategories, $atts, $custom_links );
	}
}

new DPC_Shortcode();
