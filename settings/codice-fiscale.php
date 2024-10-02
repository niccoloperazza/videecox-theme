<?php 

// add CF to woocommerce

// Add Codice Fiscale field to the billing section of the WooCommerce checkout page
add_filter('woocommerce_billing_fields', 'add_codice_fiscale_billing_field');

function add_codice_fiscale_billing_field($fields) {
    // Only show the field for users in Italy
    if (WC()->customer->get_shipping_country() === 'IT') {
        $fields['billing_codice_fiscale'] = array(
            'type'        => 'text',
            'label'       => __('Codice Fiscale'),
            'placeholder' => __('Inserisci il tuo Codice Fiscale'),
            'required'    => true,
            'class'       => array('form-row-wide', 'form-row-full'),
            'clear'       => true,
        );
    }
    return $fields;
}

// Validate the Codice Fiscale field
add_action('woocommerce_checkout_process', 'validate_codice_fiscale_billing_field');

function validate_codice_fiscale_billing_field() {
    // Only validate for users in Italy
    if (WC()->customer->get_shipping_country() === 'IT') {
        if (empty($_POST['billing_codice_fiscale'])) {
            wc_add_notice(__('Per favore inserisci il tuo Codice Fiscale.'), 'error');
        } elseif (!preg_match('/^[a-zA-Z0-9]{16}$/', $_POST['billing_codice_fiscale'])) {
            wc_add_notice(__('Il codice fiscale non è corretto.'), 'error');
        }
    }
}



// Save the Codice Fiscale field value
add_action('woocommerce_checkout_update_order_meta', 'save_codice_fiscale_billing_field');

function save_codice_fiscale_billing_field($order_id) {
    if (!empty($_POST['billing_codice_fiscale'])) {
        update_post_meta($order_id, 'billing_codice_fiscale', sanitize_text_field($_POST['billing_codice_fiscale']));
    }
}


// Display Codice Fiscale field value in the billing address section of the admin order details
add_action('woocommerce_admin_order_data_after_billing_address', 'display_codice_fiscale_in_admin_billing', 10, 1);

function display_codice_fiscale_in_admin_billing($order) {
    $codice_fiscale = get_post_meta($order->get_id(), 'billing_codice_fiscale', true);
    if ($codice_fiscale) {
        echo '<p class="form-field form-field-wide"><strong>' . __('Codice Fiscale') . ':</strong><br 7> ' . esc_html($codice_fiscale) . '</p>';
    }
}