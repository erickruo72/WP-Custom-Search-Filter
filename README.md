# WP Custom Search Filter (Version 2.5)

This plugin provides a robust and aesthetically pleasing solution for advanced job filtering on WordPress sites where the theme's default title and query logic conflict with custom taxonomy parameters.

It achieves flawless title and search results by combining multiple filter selections (e.g., Company, Location, Qualification) into a **single, comma-separated WordPress search term (`?s=`)**.

## ✨ Features

* **Unified Search Submission:** Uses JavaScript to intercept form submission, gather all selected taxonomy slugs, and consolidate them into the standard `$_GET['s']` parameter. This guarantees that **theme-dependent archive titles always display correctly**.
* **Custom Scrollable Dropdowns:** Replaces native `<select>` elements with custom HTML (`<div>`/`<ul>`) to enable full CSS control. The dropdown lists have a **fixed height (`300px`) and are vertically scrollable**, providing a much better user experience for lists with many options.
* **Dynamic Title Generation:** The included PHP filter (`job_filter_clean_title`) automatically formats the combined slug list from the URL into a readable archive title (e.g., "Search Results for: Absa Bank Limited, Bachelor Of Science Jobs").
* **Filter Reset:** Includes a "Clear Filters" button and browser back-button logic to ensure the form resets properly.

---

## ⚙️ Installation

1.  **Download:** Download the `job-filter-redirect.php` file.
2.  **Upload:** Upload the file to your WordPress plugins directory (`wp-content/plugins/`).
3.  **Activate:** Go to the WordPress dashboard, navigate to **Plugins**, and activate "Job Filter Redirect."

---

## 🚀 Usage

The plugin uses a single shortcode to display the entire filtering form.

**Shortcode:**

[job_filter]

Place this shortcode anywhere you want the job filter form to appear (e.g., in a page, a widget, or directly in a theme template file using `<?php echo do_shortcode('[job_filter]'); ?>`).

---

## 🛠️ Configuration & Styling

### 1. Taxonomy Structure (Prerequisite)

The plugin is hardcoded to look for top-level categories with the following names. Ensure these parent categories exist in your WordPress installation:

* Company
* Qualification
* Location
* Experience
* Job Field
* Job Type

### 2. Adjusting Dropdown Height

If you need to change the fixed height of the scrollable dropdown list, you can edit the CSS within the PHP file:

1.  Open `job-filter-redirect.php`.
2.  Find the CSS class `.select-options-list`.
3.  Modify the **`max-height`** value (e.g., change `300px` to `200px` or `400px`).

```css
.select-options-list {
    /* ... other styles ... */
    max-height: 300px; /* <-- EDIT THIS VALUE */
    overflow-y: auto; 
    /* ... other styles ... */
}
