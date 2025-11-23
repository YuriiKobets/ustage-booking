<?php
/**
 * Plugin Name: Ustage Booking Core
 * Description: Custom booking post type + integration with WooCommerce.
 * Author: Yurii Kobets inoplanetyura
 * Version: 0.1
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


define( 'USTAGE_BOOKING_PRODUCT_ID', 66 );

use StoutLogic\AcfBuilder\FieldsBuilder;


function ustage_register_booking_cpt() {

    $labels = [
        'name'               => 'Bookings',
        'singular_name'      => 'Booking',
        'menu_name'          => 'Bookings',
        'name_admin_bar'     => 'Booking',
        'add_new'            => 'Add Booking',
        'add_new_item'       => 'Add New Booking',
        'edit_item'          => 'Edit Booking',
        'new_item'           => 'New Booking',
        'view_item'          => 'View Booking',
        'search_items'       => 'Search Bookings',
        'not_found'          => 'No bookings found',
        'not_found_in_trash' => 'No bookings found in Trash',
    ];

    $args = [
        'labels'             => $labels,
        'public'             => false,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'menu_position'      => 25,
        'menu_icon'          => 'dashicons-calendar-alt',
        'capability_type'    => 'post',
        'supports'           => [ 'title' ],
        'has_archive'        => false,
        'rewrite'            => false,
    ];

    register_post_type( 'booking', $args );
}
add_action( 'init', 'ustage_register_booking_cpt' );

/**
 * ACF Builder : booking_data + provider_profile
 */
add_action( 'acf/init', function () {

    // ====== BOOKING FIELDS ======
    $booking = new FieldsBuilder( 'booking_data', [
        'title' => 'Booking data',
    ] );

    $booking
        ->setLocation( 'post_type', '==', 'booking' );

    $booking
        ->addUser('booking_provider', [
            'label'         => 'Provider',
            'role'          => ['provider'],
            'return_format' => 'id',
            'required'      => 1,
        ])
        ->addUser('booking_customer', [
            'label'         => 'Customer',
            'role'          => ['customer'],
            'return_format' => 'id',
            'required'      => 1,
        ])
        ->addDatePicker('booking_date', [
            'label'          => 'Date',
            'display_format' => 'Y-m-d',
            'return_format'  => 'Y-m-d',
            'required'       => 1,
        ])
        ->addTimePicker('booking_time', [
            'label'          => 'Time',
            'display_format' => 'H:i',
            'return_format'  => 'H:i',
            'required'       => 0,
        ])
        ->addNumber('booking_duration', [
            'label'         => 'Duration (minutes)',
            'default_value' => 60,
            'min'           => 0,
            'step'          => 15,
        ])
        ->addNumber('booking_price', [
            'label' => 'Price',
            'min'   => 0,
            'step'  => 1,
        ])
        ->addNumber('booking_order_id', [
            'label'        => 'Order ID',
            'instructions' => 'WooCommerce order ID linked to this booking.',
        ])
        ->addSelect('booking_status', [
            'label'         => 'Booking Status',
            'choices'       => [
                'pending'   => 'Pending',
                'confirmed' => 'Confirmed',
                'cancelled' => 'Cancelled',
            ],
            'default_value' => 'pending',
            'ui'            => 1,
            'required'      => 1,
        ])
        ->addTextarea('booking_notes', [
            'label'     => 'Notes',
            'new_lines' => 'br',
        ]);

    acf_add_local_field_group( $booking->build() );

    // ====== PROVIDER PROFILE FIELDS ======
    $providerProfile = new FieldsBuilder( 'provider_profile', [
        'title' => 'Provider profile',
    ] );

    $providerProfile
        ->setLocation( 'user_role', '==', 'provider' );

    $providerProfile
        ->addText('provider_stage_name', [
            'label'       => 'Stage name',
            'instructions'=> 'Displayed name for customers.',
            'required'    => 1,
        ])
        ->addText('provider_genre', [
            'label'       => 'Genre',
            'instructions'=> 'e.g. DJ, Cover band, Acoustic, Stand-up.',
        ])
        ->addNumber('provider_price_min', [
            'label'         => 'Price from',
            'prepend'       => '€',
            'min'           => 0,
            'step'          => 1,
        ])
        ->addNumber('provider_price_max', [
            'label'         => 'Price to',
            'prepend'       => '€',
            'min'           => 0,
            'step'          => 1,
        ])
        ->addText('provider_city', [
            'label' => 'City',
        ])
        ->addText('provider_country', [
            'label' => 'Country',
        ])
        ->addTextarea('provider_description', [
            'label'     => 'Description',
            'new_lines' => 'br',
        ])
        ->addGallery('provider_gallery', [
            'label' => 'Gallery',
        ])
        ->addUrl('provider_video_url', [
            'label'       => 'Video URL',
            'instructions'=> 'Link to promo video (YouTube/Vimeo).',
        ])
        ->addRepeater('provider_social_links', [
            'label'        => 'Social links',
            'button_label' => 'Add social link',
            'layout'       => 'table',
        ])
            ->addText('label', [
                'label' => 'Label (e.g. Instagram)',
            ])
            ->addUrl('url', [
                'label' => 'URL',
            ])
        ->endRepeater();

    acf_add_local_field_group( $providerProfile->build() );
} );




function ustage_add_booking_cart_item_data( $cart_item_data, $product_id ) {

    if ( (int) $product_id !== (int) USTAGE_BOOKING_PRODUCT_ID ) {
        return $cart_item_data;
    }

    $provider_id = isset( $_POST['ustage_provider_id'] ) ? (int) $_POST['ustage_provider_id'] : 0;
    $date        = isset( $_POST['ustage_booking_date'] ) ? sanitize_text_field( $_POST['ustage_booking_date'] ) : '';
    $time        = isset( $_POST['ustage_booking_time'] ) ? sanitize_text_field( $_POST['ustage_booking_time'] ) : '';
    $duration    = isset( $_POST['ustage_booking_duration'] ) ? (int) $_POST['ustage_booking_duration'] : 60;

    if ( $provider_id ) {
        $cart_item_data['ustage_provider_id']      = $provider_id;
        $cart_item_data['ustage_booking_date']     = $date;
        $cart_item_data['ustage_booking_time']     = $time;
        $cart_item_data['ustage_booking_duration'] = $duration;
        $cart_item_data['ustage_is_booking']       = true;

        $cart_item_data['unique_key'] = md5( microtime() . rand() );
    }

    return $cart_item_data;
}
add_filter( 'woocommerce_add_cart_item_data', 'ustage_add_booking_cart_item_data', 10, 2 );



function ustage_add_booking_order_item_meta( $item, $cart_item_key, $values, $order ) {

    if ( empty( $values['ustage_is_booking'] ) ) {
        return;
    }

    if ( isset( $values['ustage_provider_id'] ) ) {
        $item->add_meta_data( 'ustage_provider_id', (int) $values['ustage_provider_id'], true );
    }

    if ( isset( $values['ustage_booking_date'] ) ) {
        $item->add_meta_data( 'ustage_booking_date', $values['ustage_booking_date'], true );
    }

    if ( isset( $values['ustage_booking_time'] ) ) {
        $item->add_meta_data( 'ustage_booking_time', $values['ustage_booking_time'], true );
    }

    if ( isset( $values['ustage_booking_duration'] ) ) {
        $item->add_meta_data( 'ustage_booking_duration', (int) $values['ustage_booking_duration'], true );
    }
}
add_action( 'woocommerce_checkout_create_order_line_item', 'ustage_add_booking_order_item_meta', 10, 4 );




function ustage_create_bookings_from_order( $order_id ) {

    if ( ! $order_id ) {
        return;
    }

    $order = wc_get_order( $order_id );
    if ( ! $order ) {
        return;
    }

    $customer_id = $order->get_user_id();

    foreach ( $order->get_items() as $item_id => $item ) {

        $product_id = $item->get_product_id();

        if ( (int) $product_id !== (int) USTAGE_BOOKING_PRODUCT_ID ) {
            continue;
        }

        $provider_id = (int) $item->get_meta( 'ustage_provider_id' );
        $date        = $item->get_meta( 'ustage_booking_date' );
        $time        = $item->get_meta( 'ustage_booking_time' );
        $duration    = (int) $item->get_meta( 'ustage_booking_duration' );
        $price       = $item->get_total();

        $booking_post_id = wp_insert_post( [
            'post_type'   => 'booking',
            'post_status' => 'publish',
            'post_title'  => 'Booking #' . $order_id . ' – ' . $provider_id,
        ] );

        if ( $booking_post_id && ! is_wp_error( $booking_post_id ) ) {

            update_field( 'booking_provider',   $provider_id, $booking_post_id );
            update_field( 'booking_customer',   $customer_id, $booking_post_id );
            update_field( 'booking_date',       $date,        $booking_post_id );
            update_field( 'booking_time',       $time,        $booking_post_id );
            update_field( 'booking_duration',   $duration,    $booking_post_id );
            update_field( 'booking_price',      $price,       $booking_post_id );
            update_field( 'booking_order_id',   $order_id,    $booking_post_id );
            update_field( 'booking_status',     'pending',    $booking_post_id );
        }
    }
}
add_action( 'woocommerce_thankyou', 'ustage_create_bookings_from_order', 10, 1 );



function ustage_set_booking_price( $cart ) {

    if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
        return;
    }

    foreach ( $cart->get_cart() as $cart_item_key => $cart_item ) {

        if ( (int) $cart_item['product_id'] !== (int) USTAGE_BOOKING_PRODUCT_ID ) {
            continue;
        }

        $provider_id = $cart_item['ustage_provider_id']      ?? 0;
        $duration    = $cart_item['ustage_booking_duration'] ?? 60;

        if ( ! $provider_id ) {
            continue;
        }

        $price_min = get_field( 'provider_price_min', 'user_' . $provider_id );
        if ( ! $price_min ) {
            $price_min = 0;
        }

        $hours       = $duration / 60;
        $final_price = $price_min * $hours;

        $cart_item['data']->set_price( floatval( $final_price ) );
    }
}
add_action( 'woocommerce_before_calculate_totals', 'ustage_set_booking_price', 10, 1 );


/**
 * Shortcode [ustage_booking_form provider="USER_ID"]
 * Виводить форму бронювання для конкретного provider'а
 */
function ustage_booking_form_shortcode( $atts ) {
    $atts = shortcode_atts( [
        'provider' => 0,
    ], $atts );

    $provider_id = (int) $atts['provider'];

    if ( ! $provider_id ) {
        return '<p>No provider selected.</p>';
    }

    // URL корзини WooCommerce
    $cart_url = wc_get_cart_url();

    ob_start();
    ?>
    <form method="post" action="<?php echo esc_url( $cart_url ); ?>">
        <input type="hidden" name="add-to-cart" value="<?php echo (int) USTAGE_BOOKING_PRODUCT_ID; ?>">
        <input type="hidden" name="ustage_provider_id" value="<?php echo (int) $provider_id; ?>">

        <p>
            <label>Date:<br>
                <input type="date" name="ustage_booking_date" required>
            </label>
        </p>

        <p>
            <label>Time:<br>
                <input type="time" name="ustage_booking_time">
            </label>
        </p>

        <p>
            <label>Duration (minutes):<br>
                <input type="number" name="ustage_booking_duration" value="60" min="15" step="15">
            </label>
        </p>

        <button type="submit">Book this provider</button>
    </form>
    <?php

    return ob_get_clean();
}
add_shortcode( 'ustage_booking_form', 'ustage_booking_form_shortcode' );
