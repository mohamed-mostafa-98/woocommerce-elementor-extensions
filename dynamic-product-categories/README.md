# Dynamic Product Categories

**Author**: [Mohamed Mostafa](https://www.linkedin.com/in/mohamed-hella/)

A comprehensive WooCommerce + Elementor plugin designed to dynamically display responsive product category grids with advanced context-awareness. 

*(Note: The global Elementor visibility conditions previously included in this plugin have been spun off into their own dedicated, heavily optimized plugin: **[WC Category Visibility](../wc-category-visibility/README.md)**).*

## 🚀 Key Features

### 1. Advanced Product Categories Widget
The flagship widget for displaying dynamic category grids with extreme flexibility natively inside Elementor.
- **3 Smart Display Modes**:
  - `Current Product`: Auto-detects the root category of the product being viewed (perfect for Single Product pages).
  - `Current Category`: Auto-detects the category archive being viewed (perfect for Category pages).
  - `Static Category`: Manually pick a specific root category to display.
- **Advanced Visibility Filters**: Show the widget only when specific category conditions are met.
- **Custom Links (Repeater)**: Add custom items (like "Vaccines") that link to specific pages, with conditional display based on the active root category.
- **Pro Styling**: Full control over column counts (1-6), grid gaps, background colors, borders, box shadows, and typography.
- **Smart Sorting**: Order categories by Name, ID, Product Count, Slug, or Menu Order.

### 2. Classic Dynamic Categories Widget
A lightweight, streamlined version of the widget focused solely on auto-detecting the current product's root branch without the overhead of advanced styling controls.

### 3. Shortcode Support
Display your dynamic category grid anywhere (even outside of Elementor or in standard Gutenberg blocks) using a simple shortcode:
`[dynamic_product_categories columns="3" show_count="true" orderby="name" order="ASC"]`

## 🛠 How to Use

### In Elementor:
1. Search for **"Advanced Product Categories"** in the Elementor widget panel.
2. Drag the widget onto your page or Theme Builder template.
3. In the **Content** tab, choose your **Display Mode** (e.g., *Current Category* for an Archive template).
4. Use the **Widget Display Conditions** to restrict where the widget appears.
5. Add **Custom Categories** via the repeater to inject promotional or static links into the dynamic grid.
6. Customize the layout and design in the **Style** tab.

## 🧠 Logic Rules & Technical Details
- **Root Ancestor Detection**: The plugin traces product categories all the way up to the top-level parent to ensure consistent "Branch-based" display grids.
- **Cache Evasion**: Unlike structural Elementor elements, this widget natively bypasses Elementor's template cache by executing its query logic directly inside the dynamic `render()` method. This ensures the category grid is always fresh and accurately reflects the current URL context without being locked into static HTML transients.
- **Smart Hierarchy**: Visibility conditions automatically include sub-categories. If you select "Human Products", it will also match "Cosmetics", "Supplements", etc.

---
*Created for specialized WooCommerce workflows by Mohamed Mostafa.*
