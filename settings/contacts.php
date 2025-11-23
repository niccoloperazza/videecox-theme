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
        'description' => __('Inserisci i dati di contatto.<br><br><strong>Utilizzo Shortcode:</strong><br>• [contacts] - Mostra tutti i campi<br>• [contacts field="telefono"] - Solo Telefono<br>• [contacts field="email"] - Solo Email<br>• [contacts field="sede_operativa"] - Solo Sede Operativa<br>• [contacts field="sede_operativa" map="true"] - Sede con mappa OpenStreetMap<br>• [contacts map="true"] - Tutti i campi con mappa<br>• [contacts label="false"] - Nascondi etichette', 'videecox'),
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

    // Latitudine
    $wp_customize->add_setting('videecox_sede_lat', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('videecox_sede_lat', array(
        'label'       => __('Latitudine Sede', 'videecox'),
        'description' => __('Inserisci la latitudine (es: 45.4642)', 'videecox'),
        'section'     => 'videecox_contacts',
        'type'        => 'text',
        'priority'    => 40,
    ));

    // Longitudine
    $wp_customize->add_setting('videecox_sede_lng', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('videecox_sede_lng', array(
        'label'       => __('Longitudine Sede', 'videecox'),
        'description' => __('Inserisci la longitudine (es: 9.1900)', 'videecox'),
        'section'     => 'videecox_contacts',
        'type'        => 'text',
        'priority'    => 50,
    ));
}

// Enqueue Leaflet.js for OpenStreetMap
function videecox_enqueue_leaflet() {
    if (has_shortcode(get_post()->post_content, 'contacts')) {
        wp_enqueue_style('leaflet-css', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css', array(), '1.9.4');
        wp_enqueue_script('leaflet-js', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js', array(), '1.9.4', true);
    }
}
add_action('wp_enqueue_scripts', 'videecox_enqueue_leaflet');

// Shortcode to display contacts
add_shortcode('contacts', 'videecox_contacts_shortcode');

function videecox_contacts_shortcode($atts) {
    // Parse attributes
    $atts = shortcode_atts(array(
        'field'  => 'all',   // all, telefono, email, sede_operativa
        'label'  => 'true',  // show labels
        'map'    => 'false', // show OpenStreetMap
        'zoom'   => '15',    // map zoom level
        'height' => '400px', // map height
    ), $atts);

    $telefono = get_theme_mod('videecox_telefono', '');
    $email = get_theme_mod('videecox_email', '');
    $sede_operativa = get_theme_mod('videecox_sede_operativa', '');
    $sede_lat = get_theme_mod('videecox_sede_lat', '');
    $sede_lng = get_theme_mod('videecox_sede_lng', '');

    $show_label = ($atts['label'] === 'true');
    $show_map = ($atts['map'] === 'true');
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

                // Add map if coordinates are available and map is enabled
                if ($show_map && !empty($sede_lat) && !empty($sede_lng)) {
                    $output .= videecox_render_osm_map($sede_lat, $sede_lng, $atts['zoom'], $atts['height']);
                }
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

                // Add map if coordinates are available and map is enabled
                if ($show_map && !empty($sede_lat) && !empty($sede_lng)) {
                    $output .= videecox_render_osm_map($sede_lat, $sede_lng, $atts['zoom'], $atts['height']);
                }
            }
            break;
    }

    return $output;
}

// Helper function to render OpenStreetMap
function videecox_render_osm_map($lat, $lng, $zoom = 15, $height = '400px') {
    static $map_counter = 0;
    $map_counter++;
    $map_id = 'osm-map-' . $map_counter;

    $lat = floatval($lat);
    $lng = floatval($lng);
    $zoom = intval($zoom);

    $output = '<style>
        a.leaflet-control-zoom-out, a.leaflet-control-zoom-in {
            text-decoration: none !important;
        }
    </style>';
    $output .= '<div id="' . esc_attr($map_id) . '" class="osm-map" style="width: 100%; height: ' . esc_attr($height) . '; margin-top: 15px; border-radius: 8px; overflow: hidden;"></div>';
    $output .= '<script>
        document.addEventListener("DOMContentLoaded", function() {
            if (typeof L !== "undefined") {
                var map = L.map("' . esc_js($map_id) . '").setView([' . $lat . ', ' . $lng . '], ' . $zoom . ');

                // Using OpenStreetMap tiles (privacy-friendly, no tracking)
                L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
                    attribution: \'&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors\',
                    maxZoom: 19
                }).addTo(map);

                // Add marker
                L.marker([' . $lat . ', ' . $lng . ']).addTo(map);
            }
        });
    </script>';

    return $output;
}
