<?php
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

class WCCV_Elementor
{

	public function __construct()
	{
		// Inject controls into all elements
		add_action('elementor/element/after_section_end', [$this, 'add_controls'], 10, 3);

		// Inject data attributes for JS evaluation.
		// These attributes are safely cached by Elementor, and evaluated dynamically in the browser.
		add_action( 'elementor/frontend/widget/before_render', [ $this, 'add_render_attributes' ] );
		add_action( 'elementor/frontend/section/before_render', [ $this, 'add_render_attributes' ] );
		add_action( 'elementor/frontend/container/before_render', [ $this, 'add_render_attributes' ] );
		add_action( 'elementor/frontend/column/before_render', [ $this, 'add_render_attributes' ] );
	}

	/**
	 * Inject controls into the Advanced tab of elements.
	 */
	public function add_controls($element, $section_id, $args)
	{
		// Only inject once per element. We hook after the standard custom CSS or similar section to place it at the bottom.
		// _section_responsive is a standard section in the Advanced tab of all elements.
		if ('_section_responsive' !== $section_id) {
			return;
		}

		$element->start_controls_section(
			'section_wccv_display',
			[
				'label' => __('Conditional Display (WooCommerce)', 'wc-category-visibility'),
				'tab' => \Elementor\Controls_Manager::TAB_ADVANCED,
			]
		);

		$element->add_control(
			'wccv_categories',
			[
				'label' => __('Categories', 'wc-category-visibility'),
				'type' => \Elementor\Controls_Manager::SELECT2,
				'multiple' => true,
				'options' => $this->get_product_categories(),
				'description' => __('Leave empty to always show this element.', 'wc-category-visibility'),
				'label_block' => true,
			]
		);

		$element->add_control(
			'wccv_condition',
			[
				'label' => __('Condition', 'wc-category-visibility'),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'in' => __('Show only when IN selected categories', 'wc-category-visibility'),
					'not_in' => __('Hide when IN selected categories', 'wc-category-visibility'),
				],
				'default' => 'in',
				'condition' => [
					'wccv_categories!' => [], // Only show if categories are selected
				],
			]
		);

		$element->add_control(
			'wccv_notice',
			[
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'raw' => __('ℹ Works on: shop, category archives, product pages.', 'wc-category-visibility'),
				'content_classes' => 'elementor-descriptor',
			]
		);

		$element->end_controls_section();
	}

	/**
	 * Add render attributes to elements for JS evaluation.
	 */
	public function add_render_attributes( $element ) {
		$settings = $element->get_settings_for_display();
		if ( empty( $settings['wccv_categories'] ) ) {
			return;
		}

		$element->add_render_attribute( '_wrapper', [
			'data-wccv-categories' => implode( ',', $settings['wccv_categories'] ),
			'data-wccv-condition'  => isset( $settings['wccv_condition'] ) ? $settings['wccv_condition'] : 'in',
			'class'                => 'wccv-evaluating'
		] );
	}

	/**
	 * Helper to get product categories formatted for Select2 with indentation.
	 */
	private function get_product_categories()
	{
		$options = [];
		$categories = get_terms([
			'taxonomy' => 'product_cat',
			'hide_empty' => false,
		]);

		if (!empty($categories) && !is_wp_error($categories)) {
			$options = $this->build_category_tree($categories);
		}

		return $options;
	}

	private function build_category_tree($categories, $parent_id = 0, $depth = 0)
	{
		$options = [];
		foreach ($categories as $category) {
			if ($category->parent == $parent_id) {
				$prefix = str_repeat('— ', $depth);
				$options[$category->term_id] = $prefix . $category->name;

				// Recursive call for children
				$children = $this->build_category_tree($categories, $category->term_id, $depth + 1);
				if (!empty($children)) {
					// Use foreach to preserve numeric keys
					foreach ($children as $k => $v) {
						$options[$k] = $v;
					}
				}
			}
		}
		return $options;
	}
}
