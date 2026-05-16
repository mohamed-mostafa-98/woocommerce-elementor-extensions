<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class DPC_Core {

	/**
	 * Get the top-level product category of the current product.
	 *
	 * @param int $product_id The product ID.
	 * @return WP_Term|false The top-level term object or false if not found.
	 */
	public static function get_top_level_category( $product_id ) {
		$terms = get_the_terms( $product_id, 'product_cat' );

		if ( ! $terms || is_wp_error( $terms ) ) {
			return false;
		}

		// Pick the first term to find the ancestor.
		// If a product belongs to multiple top-level categories, we just take the first detected branch.
		$first_term = $terms[0];

		if ( 0 === $first_term->parent ) {
			return $first_term;
		}

		$ancestors = get_ancestors( $first_term->term_id, 'product_cat' );
		
		if ( empty( $ancestors ) ) {
			return $first_term;
		}

		$top_level_id = end( $ancestors );
		return get_term( $top_level_id, 'product_cat' );
	}

	/**
	 * Get subcategories of a given top-level category.
	 *
	 * @param int $parent_id The parent category ID.
	 * @return array Array of WP_Term objects.
	 */
	public static function get_subcategories( $parent_id, $query_args = [] ) {
		$args = wp_parse_args( $query_args, [
			'taxonomy'   => 'product_cat',
			'parent'     => $parent_id,
			'hide_empty' => true,
			'orderby'    => 'name',
			'order'      => 'ASC',
		] );

		$terms = get_terms( $args );

		if ( is_wp_error( $terms ) ) {
			return [];
		}

		return $terms;
	}

	/**
	 * Get all product categories as options for select controls.
	 *
	 * @return array Array of term_id => name.
	 */
	public static function get_all_category_options() {
		$options = [];
		if ( taxonomy_exists( 'product_cat' ) ) {
			$categories = get_terms( [
				'taxonomy'   => 'product_cat',
				'hide_empty' => false,
			] );
			if ( ! is_wp_error( $categories ) && ! empty( $categories ) ) {
				foreach ( $categories as $cat ) {
					$options[ $cat->term_id ] = $cat->name;
				}
			}
		}
		return $options;
	}

	/**
	 * Render the category grid HTML.
	 *
	 * @param array $categories Array of WP_Term objects.
	 * @param array $settings Widget or Shortcode settings.
	 * @return string HTML output.
	 */
	public static function render_grid( $categories, $settings = [], $custom_links = [] ) {
		if ( empty( $categories ) && empty( $custom_links ) ) {
			return '';
		}

		$show_count = isset( $settings['show_count'] ) ? filter_var( $settings['show_count'], FILTER_VALIDATE_BOOLEAN ) : true;

		ob_start();
		?>
		<?php
		$columns = isset( $settings['columns'] ) ? $settings['columns'] : '3';
		?>
		<div class="dpc-grid-container dpc-columns-<?php echo esc_attr( $columns ); ?>">
			<?php foreach ( $categories as $category ) : ?>
				<?php $term_link = get_term_link( $category ); ?>
				<div class="dpc-grid-item">
					<div class="wlsingle-categorie">
						<div class="wlcategorie-content">
							<h4>
								<a href="<?php echo esc_url( $term_link ); ?>" aria-label="<?php echo esc_attr( $category->name ); ?>" rel="nofollow"><?php echo esc_html( $category->name ); ?></a>
								<?php if ( $show_count && $category->count > 0 ) : ?>
									<sup>(<?php echo esc_html( $category->count ); ?>)</sup>
								<?php endif; ?>
							</h4>
							<p></p>
						</div>
					</div>
				</div>
			<?php endforeach; ?>

			<?php foreach ( $custom_links as $custom ) : ?>
				<?php if ( empty( $custom['title'] ) && empty( $custom['url'] ) ) continue; ?>
				<div class="dpc-grid-item">
					<div class="wlsingle-categorie">
						<div class="wlcategorie-content">
							<h4>
								<a href="<?php echo esc_url( $custom['url'] ); ?>" aria-label="<?php echo esc_attr( $custom['title'] ); ?>" rel="nofollow"><?php echo esc_html( $custom['title'] ); ?></a>
								<?php if ( $show_count && ! empty( $custom['count'] ) ) : ?>
									<sup>(<?php echo esc_html( $custom['count'] ); ?>)</sup>
								<?php endif; ?>
							</h4>
							<p></p>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
		<?php
		return ob_get_clean();
	}
}
