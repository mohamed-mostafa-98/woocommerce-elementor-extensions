<?php
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

class WCCV_Checker
{

	/**
	 * Output JavaScript to evaluate visibility on the client side.
	 * This completely bypasses Elementor's Template Cache!
	 */
	public static function output_js_evaluator()
	{
		// Never hide in the Elementor editor
		if (\Elementor\Plugin::$instance->editor->is_edit_mode() || \Elementor\Plugin::$instance->preview->is_preview_mode()) {
			return;
		}

		$context = self::get_current_page_category_ids();
		$current_ids = $context['ids'];
		$debug_source = $context['source'];
		?>
		<style>
			/* Temporarily hide elements while JS evaluates them to prevent flicker */
			.wccv-evaluating { display: none !important; }
			.wccv-hidden { display: none !important; }
		</style>
		<script>
			document.addEventListener("DOMContentLoaded", function() {
				var currentCats = <?php echo wp_json_encode(array_values($current_ids)); ?>;
				var wpSource = <?php echo wp_json_encode($debug_source); ?>;
				var elements = document.querySelectorAll('[data-wccv-categories]');
				
				for (var i = 0; i < elements.length; i++) {
					var el = elements[i];
					var catsString = el.getAttribute('data-wccv-categories');
					if (!catsString) {
						el.classList.remove('wccv-evaluating');
						continue;
					}
					
					var cats = catsString.split(',').map(Number);
					var cond = el.getAttribute('data-wccv-condition');
					
					var intersects = cats.some(function(c) { 
						return currentCats.indexOf(c) !== -1; 
					});
					
					var hide = false;
					if (cond === 'in') {
						hide = !intersects;
					} else { // 'not_in'
						hide = intersects;
					}
					
					if (hide) {
						el.classList.add('wccv-hidden');
					}
					
					// Evaluation complete, show the element (unless wccv-hidden was added)
					el.classList.remove('wccv-evaluating');
					
					console.log('WCCV JS Debug Element:', {
						element: el,
						required_categories: cats,
						condition: cond,
						page_categories: currentCats,
						hidden: hide,
						source: wpSource
					});
				}
			});
		</script>
		<?php
	}

	/**
	 * Get category IDs associated with the current page context (including ancestors).
	 * @return array Array of term IDs (integers).
	 */
	private static function get_current_page_category_ids()
	{
		$current_ids = [];
		$source = 'none';

		if (is_product()) {
			$product_id = get_the_ID();
			$source = 'is_product_' . $product_id;
			$terms = get_the_terms($product_id, 'product_cat');
			if ($terms && !is_wp_error($terms)) {
				foreach ($terms as $term) {
					$current_ids[] = (int) $term->term_id;
					$ancestors = get_ancestors($term->term_id, 'product_cat');
					if (!empty($ancestors)) {
						$current_ids = array_merge($current_ids, $ancestors);
					}
				}
			}
		} elseif (is_product_category()) {
			$current_cat = get_queried_object();
			if ($current_cat && isset($current_cat->term_id)) {
				$source = 'is_product_category_' . $current_cat->term_id;
				$current_ids[] = (int) $current_cat->term_id;
				$ancestors = get_ancestors($current_cat->term_id, 'product_cat');
				if (!empty($ancestors)) {
					$current_ids = array_merge($current_ids, $ancestors);
				}
			}
		} elseif (is_shop()) {
			$source = 'is_shop';
			$current_ids = [];
		} else {
			$source = 'unknown_page';
		}

		// Return array with ids and debug source
		return [
			'ids' => array_unique(array_map('absint', $current_ids)),
			'source' => $source
		];
	}
}
