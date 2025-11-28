<?php

/**
 * Plugin Name: Ustage Booking
 * Description: Custom booking post type + integration with WooCommerce.
 * Author: Yurii Kobets inoplanetyura
 * Version: 0.1
 */

if (! defined('ABSPATH')) {
    exit;
}

define('USTAGE_BOOKING_PRODUCT_ID', 66);

require_once __DIR__ . '/includes/cpt-taxonomies.php';
require_once __DIR__ . '/includes/acf-fields.php';
require_once __DIR__ . '/includes/woocommerce-hooks.php';
require_once __DIR__ . '/includes/shortcodes.php';
