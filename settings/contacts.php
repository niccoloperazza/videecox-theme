<?php

/**
 * Contacts Settings
 * Adds customizer options for contact data and shortcode to display them
 */

// Add contacts section to WordPress Customizer
add_action('customize_register', 'videecox_contacts_customizer');

function videecox_contacts_customizer($wp_customize) {
    // Add section
    $wp_customize->add_section('videecox_contacts', array(
        'title'       => __('Contatti', 'videecox'),
        'description' => __('Inserisci i dati di contatto.<br><br><strong>Utilizzo Shortcode:</strong><br>• [contacts] - Mostra tutti i campi<br>• [contacts field="telefono"] - Solo Telefono<br>• [contacts field="email"] - Solo Email<br>• [contacts field="sede_operativa"] - Solo Sede Operativa<br>• [contacts label="false"] - Nascondi etichette', 'videecox'),
        'priority'    => 31,
    ));

    // Telefono
    $wp_customize->add_setting('videecox_telefono', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('videecox_telefono', array(
        'label'       => __('Telefono', 'videecox'),
        'description' => __('Inserisci il numero di telefono', 'videecox'),
        'section'     => 'videecox_contacts',
        'type'        => 'tel',
        'priority'    => 10,
    ));

    // Email
    $wp_customize->add_setting('videecox_email', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_email',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('videecox_email', array(
        'label'       => __('Email', 'videecox'),
        'description' => __('Inserisci l\'indirizzo email', 'videecox'),
        'section'     => 'videecox_contacts',
        'type'        => 'email',
        'priority'    => 20,
    ));

    // Sede Operativa
    $wp_customize->add_setting('videecox_sede_operativa', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_textarea_field',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('videecox_sede_operativa', array(
        'label'       => __('Sede Operativa', 'videecox'),
        'description' => __('Inserisci l\'indirizzo della sede operativa', 'videecox'),
        'section'     => 'videecox_contacts',
        'type'        => 'textarea',
        'priority'    => 30,
    ));
}

// Shortcode to display contacts
add_shortcode('contacts', 'videecox_contacts_shortcode');

function videecox_contacts_shortcode($atts) {
    // Parse attributes
    $atts = shortcode_atts(array(
        'field' => 'all', // all, telefono, email, sede_operativa
        'label' => 'true', // show labels
    ), $atts);

    $telefono = get_theme_mod('videecox_telefono', '');
    $email = get_theme_mod('videecox_email', '');
    $sede_operativa = get_theme_mod('videecox_sede_operativa', '');

    $show_label = ($atts['label'] === 'true');
    $output = '';

    // Build output based on field parameter
    switch ($atts['field']) {
        case 'telefono':
            if (!empty($telefono)) {
                $output = $show_label ? '<strong>Telefono:</strong> ' . esc_html($telefono) : esc_html($telefono);
            }
            break;

        case 'email':
            if (!empty($email)) {
                $email_link = '<a href="mailto:' . esc_attr($email) . '">' . esc_html($email) . '</a>';
                $output = $show_label ? '<strong>Email:</strong> ' . $email_link : $email_link;
            }
            break;

        case 'sede_operativa':
            if (!empty($sede_operativa)) {
                $output = $show_label ? '<strong>Sede Operativa:</strong> ' . nl2br(esc_html($sede_operativa)) : nl2br(esc_html($sede_operativa));
            }
            break;

        case 'all':
        default:
            $items = array();
            if (!empty($telefono)) {
                $items[] = ($show_label ? '<strong>Telefono:</strong> ' : '') . esc_html($telefono);
            }
            if (!empty($email)) {
                $email_link = '<a href="mailto:' . esc_attr($email) . '">' . esc_html($email) . '</a>';
                $items[] = ($show_label ? '<strong>Email:</strong> ' : '') . $email_link;
            }
            if (!empty($sede_operativa)) {
                $items[] = ($show_label ? '<strong>Sede Operativa:</strong> ' : '') . nl2br(esc_html($sede_operativa));
            }
            if (!empty($items)) {
                $output = '<div class="contacts-info">' . implode('<br>', $items) . '</div>';
            }
            break;
    }

    return $output;
}
