<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

class DPC_Elementor_Widget extends Widget_Base {

	public function get_name() {
		return 'dynamic_product_categories';
	}

	public function get_title() {
		return __( 'Dynamic Product Categories', 'dynamic-product-categories' );
	}

	public function get_icon() {
		return 'eicon-product-categories';
	}

	public function get_categories() {
		return [ 'woocommerce-elements', 'general' ];
	}

	protected function register_controls() {
		// Content Section
		$this->start_controls_section(
			'content_section',
			[
				'label' => __( 'Content', 'dynamic-product-categories' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'columns',
			[
				'label'   => __( 'Columns', 'dynamic-product-categories' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '3',
				'options' => [
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6',
				],
				'selectors' => [
					'{{WRAPPER}} .dpc-grid-container' => 'display: grid; grid-template-columns: repeat({{VALUE}}, 1fr);',
				],
			]
		);

		$this->add_control(
			'orderby',
			[
				'label'   => __( 'Order By', 'dynamic-product-categories' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'name',
				'options' => [
					'name'       => __( 'Name', 'dynamic-product-categories' ),
					'id'         => __( 'ID', 'dynamic-product-categories' ),
					'count'      => __( 'Count', 'dynamic-product-categories' ),
					'slug'       => __( 'Slug', 'dynamic-product-categories' ),
					'term_order' => __( 'Term Order', 'dynamic-product-categories' ),
				],
			]
		);

		$this->add_control(
			'order',
			[
				'label'   => __( 'Order', 'dynamic-product-categories' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'ASC',
				'options' => [
					'ASC'  => __( 'Ascending', 'dynamic-product-categories' ),
					'DESC' => __( 'Descending', 'dynamic-product-categories' ),
				],
			]
		);

		$this->add_control(
			'show_count',
			[
				'label'        => __( 'Show Product Count', 'dynamic-product-categories' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'dynamic-product-categories' ),
				'label_off'    => __( 'Hide', 'dynamic-product-categories' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'fallback_message',
			[
				'label'       => __( 'Fallback Message', 'dynamic-product-categories' ),
				'type'        => Controls_Manager::TEXT,
				'description' => __( 'Message to show if no categories are found.', 'dynamic-product-categories' ),
				'default'     => '',
			]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'custom_title',
			[
				'label' => __( 'Title', 'dynamic-product-categories' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Custom Category' , 'dynamic-product-categories' ),
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'custom_url',
			[
				'label' => __( 'URL', 'dynamic-product-categories' ),
				'type' => Controls_Manager::URL,
				'placeholder' => __( 'https://your-link.com', 'dynamic-product-categories' ),
				'default' => [
					'url' => '',
				],
			]
		);
		
		$repeater->add_control(
			'custom_count',
			[
				'label' => __( 'Product Count (Optional)', 'dynamic-product-categories' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 0,
				'default' => '',
			]
		);

		$repeater->add_control(
			'condition_category',
			[
				'label' => __( 'Show only under Root Category Slug', 'dynamic-product-categories' ),
				'type' => Controls_Manager::TEXT,
				'description' => __( 'Enter the slug of the root category (e.g., "veterinary-products"). Leave empty to always show.', 'dynamic-product-categories' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'custom_categories',
			[
				'label' => __( 'Custom Categories', 'dynamic-product-categories' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'title_field' => '{{{ custom_title }}}',
			]
		);

		$this->end_controls_section();

		// Style Section - Grid
		$this->start_controls_section(
			'style_grid_section',
			[
				'label' => __( 'Grid & Cards', 'dynamic-product-categories' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'grid_gap',
			[
				'label'      => __( 'Gap', 'dynamic-product-categories' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem' ],
				'range'      => [
					'px' => [ 'min' => 0, 'max' => 100 ],
				],
				'selectors'  => [
					'{{WRAPPER}} .dpc-grid-container' => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'card_padding',
			[
				'label'      => __( 'Card Padding', 'dynamic-product-categories' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'selectors'  => [
					'{{WRAPPER}} .wlcategorie-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'card_bg_color',
			[
				'label'     => __( 'Background Color', 'dynamic-product-categories' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .wlsingle-categorie' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'card_bg_color_hover',
			[
				'label'     => __( 'Background Color (Hover)', 'dynamic-product-categories' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .wlsingle-categorie:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'card_border_radius',
			[
				'label'      => __( 'Border Radius', 'dynamic-product-categories' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'selectors'  => [
					'{{WRAPPER}} .wlsingle-categorie' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'card_border',
				'label' => __( 'Border', 'dynamic-product-categories' ),
				'selector' => '{{WRAPPER}} .wlsingle-categorie',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'card_box_shadow',
				'selector' => '{{WRAPPER}} .wlsingle-categorie',
			]
		);
		
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'card_box_shadow_hover',
				'label'    => __( 'Hover Box Shadow', 'dynamic-product-categories' ),
				'selector' => '{{WRAPPER}} .wlsingle-categorie:hover',
			]
		);

		$this->end_controls_section();

		// Style Section - Typography
		$this->start_controls_section(
			'style_typography_section',
			[
				'label' => __( 'Typography', 'dynamic-product-categories' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'heading_title_style',
			[
				'label'     => __( 'Title', 'dynamic-product-categories' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'title_color',
			[
				'label'     => __( 'Color', 'dynamic-product-categories' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .wlcategorie-content h4 a' => 'color: {{VALUE}};',
				],
			]
		);
		
		$this->add_control(
			'title_color_hover',
			[
				'label'     => __( 'Hover Color', 'dynamic-product-categories' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .wlsingle-categorie:hover .wlcategorie-content h4 a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .wlcategorie-content h4 a',
			]
		);

		$this->add_control(
			'heading_count_style',
			[
				'label'     => __( 'Product Count', 'dynamic-product-categories' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'count_color',
			[
				'label'     => __( 'Color', 'dynamic-product-categories' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .wlcategorie-content h4 sup' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'count_typography',
				'selector' => '{{WRAPPER}} .wlcategorie-content h4 sup',
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		// In Elementor editor or frontend
		$product_id = get_the_ID();
		
		// If editing an Elementor template (e.g. Single Product template), we might not have a real product in the editor.
		// Elementor usually sets a preview post ID
		if ( ! $product_id ) {
			echo esc_html( $settings['fallback_message'] );
			return;
		}

		$top_level_cat = DPC_Core::get_top_level_category( $product_id );

		if ( ! $top_level_cat ) {
			echo esc_html( $settings['fallback_message'] );
			return;
		}

		$query_args = [
			'orderby' => $settings['orderby'],
			'order'   => $settings['order'],
		];

		$subcategories = DPC_Core::get_subcategories( $top_level_cat->term_id, $query_args );

		$custom_links = [];
		if ( ! empty( $settings['custom_categories'] ) ) {
			foreach ( $settings['custom_categories'] as $item ) {
				$condition = trim( $item['condition_category'] );
				
				// Check condition (match slug or show if empty)
				if ( empty( $condition ) || $condition === $top_level_cat->slug ) {
					$custom_links[] = [
						'title' => $item['custom_title'],
						'url'   => $item['custom_url']['url'] ?? '',
						'count' => $item['custom_count'],
					];
				}
			}
		}

		if ( empty( $subcategories ) && empty( $custom_links ) ) {
			echo esc_html( $settings['fallback_message'] );
			return;
		}

		$grid_settings = [
			'columns'    => $settings['columns'],
			'show_count' => 'yes' === $settings['show_count'],
		];

		echo DPC_Core::render_grid( $subcategories, $grid_settings, $custom_links );
	}
}
