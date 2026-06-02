<?php
if (!defined('ABSPATH')) {
    exit;
}
add_shortcode('checkout_btn', 'order_action');

function order_action()
{
    ob_start();
    global $product;
    // https://kaporghor.com/checkout/?wc-quick-buy-now=12474&quantity=1
    $product_id = $product->get_id();
    $direct_checkout_url = wc_get_checkout_url() . '?wc-quick-buy-now=' . $product_id . '&quantity=1';
    echo '<a class="checkout_btn" href="' . $direct_checkout_url . '"> অর্ডার করুন </a>';

    return ob_get_clean();
}


add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('pfwc-order-action', PFWC_URL . 'assets/orderAction.css', [], '1.0.0');

    if (is_checkout() && isset($_GET['add-to-cart'])) {
        wp_add_inline_script('jquery-core', 'history.replaceState(null, "", window.location.pathname);');
    }
});
