# Popup For WooCommerce

A lightweight WordPress plugin that displays a fully customizable notice popup on the WooCommerce checkout page. Perfect for announcing delivery schedules, holiday closures, or any important order-related notice — without touching a single line of code.

---

## Why This Plugin?

WooCommerce store owners often need to inform customers about important notices **before** they place an order — such as:

- Eid/holiday delivery delays
- Courier service suspension
- Special delivery schedules by area
- Any urgent order-related announcement

There is no built-in WooCommerce feature for this. This plugin solves that by showing a **modal popup** the moment a customer lands on the checkout page, ensuring the message is seen before the order is confirmed.

---

## Features

- Popup appears automatically on the WooCommerce checkout page
- Fully manageable from the WordPress admin dashboard — no coding needed
- Customizable **title**, **main message**, and **note** text
- Configurable **auto-close timer** (in seconds) with a visual countdown bar
- Manual **close button** (✖) for the user
- Enable / disable the popup with a single checkbox
- Smooth open/close animations
- Responsive design — works on mobile and desktop
- Secure: uses WordPress nonces, data sanitization, and escaping

---

## Requirements

- WordPress 5.0 or higher
- WooCommerce (required — plugin will not activate without it)
- PHP 7.4 or higher

---

## Installation

1. Upload the `popup-for-woocommerce` folder to `/wp-content/plugins/`.
2. Go to **WordPress Admin → Plugins** and activate **Popup For WooCommerce**.
3. Navigate to **Checkout Popup** in the admin sidebar to configure the popup.

---

## How It Works

### Admin Side

When the plugin is activated, a new menu item **"Checkout Popup"** (with a megaphone icon) appears in the WordPress admin sidebar at position 56.

The settings page (`pfwc-settings`) lets you control:

| Setting | Description | Default |
|---|---|---|
| Enable Popup | Toggle the popup on or off | Enabled |
| Title | The bold heading shown at the top of the popup | Eid holiday notice |
| Main Text | The body message (supports multiple lines) | Delivery schedule info |
| Note | A smaller highlighted line shown below the main text | Confirmation reminder |
| Duration | How many seconds before the popup auto-closes (1–60) | 7 seconds |

All settings are saved to the WordPress `wp_options` table using `update_option()` and retrieved with `get_option()`.

### Frontend Side

When a customer visits the **checkout page**:

1. The plugin checks if the popup is enabled.
2. If enabled, it enqueues `popup.css` and `popup.js` from the `assets/` folder.
3. It injects the popup HTML into `wp_footer` — a full-screen dark overlay containing the popup card.
4. The popup card contains:
   - A **close button (✖)** in the top-right corner
   - The **title** in bold red
   - A **divider line**
   - The **main message** (each line break in the admin textarea becomes a `<br>` in the popup)
   - An optional **note** line
   - A **green timer bar** that shrinks from full width to zero over the configured duration
5. After the timer runs out, the popup fades out and is removed from the DOM.
6. The user can also close it early by clicking the ✖ button.

---

## File Structure

```
popup-for-woocommerce/
├── popup-for-woocommerce.php   # Main plugin file — admin menu, settings page, frontend hooks
├── assets/
│   ├── popup.css               # Popup styles — overlay, card, timer bar, animations
│   └── popup.js                # Popup logic — timer countdown, close button, fade-out
└── README.md
```

---

## Technical Details

### popup-for-woocommerce.php

- Defines two constants: `PFWC_DIR` (server path) and `PFWC_URL` (URL path) for asset loading.
- Hooks into `admin_menu` to register the settings page.
- Uses `check_admin_referer()` and `wp_nonce_field()` for CSRF protection on form save.
- Sanitizes all inputs: `sanitize_text_field()`, `sanitize_textarea_field()`, `absint()`.
- Hooks into `wp_enqueue_scripts` to load CSS/JS only on the checkout page.
- Uses `wp_localize_script()` to pass the `duration` value from PHP to JavaScript as `pfwcData.duration`.
- Hooks into `wp_footer` to render the popup HTML, using `esc_html()` on all output.

### popup.css

- The overlay (`#pfwc-overlay`) covers the full viewport with a semi-transparent black background (`rgba(0,0,0,0.6)`) and `z-index: 99999` to sit above all page content.
- The popup card (`#pfwc-popup`) is centered, max 700px wide, with a dashed green border and rounded corners.
- Entry animation (`pfwcIn`) scales the card from 92% to 100% with a fade-in over 0.3s.
- The timer bar (`#pfwc-timer-fill`) uses a CSS `transform: scaleX()` transition to animate the countdown visually.

### popup.js

- Waits for `DOMContentLoaded` before running.
- Reads `pfwcData.duration` (passed from PHP) and converts it to milliseconds.
- Starts the timer bar shrink animation using two nested `requestAnimationFrame` calls to ensure the CSS transition triggers correctly after the element is painted.
- Sets a `setTimeout` to auto-close the popup after the duration.
- The close button clears the timeout and calls `closePopup()` immediately.
- `closePopup()` fades the overlay to opacity 0 over 0.3s, then removes it from the DOM.

---

## Security

- Admin form is protected with a WordPress nonce (`pfwc_nonce`).
- All saved values are sanitized before storing.
- All output in the frontend popup is escaped with `esc_html()`.
- Plugin only runs on the checkout page — no unnecessary script loading on other pages.

---

## Author

**Fardin Ahmed**
- GitHub: [devfardin](https://github.com/devfardin)

---

## License

This plugin is open-source and free to use for any WordPress/WooCommerce store.
