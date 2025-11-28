<?php

if (! defined('ABSPATH')) {
    exit;
}

function ustage_booking_form_shortcode($atts)
{
    $atts = shortcode_atts([
        'provider' => 0,
    ], $atts);

    $provider_id = (int) $atts['provider'];

    if (! $provider_id) {
        return '<p>No provider selected.</p>';
    }

    $cart_url = wc_get_cart_url();

    ob_start();
?>
    <form method="post" action="<?php echo esc_url($cart_url); ?>">
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
add_shortcode('ustage_booking_form', 'ustage_booking_form_shortcode');
/* // DEBUG: перевірка provider_price_min для um_entertainer
if ( ! function_exists('ustage_debug_provider_price') ) {

    function ustage_debug_provider_price( $atts ) {
        $atts = shortcode_atts([
            'provider' => 0,
        ], $atts);

        $provider_id = (int) $atts['provider'];

        if ( ! $provider_id ) {
            return '<p>[ustage_debug_price] No provider ID passed.</p>';
        }

        // ACF value
        $acf_price = function_exists('get_field')
            ? get_field('provider_price_min', 'user_' . $provider_id)
            : 'get_field() not available';

        // Raw usermeta
        $meta_price = get_user_meta($provider_id, 'provider_price_min', true);

        // Лог у error_log на всякий
        error_log(
            'USTAGE DEBUG provider_price_min for user ' . $provider_id .
            ' | ACF=' . print_r($acf_price, true) .
            ' | META=' . print_r($meta_price, true)
        );

        $output  = "[ustage_debug_price]\n";
        $output .= "Provider ID: {$provider_id}\n\n";
        $output .= "get_field('provider_price_min', 'user_{$provider_id}'):\n";
        $output .= print_r($acf_price, true) . "\n\n";
        $output .= "get_user_meta({$provider_id}, 'provider_price_min', true):\n";
        $output .= print_r($meta_price, true) . "\n";

        return '<pre>' . esc_html($output) . '</pre>';
    }

    add_shortcode('ustage_debug_price', 'ustage_debug_provider_price');
}
 */