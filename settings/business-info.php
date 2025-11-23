<?php

/**
 * Business Information Settings
 * Adds customizer options for business data and shortcode to display them
 */

// Add business info section to WordPress Customizer
add_action('customize_register', 'videecox_business_info_customizer');

function videecox_business_info_customizer($wp_customize) {
    // Add section
    $wp_customize->add_section('videecox_business_info', array(
        'title'       => __('Informazioni Aziendali', 'videecox'),
        'description' => __('Inserisci i dati aziendali.<br><br><strong>Utilizzo Shortcode:</strong><br>• [business_info] - Mostra tutti i campi<br>• [business_info field="partita_iva"] - Solo Partita IVA<br>• [business_info field="codice_fiscale"] - Solo Codice Fiscale<br>• [business_info field="codice_sdi"] - Solo Codice SDI<br>• [business_info field="pec"] - Solo PEC<br>• [business_info field="indirizzo"] - Solo Indirizzo<br>• [business_info label="false"] - Nascondi etichette', 'videecox'),
        'priority'    => 30,
    ));

    // Partita IVA
    $wp_customize->add_setting('videecox_partita_iva', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('videecox_partita_iva', array(
        'label'       => __('Partita IVA', 'videecox'),
        'description' => __('Inserisci la Partita IVA', 'videecox'),
        'section'     => 'videecox_business_info',
        'type'        => 'text',
        'priority'    => 10,
    ));

    // Codice Fiscale
    $wp_customize->add_setting('videecox_codice_fiscale', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('videecox_codice_fiscale', array(
        'label'       => __('Codice Fiscale', 'videecox'),
        'description' => __('Inserisci il Codice Fiscale', 'videecox'),
        'section'     => 'videecox_business_info',
        'type'        => 'text',
        'priority'    => 20,
    ));

    // Codice SDI
    $wp_customize->add_setting('videecox_codice_sdi', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('videecox_codice_sdi', array(
        'label'       => __('Codice SDI', 'videecox'),
        'description' => __('Inserisci il Codice SDI (Sistema di Interscambio)', 'videecox'),
        'section'     => 'videecox_business_info',
        'type'        => 'text',
        'priority'    => 30,
    ));

    // PEC
    $wp_customize->add_setting('videecox_pec', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_email',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('videecox_pec', array(
        'label'       => __('PEC', 'videecox'),
        'description' => __('Inserisci l\'indirizzo PEC', 'videecox'),
        'section'     => 'videecox_business_info',
        'type'        => 'email',
        'priority'    => 40,
    ));

    // Indirizzo
    $wp_customize->add_setting('videecox_indirizzo', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_textarea_field',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('videecox_indirizzo', array(
        'label'       => __('Indirizzo', 'videecox'),
        'description' => __('Inserisci l\'indirizzo completo', 'videecox'),
        'section'     => 'videecox_business_info',
        'type'        => 'textarea',
        'priority'    => 50,
    ));
}

// Shortcode to display business information
add_shortcode('business_info', 'videecox_business_info_shortcode');

function videecox_business_info_shortcode($atts) {
    // Parse attributes
    $atts = shortcode_atts(array(
        'field' => 'all', // all, partita_iva, codice_fiscale, codice_sdi, pec, indirizzo
        'label' => 'true', // show labels
    ), $atts);

    $partita_iva = get_theme_mod('videecox_partita_iva', '');
    $codice_fiscale = get_theme_mod('videecox_codice_fiscale', '');
    $codice_sdi = get_theme_mod('videecox_codice_sdi', '');
    $pec = get_theme_mod('videecox_pec', '');
    $indirizzo = get_theme_mod('videecox_indirizzo', '');

    $show_label = ($atts['label'] === 'true');
    $output = '';

    // Build output based on field parameter
    switch ($atts['field']) {
        case 'partita_iva':
            if (!empty($partita_iva)) {
                $output = $show_label ? '<strong>Partita IVA:</strong> ' . esc_html($partita_iva) : esc_html($partita_iva);
            }
            break;

        case 'codice_fiscale':
            if (!empty($codice_fiscale)) {
                $output = $show_label ? '<strong>Codice Fiscale:</strong> ' . esc_html($codice_fiscale) : esc_html($codice_fiscale);
            }
            break;

        case 'codice_sdi':
            if (!empty($codice_sdi)) {
                $output = $show_label ? '<strong>Codice SDI:</strong> ' . esc_html($codice_sdi) : esc_html($codice_sdi);
            }
            break;

        case 'pec':
            if (!empty($pec)) {
                $output = $show_label ? '<strong>PEC:</strong> ' . esc_html($pec) : esc_html($pec);
            }
            break;

        case 'indirizzo':
            if (!empty($indirizzo)) {
                $output = $show_label ? '<strong>Indirizzo:</strong> ' . nl2br(esc_html($indirizzo)) : nl2br(esc_html($indirizzo));
            }
            break;

        case 'all':
        default:
            $items = array();
            if (!empty($partita_iva)) {
                $items[] = ($show_label ? '<strong>Partita IVA:</strong> ' : '') . esc_html($partita_iva);
            }
            if (!empty($codice_fiscale)) {
                $items[] = ($show_label ? '<strong>Codice Fiscale:</strong> ' : '') . esc_html($codice_fiscale);
            }
            if (!empty($codice_sdi)) {
                $items[] = ($show_label ? '<strong>Codice SDI:</strong> ' : '') . esc_html($codice_sdi);
            }
            if (!empty($pec)) {
                $items[] = ($show_label ? '<strong>PEC:</strong> ' : '') . esc_html($pec);
            }
            if (!empty($indirizzo)) {
                $items[] = ($show_label ? '<strong>Indirizzo:</strong> ' : '') . nl2br(esc_html($indirizzo));
            }
            if (!empty($items)) {
                $output = '<div class="business-info">' . implode('<br>', $items) . '</div>';
            }
            break;
    }

    return $output;
}
