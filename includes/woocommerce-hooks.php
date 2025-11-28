<?php

if (! defined('ABSPATH')) {
    exit;
}

function ustage_add_booking_cart_item_data($cart_item_data, $product_id)
{

    if ((int) $product_id !== (int) USTAGE_BOOKING_PRODUCT_ID) {
        return $cart_item_data;
    }

    $provider_id = isset($_POST['ustage_provider_id']) ? (int) $_POST['ustage_provider_id'] : 0;
    $date        = isset($_POST['ustage_booking_date']) ? sanitize_text_field($_POST['ustage_booking_date']) : '';
    $time        = isset($_POST['ustage_booking_time']) ? sanitize_text_field($_POST['ustage_booking_time']) : '';
    $duration    = isset($_POST['ustage_booking_duration']) ? (int) $_POST['ustage_booking_duration'] : 60;

    if ($provider_id) {
        $cart_item_data['ustage_provider_id']      = $provider_id;
        $cart_item_data['ustage_booking_date']     = $date;
        $cart_item_data['ustage_booking_time']     = $time;
        $cart_item_data['ustage_booking_duration'] = $duration;
        $cart_item_data['ustage_is_booking']       = true;

        $cart_item_data['unique_key'] = md5(microtime() . rand());
    }

    return $cart_item_data;
}
add_filter('woocommerce_add_cart_item_data', 'ustage_add_booking_cart_item_data', 10, 2);



function ustage_add_booking_order_item_meta($item, $cart_item_key, $values, $order)
{

    if (empty($values['ustage_is_booking'])) {
        return;
    }

    if (isset($values['ustage_provider_id'])) {
        $item->add_meta_data('ustage_provider_id', (int) $values['ustage_provider_id'], true);
    }

    if (isset($values['ustage_booking_date'])) {
        $item->add_meta_data('ustage_booking_date', $values['ustage_booking_date'], true);
    }

    if (isset($values['ustage_booking_time'])) {
        $item->add_meta_data('ustage_booking_time', $values['ustage_booking_time'], true);
    }

    if (isset($values['ustage_booking_duration'])) {
        $item->add_meta_data('ustage_booking_duration', (int) $values['ustage_booking_duration'], true);
    }
}
add_action('woocommerce_checkout_create_order_line_item', 'ustage_add_booking_order_item_meta', 10, 4);



function ustage_create_bookings_from_order($order_id)
{

    if (! $order_id) {
        return;
    }

    $order = wc_get_order($order_id);
    if (! $order) {
        return;
    }

    $customer_id = $order->get_user_id();

    foreach ($order->get_items() as $item_id => $item) {

        $product_id = $item->get_product_id();

        if ((int) $product_id !== (int) USTAGE_BOOKING_PRODUCT_ID) {
            continue;
        }

        $provider_id = (int) $item->get_meta('ustage_provider_id');
        $date        = $item->get_meta('ustage_booking_date');
        $time        = $item->get_meta('ustage_booking_time');
        $duration    = (int) $item->get_meta('ustage_booking_duration');
        $price       = $item->get_total();

        $booking_post_id = wp_insert_post([
            'post_type'   => 'booking',
            'post_status' => 'publish',
            'post_title'  => 'Booking #' . $order_id . ' – ' . $provider_id,
        ]);

        if ($booking_post_id && ! is_wp_error($booking_post_id)) {

            update_field('booking_provider',   $provider_id, $booking_post_id);
            update_field('booking_customer',   $customer_id, $booking_post_id);
            update_field('booking_date',       $date,        $booking_post_id);
            update_field('booking_time',       $time,        $booking_post_id);
            update_field('booking_duration',   $duration,    $booking_post_id);
            update_field('booking_price',      $price,       $booking_post_id);
            update_field('booking_order_id',   $order_id,    $booking_post_id);
            update_field('booking_status',     'pending',    $booking_post_id);
        }
    }
}
add_action('woocommerce_thankyou', 'ustage_create_bookings_from_order', 10, 1);



function ustage_set_booking_price($cart)
{

    if (is_admin() && ! defined('DOING_AJAX')) {
        return;
    }

    foreach ($cart->get_cart() as $cart_item_key => $cart_item) {

        if ((int) $cart_item['product_id'] !== (int) USTAGE_BOOKING_PRODUCT_ID) {
            continue;
        }

        $provider_id = $cart_item['ustage_provider_id']      ?? 0;
        $duration    = $cart_item['ustage_booking_duration'] ?? 60;

        if (! $provider_id) {
            continue;
        }

        $price_min = get_field('provider_price_min', 'user_' . $provider_id);
        if (! $price_min) {
            $price_min = 0;
        }

        $hours       = $duration / 60;
        $final_price = $price_min * $hours;

        $cart_item['data']->set_price(floatval($final_price));
    }
}
add_action('woocommerce_before_calculate_totals', 'ustage_set_booking_price', 10, 1);
