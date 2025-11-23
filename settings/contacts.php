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

    // Mostra icona WhatsApp
    $wp_customize->add_setting('videecox_show_whatsapp', array(
        'default'           => false,
        'sanitize_callback' => 'wp_validate_boolean',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('videecox_show_whatsapp', array(
        'label'       => __('Mostra Icona WhatsApp', 'videecox'),
        'description' => __('Attiva per mostrare un\'icona WhatsApp floating (richiede il numero di telefono)', 'videecox'),
        'section'     => 'videecox_contacts',
        'type'        => 'checkbox',
        'priority'    => 60,
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

// Render floating WhatsApp button
add_action('wp_footer', 'videecox_render_whatsapp_button');

function videecox_render_whatsapp_button() {
    $show_whatsapp = get_theme_mod('videecox_show_whatsapp', false);
    $telefono = get_theme_mod('videecox_telefono', '');

    // Only show if enabled and phone number is set
    if (!$show_whatsapp || empty($telefono)) {
        return;
    }

    // Clean phone number (remove spaces, dashes, etc.)
    $clean_phone = preg_replace('/[^0-9+]/', '', $telefono);

    ?>
    <style>
        .whatsapp-floating {
            position: fixed;
            bottom: 20px;
            left: 20px;
            width: 60px;
            height: 60px;
            background-color: #2c3e50;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 9999;
            cursor: pointer;
            text-decoration: none;
        }

        .whatsapp-floating:hover {
            background-color: #25D366;
            box-shadow: 0 6px 20px rgba(37, 211, 102, 0.4);
            transform: scale(1.1);
        }

        .whatsapp-floating svg {
            width: 32px;
            height: 32px;
            fill: #ffffff;
        }

        @media (max-width: 768px) {
            .whatsapp-floating {
                width: 50px;
                height: 50px;
            }
            .whatsapp-floating svg {
                width: 28px;
                height: 28px;
            }
        }
    </style>

    <a href="https://wa.me/<?php echo esc_attr($clean_phone); ?>"
       class="whatsapp-floating"
       target="_blank"
       rel="noopener noreferrer"
       aria-label="Contattaci su WhatsApp">
        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
        </svg>
    </a>
    <?php
}
