# WooCommerce Elementor Extensions

This repository contains custom WooCommerce extensions built specifically for Elementor, designed to enhance product filtering, category display, and conditional visibility logic on complex e-commerce sites.

## Included Plugins

### 1. [WC Category Visibility](./wc-category-visibility/README.md)
A powerful conditional visibility engine that allows you to hide or show any Elementor widget, column, container, or section based on the current WooCommerce product category context. Includes a robust cache-bypassing architecture to ensure flawless operation on highly-cached sites.

### 2. [Dynamic Product Categories](./dynamic-product-categories/README.md)
An advanced Elementor widget that dynamically renders a responsive grid of WooCommerce subcategories based on the current page's context. Supports custom static fallback links and flexible display conditions.

### 3. [WC Product Advanced Editor](./wc_product_advanced_editor/README.md)
A specialized admin tool for WooCommerce that provides a rich-text editing experience for products. Features include advanced typography controls (line height, spacing), category management, and an integrated image uploader for both featured and gallery images.

## Requirements
- WordPress 6.0+
- WooCommerce 8.0+
- Elementor 3.18+ (Required for Visibility and Category plugins)

## Installation

### Method 1: Manual Folder Upload (Recommended for Developers)
1. Clone or download this repository.
2. Move the individual plugin folders (`wc-category-visibility`, `dynamic-product-categories`, or `wc_product_advanced_editor`) into your WordPress `wp-content/plugins/` directory.
3. Activate the plugins through the 'Plugins' menu in WordPress.

### Method 2: Direct Zip Download (Standard)
If you do not have FTP access:
1. You can download the pre-built `.zip` files for each plugin directly from the list above in this repository.
2. Go to your WordPress Dashboard > **Plugins** > **Add New** > **Upload Plugin**.
3. Choose the downloaded `.zip` file and click **Install Now**.

### Method 3: Manual Zip Creation
1. Download this repository as a `.zip` file from the green **Code** button at the top.
2. Extract the zip on your computer.
3. Right-click any individual plugin folder (e.g., `wc-category-visibility`) and select **Compress to ZIP file**.
4. Go to your WordPress Dashboard > **Plugins** > **Add New** > **Upload Plugin**.
5. Choose your newly created `.zip` file and click **Install Now**.

