<?php
/**
 * Plugin Name: Popup For Woocommerce
 * Description: Display a fully customizable notice popup on the WooCommerce checkout page. Ideal for announcing delivery schedules, holidays, or important order notices. Manage the popup title, message, note, display duration, and enable/disable status directly from your WordPress dashboard.
 * Plugin URI: https://simple-contact-form-management.com
 * Version: 1.0.0
 * Author: Fardin Ahmed
 * Author URI: https://github.com/devfardin
 * Text Domain: pfwc
 * Requires Plugins: woocommerce
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'PFWC_DIR', plugin_dir_path( __FILE__ ) );
define( 'PFWC_URL', plugin_dir_url( __FILE__ ) );

// --- Admin Menu ---
add_action( 'admin_menu', function() {
    add_menu_page(
        'Checkout Popup',
        'Checkout Popup',
        'manage_options',
        'pfwc-settings',
        'pfwc_settings_page',
        'dashicons-megaphone',
        56
    );
});

function pfwc_settings_page() {
    if ( isset( $_POST['pfwc_save'] ) && check_admin_referer( 'pfwc_nonce' ) ) {
        update_option( 'pfwc_enabled',  isset( $_POST['pfwc_enabled'] ) ? 1 : 0 );
        update_option( 'pfwc_title',    sanitize_text_field( $_POST['pfwc_title'] ) );
        update_option( 'pfwc_text',     sanitize_textarea_field( $_POST['pfwc_text'] ) );
        update_option( 'pfwc_note',     sanitize_text_field( $_POST['pfwc_note'] ) );
        update_option( 'pfwc_duration', absint( $_POST['pfwc_duration'] ) );
        echo '<div class="notice notice-success"><p>✅ সেটিংস সেভ হয়েছে!</p></div>';
    }

    $enabled  = get_option( 'pfwc_enabled', 1 );
    $title    = get_option( 'pfwc_title',   '⚠️ ঈদের ছুটির কারণে কুরিয়ার সার্ভিস বন্ধ ⚠️' );
    $text     = get_option( 'pfwc_text',    "আপনার অর্ডারটি\nঢাকার ভিতরে ২৬ তারিখে ডেলিভারি পাবেন।\nঢাকার বাইরে ২৭ তারিখ অথবা ২৮ তারিখ ডেলিভারি পাবেন" );
    $note     = get_option( 'pfwc_note',    '❗ দয়া করে ১০০% নিশ্চিত হয়ে অর্ডার কনফার্ম করুন।' );
    $duration = get_option( 'pfwc_duration', 7 );
    ?>
    <div class="wrap">
        <h1>Checkout Popup সেটিংস</h1>
        <form method="post">
            <?php wp_nonce_field( 'pfwc_nonce' ); ?>
            <table class="form-table">
                <tr>
                    <th>Popup চালু করুন</th>
                    <td><label><input type="checkbox" name="pfwc_enabled" value="1" <?php checked( $enabled, 1 ); ?>> সক্রিয়</label></td>
                </tr>
                <tr>
                    <th>Title (শিরোনাম)</th>
                    <td><input type="text" name="pfwc_title" value="<?php echo esc_attr( $title ); ?>" class="large-text"></td>
                </tr>
                <tr>
                    <th>মূল টেক্সট</th>
                    <td><textarea name="pfwc_text" rows="4" class="large-text"><?php echo esc_textarea( $text ); ?></textarea>
                    <p class="description">প্রতিটি লাইন নতুন লাইনে লিখুন।</p></td>
                </tr>
                <tr>
                    <th>Note (নিচের লাইন)</th>
                    <td><input type="text" name="pfwc_note" value="<?php echo esc_attr( $note ); ?>" class="large-text"></td>
                </tr>
                <tr>
                    <th>কতক্ষণ দেখাবে (সেকেন্ড)</th>
                    <td><input type="number" name="pfwc_duration" value="<?php echo esc_attr( $duration ); ?>" min="1" max="60" class="small-text"> সেকেন্ড</td>
                </tr>
            </table>
            <?php submit_button( 'সেভ করুন', 'primary', 'pfwc_save' ); ?>
        </form>
    </div>
    <?php
}

// --- Frontend ---
add_action( 'wp_enqueue_scripts', function() {
    if ( ! function_exists( 'is_checkout' ) || ! is_checkout() ) return;
    if ( ! get_option( 'pfwc_enabled', 1 ) ) return;

    wp_enqueue_style( 'pfwc-style', PFWC_URL . 'assets/popup.css', [], '1.0.0' );
    wp_enqueue_script( 'pfwc-script', PFWC_URL . 'assets/popup.js', [], '1.0.0', true );

    wp_localize_script( 'pfwc-script', 'pfwcData', [
        'duration' => (int) get_option( 'pfwc_duration', 7 ),
    ]);
});

add_action( 'wp_footer', function() {
    if ( ! function_exists( 'is_checkout' ) || ! is_checkout() ) return;
    if ( ! get_option( 'pfwc_enabled', 1 ) ) return;

    $title    = get_option( 'pfwc_title',   '⚠️ ঈদের ছুটির কারণে কুরিয়ার সার্ভিস বন্ধ ⚠️' );
    $text     = get_option( 'pfwc_text',    '' );
    $note     = get_option( 'pfwc_note',    '' );
    $lines    = array_filter( array_map( 'trim', explode( "\n", $text ) ) );
    ?>
    <div id="pfwc-overlay">
        <div id="pfwc-popup">
            <div id="pfwc-close">✖</div>
            <div id="pfwc-title"><?php echo esc_html( $title ); ?></div>
            <div id="pfwc-divider"></div>
            <div id="pfwc-text">
                <?php echo implode( '<br>', array_map( 'esc_html', $lines ) ); ?>
            </div>
            <?php if ( $note ) : ?>
            <div id="pfwc-note"><?php echo esc_html( $note ); ?></div>
            <?php endif; ?>
            <div id="pfwc-timer-bar"><span id="pfwc-timer-fill"></span></div>
        </div>
    </div>
    <?php
});
