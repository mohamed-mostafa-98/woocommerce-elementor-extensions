<?php

if (!defined('ABSPATH')) {
    exit;
}

class WC_PAE_Admin
{

    public function __construct()
    {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('wp_ajax_wc_pae_get_product_data', array($this, 'get_product_data'));
        add_action('wp_ajax_wc_pae_save_product', array($this, 'save_product'));
        add_filter('post_row_actions', array($this, 'add_row_actions'), 10, 2);
    }

    /**
     * Add "Advanced Edit" link to product row actions.
     */
    public function add_row_actions($actions, $post)
    {
        if ('product' !== $post->post_type) {
            return $actions;
        }

        $url = add_query_arg(
            array(
                'page' => 'wc-product-advanced-editor',
                'action' => 'edit',
                'product_id' => $post->ID
            ),
            admin_url('admin.php')
        );

        $actions['wc_pae_edit'] = '<a href="' . esc_url($url) . '">Advanced Edit</a>';

        return $actions;
    }

    /**
     * Add sub-menu under WooCommerce.
     */
    public function add_admin_menu()
    {
        add_submenu_page(
            'woocommerce',
            'Product Advanced Editor',
            'Product Advanced Editor',
            'manage_woocommerce',
            'wc-product-advanced-editor',
            array($this, 'render_admin_page')
        );
    }

    /**
     * Enqueue scripts and styles.
     */
    public function enqueue_scripts($hook)
    {
        if ('woocommerce_page_wc-product-advanced-editor' !== $hook) {
            return;
        }

        wp_enqueue_style('wc-pae-admin-css', WC_PAE_URL . 'assets/css/admin.css', array(), WC_PAE_VERSION);

        // Enqueue WordPress Media Uploader
        wp_enqueue_media();

        // Enqueue Select2 if WooCommerce is active and registers it, otherwise we rely on default or our own handling
        // WooCommerce usually enqueues select2-core on its pages. We might need to ensure it's loaded.
        if (class_exists('WooCommerce')) {
            wp_enqueue_style('select2');
            wp_enqueue_script('select2');
        }

        wp_enqueue_script('wc-pae-admin-js', WC_PAE_URL . 'assets/js/admin.js', array('jquery'), WC_PAE_VERSION, true);

        wp_localize_script('wc-pae-admin-js', 'wc_pae_vars', array(
            'nonce' => wp_create_nonce('wc_pae_nonce'),
            'search_nonce' => wp_create_nonce('search-products') // Standard WC nonce usually
        ));
    }

    /**
     * Render the admin page HTML.
     */
    public function render_admin_page()
    {
        $action = isset($_GET['action']) ? sanitize_text_field($_GET['action']) : 'list';
        $product_id = isset($_GET['product_id']) ? intval($_GET['product_id']) : 0;

        ?>
        <div class="wrap wc-pae-wrap">
            <div class="wc-pae-header">
                <h1>Product Advanced Editor</h1>
            </div>

            <?php
            if ('edit' === $action && $product_id) {
                $this->render_editor_view($product_id);
            } else {
                $this->render_list_view();
            }
            ?>
        </div>
        <?php
    }

    /**
     * Render the Product List View.
     */
    private function render_list_view()
    {
        $paged = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
        $args = array(
            'limit' => 20,
            'page' => $paged,
            'paginate' => true,
        );

        $results = wc_get_products($args);
        $products = $results->products;
        $total_pages = $results->max_num_pages;

        echo '<table class="wp-list-table widefat fixed striped table-view-list">';
        echo '<thead><tr>
                <th style="width: 80px;">Image</th>
                <th>Name</th>
                <th>SKU</th>
                <th>Price</th>
                <th>Status</th>
                <th style="width: 150px;">Actions</th>
              </tr></thead>';
        echo '<tbody>';

        if (!empty($products)) {
            foreach ($products as $product) {
                $edit_link = add_query_arg(
                    array(
                        'page' => 'wc-product-advanced-editor',
                        'action' => 'edit',
                        'product_id' => $product->get_id()
                    ),
                    admin_url('admin.php')
                );

                $image = $product->get_image(array(50, 50));

                echo '<tr>';
                echo '<td>' . $image . '</td>';
                echo '<td><strong><a href="' . esc_url($edit_link) . '">' . esc_html($product->get_name()) . '</a></strong></td>';
                echo '<td>' . esc_html($product->get_sku()) . '</td>';
                echo '<td>' . $product->get_price_html() . '</td>';
                echo '<td>' . esc_html(ucfirst($product->get_status())) . '</td>';
                echo '<td><a href="' . esc_url($edit_link) . '" class="button button-small">Advanced Edit</a></td>';
                echo '</tr>';
            }
        } else {
            echo '<tr><td colspan="6">No products found.</td></tr>';
        }

        echo '</tbody></table>';

        // Pagination
        if ($total_pages > 1) {
            $page_links = paginate_links(array(
                'base' => add_query_arg('paged', '%#%'),
                'format' => '',
                'prev_text' => '&laquo;',
                'next_text' => '&raquo;',
                'total' => $total_pages,
                'current' => $paged
            ));

            if ($page_links) {
                echo '<div class="tablenav bottom"><div class="tablenav-pages">' . $page_links . '</div></div>';
            }
        }
    }

    /**
     * Render the Editor View.
     */
    private function render_editor_view($product_id)
    {
        $product = wc_get_product($product_id);
        if (!$product) {
            echo '<div class="notice notice-error"><p>Product not found.</p></div>';
            $this->render_list_view();
            return;
        }

        $back_link = remove_query_arg(array('action', 'product_id'));

        // Prepare data
        $image_id = $product->get_image_id();
        $image_url = $image_id ? wp_get_attachment_url($image_id) : '';

        $gallery_ids = $product->get_gallery_image_ids();
        $gallery_image_id = !empty($gallery_ids) ? $gallery_ids[0] : '';
        $gallery_image_url = $gallery_image_id ? wp_get_attachment_url($gallery_image_id) : '';

        ?>
        <div class="wc-pae-toolbar">
            <a href="<?php echo esc_url($back_link); ?>" class="button">&larr; Back to Products</a>
            <h2>Editing: <?php echo esc_html($product->get_name()); ?></h2>
        </div>

        <div class="wc-pae-editor-container" style="display: block;">
            <input type="hidden" id="wc_pae_product_id" value="<?php echo esc_attr($product_id); ?>">

            <!-- Product Name -->
            <div class="wc-pae-field-group">
                <label for="wc_pae_product_name">Product Name</label>
                <input type="text" id="wc_pae_product_name" value="<?php echo esc_attr($product->get_name()); ?>">
            </div>

            <!-- Usages (Short Description) -->
            <div class="wc-pae-field-group">
                <label for="wc_pae_short_description">Usages</label>
                <?php
                $short_desc_settings = array(
                    'textarea_name' => 'wc_pae_short_description',
                    'textarea_rows' => 10,
                    'media_buttons' => true,
                    'tinymce' => array(
                        'toolbar1' => 'bold italic underline | bullist numlist | link unlink | undo redo',
                        'content_style' => 'ul, ol { margin-bottom: 0px; }',
                    ),
                );
                wp_editor($product->get_short_description(), 'wc_pae_short_description', $short_desc_settings);
                ?>
            </div>

            <!-- Description Editor -->
            <div class="wc-pae-field-group">
                <label for="wc_pae_description">Product Description (Rich Text)</label>
                <?php
                $style_formats = array(
                    array(
                        'title' => 'Line Height',
                        'items' => array(
                            array('title' => '1.0', 'selector' => 'p, h1, h2, h3, h4, h5, h6, div, ul, ol, li', 'styles' => array('line-height' => '1.0')),
                            array('title' => '1.2', 'selector' => 'p, h1, h2, h3, h4, h5, h6, div, ul, ol, li', 'styles' => array('line-height' => '1.2')),
                            array('title' => '1.5', 'selector' => 'p, h1, h2, h3, h4, h5, h6, div, ul, ol, li', 'styles' => array('line-height' => '1.5')),
                            array('title' => '2.0', 'selector' => 'p, h1, h2, h3, h4, h5, h6, div, ul, ol, li', 'styles' => array('line-height' => '2.0')),
                            array('title' => '2.5', 'selector' => 'p, h1, h2, h3, h4, h5, h6, div, ul, ol, li', 'styles' => array('line-height' => '2.5')),
                        ),
                    ),
                    array(
                        'title' => 'Paragraph Spacing',
                        'items' => array(
                            array('title' => 'Small Bottom Margin', 'selector' => 'p, h1, h2, h3, h4, h5, h6, div, ul, ol, li', 'styles' => array('margin-bottom' => '5px')),
                            array('title' => 'Medium Bottom Margin', 'selector' => 'p, h1, h2, h3, h4, h5, h6, div, ul, ol, li', 'styles' => array('margin-bottom' => '15px')),
                            array('title' => 'Large Bottom Margin', 'selector' => 'p, h1, h2, h3, h4, h5, h6, div, ul, ol, li', 'styles' => array('margin-bottom' => '25px')),
                            array('title' => 'No Bottom Margin', 'selector' => 'p, h1, h2, h3, h4, h5, h6, div, ul, ol, li', 'styles' => array('margin-bottom' => '0px')),
                        ),
                    ),
                );

                $settings = array(
                    'textarea_name' => 'wc_pae_description',
                    'textarea_rows' => 20,
                    'media_buttons' => true,
                    'tinymce' => array(
                        // Add styleselect to toolbar
                        'toolbar1' => 'styleselect | formatselect | fontselect fontsizeselect | bold italic underline strikethrough | forecolor backcolor',
                        'toolbar2' => 'alignleft aligncenter alignright alignjustify | bullist numlist | outdent indent | link unlink | undo redo | charmap hr',
                        // Pass JSON encoded style formats
                        'style_formats' => json_encode($style_formats),
                        // CSS to remove bottom margin from lists by default
                        'content_style' => 'ul, ol { margin-bottom: 0px; }',
                    ),
                );
                wp_editor($product->get_description(), 'wc_pae_description', $settings);
                ?>
            </div>

            <!-- Categories -->
            <div class="wc-pae-field-group">
                <label>Product Categories</label>
                <div class="wc-pae-categories-list"
                    style="max-height: 200px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; background: #fff;">
                    <ul class="wc-pae-category-checklist">
                        <?php
                        wp_terms_checklist($product_id, array(
                            'taxonomy' => 'product_cat',
                            'checked_ontop' => true,
                            // Fix for naming if needed, but default tax_input[taxonomy] is fine if we process it
                        ));
                        ?>
                    </ul>
                </div>
            </div>

            <!-- Images -->
            <div class="wc-pae-field-group">
                <label>Product Images</label>

                <div class="wc-pae-image-uploader">
                    <strong>Featured Image</strong>
                    <img id="wc_pae_featured_image_preview" src="<?php echo esc_url($image_url); ?>"
                        class="wc-pae-image-preview <?php echo empty($image_url) ? 'hidden' : ''; ?>">
                    <input type="hidden" id="wc_pae_featured_image_id" value="<?php echo esc_attr($image_id); ?>">
                    <br>
                    <button class="button wc-pae-upload-btn"
                        data-target="featured"><?php echo empty($image_url) ? 'Upload Image' : 'Change Image'; ?></button>
                    <a href="#" class="wc-pae-remove-image" data-target="featured">Remove</a>
                </div>

                <div class="wc-pae-image-uploader">
                    <strong>Gallery Image (Single)</strong>
                    <img id="wc_pae_gallery_image_preview" src="<?php echo esc_url($gallery_image_url); ?>"
                        class="wc-pae-image-preview <?php echo empty($gallery_image_url) ? 'hidden' : ''; ?>">
                    <input type="hidden" id="wc_pae_gallery_image_id" value="<?php echo esc_attr($gallery_image_id); ?>">
                    <br>
                    <button class="button wc-pae-upload-btn"
                        data-target="gallery"><?php echo empty($gallery_image_url) ? 'Upload Image' : 'Change Image'; ?></button>
                    <a href="#" class="wc-pae-remove-image" data-target="gallery">Remove</a>
                </div>
            </div>

            <!-- Actions -->
            <div class="wc-pae-actions">
                <span class="wc-pae-spinner spinner"></span>
                <button class="button button-primary button-large wc-pae-save-btn">Update Product</button>
            </div>
        </div>
        <?php
    }

    /**
     * AJAX Handler: Get Product Data (Deprecated/Removed for direct render)
     */
    public function get_product_data()
    {
        // Method removed as we now render data server-side
    }

    /**
     * AJAX Handler: Save Product Data
     */
    public function save_product()
    {
        check_ajax_referer('wc_pae_nonce', 'security');

        if (!current_user_can('edit_products')) {
            wp_send_json_error('Permission denied');
        }

        $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
        $product_name = isset($_POST['product_name']) ? sanitize_text_field($_POST['product_name']) : '';
        // Allow HTML in description for the rich text editor
        $product_description = isset($_POST['product_description']) ? wp_kses_post($_POST['product_description']) : '';
        $product_short_description = isset($_POST['product_short_description']) ? wp_kses_post($_POST['product_short_description']) : '';


        $featured_image_id = isset($_POST['featured_image_id']) ? intval($_POST['featured_image_id']) : '';
        $gallery_image_id = isset($_POST['gallery_image_id']) ? intval($_POST['gallery_image_id']) : '';

        // Handle Categories
        // tax_input is usually an array like tax_input[product_cat] = array( ID, ID... )
        $category_ids = array();
        if (isset($_POST['tax_input']['product_cat']) && is_array($_POST['tax_input']['product_cat'])) {
            $category_ids = array_map('intval', $_POST['tax_input']['product_cat']);
        }

        $product = wc_get_product($product_id);

        if (!$product) {
            wp_send_json_error('Product not found');
        }

        try {
            // Update Name
            $product->set_name($product_name);

            // Update Description
            $product->set_description($product_description);

            // Update Short Description (Usages)
            $product->set_short_description($product_short_description);

            // Update Featured Image
            if ($featured_image_id !== '') {
                $product->set_image_id($featured_image_id);
            } else {
                // If ID is empty string/0 and was explicitly sent, remove it? 
                // The prompt says "Automatically replace existing images", implying if we remove it, it should be removed.
                // Our UI sends '' if removed.
                $product->set_image_id('');
            }

            // Update Gallery Image (Single)
            if ($gallery_image_id) {
                $product->set_gallery_image_ids(array($gallery_image_id));
            } else {
                $product->set_gallery_image_ids(array());
            }

            // Update Categories
            $product->set_category_ids($category_ids);

            $product->save();

            wp_send_json_success('Product updated successfully');

        } catch (Exception $e) {
            wp_send_json_error($e->getMessage());
        }
    }
}
