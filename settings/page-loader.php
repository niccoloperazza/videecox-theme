<?php

/**
 * Page Loader Settings
 * Adds customizer option for page transition loaders (minimal or skeleton)
 */

// Add page loader section to WordPress Customizer
add_action('customize_register', 'videecox_page_loader_customizer');

function videecox_page_loader_customizer($wp_customize) {
    // Add section
    $wp_customize->add_section('videecox_page_loader', array(
        'title'       => __('Page Loader', 'videecox'),
        'description' => __('Scegli il tipo di loader da mostrare durante le transizioni tra pagine.', 'videecox'),
        'priority'    => 35,
    ));

    // Loader type selection
    $wp_customize->add_setting('videecox_loader_type', array(
        'default'           => 'disabled',
        'sanitize_callback' => 'videecox_sanitize_loader_type',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('videecox_loader_type', array(
        'label'       => __('Tipo Loader', 'videecox'),
        'description' => __('Scegli tra loader minimale (spinner) o skeleton loading', 'videecox'),
        'section'     => 'videecox_page_loader',
        'type'        => 'select',
        'choices'     => array(
            'disabled' => __('Disabilitato', 'videecox'),
            'minimal'  => __('Loader Minimale (Spinner)', 'videecox'),
            'skeleton' => __('Skeleton Loading', 'videecox'),
        ),
        'priority'    => 10,
    ));

    // Loader color
    $wp_customize->add_setting('videecox_loader_color', array(
        'default'           => '#2c3e50',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'videecox_loader_color', array(
        'label'       => __('Colore Loader', 'videecox'),
        'description' => __('Colore per il loader (spinner o skeleton)', 'videecox'),
        'section'     => 'videecox_page_loader',
        'priority'    => 20,
    )));
}

// Sanitize loader type
function videecox_sanitize_loader_type($input) {
    $valid = array('disabled', 'minimal', 'skeleton');
    return in_array($input, $valid, true) ? $input : 'disabled';
}

// Render page loader in footer
add_action('wp_footer', 'videecox_render_page_loader');

function videecox_render_page_loader() {
    $loader_type = get_theme_mod('videecox_loader_type', 'disabled');

    if ($loader_type === 'disabled') {
        return;
    }

    $loader_color = get_theme_mod('videecox_loader_color', '#2c3e50');

    if ($loader_type === 'minimal') {
        videecox_render_minimal_loader($loader_color);
    } elseif ($loader_type === 'skeleton') {
        videecox_render_skeleton_loader($loader_color);
    }
}

// Minimal spinner loader
function videecox_render_minimal_loader($color) {
    ?>
    <style>
        .page-loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 255, 255, 0.95);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 99999;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease, visibility 0.3s ease;
            pointer-events: none;
        }

        .page-loader.active {
            opacity: 1;
            visibility: visible;
        }

        .page-loader-spinner {
            width: 40px;
            height: 40px;
            border: 3px solid rgba(0, 0, 0, 0.1);
            border-top-color: <?php echo esc_attr($color); ?>;
            border-radius: 50%;
            animation: page-loader-spin 0.8s linear infinite;
        }

        @keyframes page-loader-spin {
            to {
                transform: rotate(360deg);
            }
        }
    </style>

    <div class="page-loader">
        <div class="page-loader-spinner"></div>
    </div>

    <script>
        (function() {
            'use strict';

            const loader = document.querySelector('.page-loader');
            let isNavigating = false;

            function showLoader() {
                if (loader && !isNavigating) {
                    isNavigating = true;
                    loader.classList.add('active');
                }
            }

            function hideLoader() {
                if (loader) {
                    isNavigating = false;
                    loader.classList.remove('active');
                }
            }

            document.addEventListener('click', function(e) {
                const link = e.target.closest('a');

                if (link && link.href) {
                    const url = new URL(link.href, window.location.href);
                    const currentUrl = new URL(window.location.href);

                    if (url.origin === currentUrl.origin &&
                        !link.hasAttribute('target') &&
                        !link.getAttribute('href').startsWith('#') &&
                        !link.getAttribute('href').startsWith('mailto:') &&
                        !link.getAttribute('href').startsWith('tel:') &&
                        !link.classList.contains('no-loader')) {

                        showLoader();
                        setTimeout(hideLoader, 5000);
                    }
                }
            });

            window.addEventListener('load', hideLoader);
            window.addEventListener('pageshow', function(event) {
                if (event.persisted) {
                    hideLoader();
                }
            });

            hideLoader();
        })();
    </script>
    <?php
}

// Skeleton loader
function videecox_render_skeleton_loader($color) {
    // Convert hex to rgb for opacity
    $rgb = sscanf($color, "#%02x%02x%02x");
    $skeleton_color = "rgba({$rgb[0]}, {$rgb[1]}, {$rgb[2]}, 0.1)";
    $skeleton_shimmer = "rgba({$rgb[0]}, {$rgb[1]}, {$rgb[2]}, 0.2)";
    ?>
    <style>
        /* Skeleton Loading Styles */
        .skeleton-loading article,
        .skeleton-loading .entry-content > *,
        .skeleton-loading h1,
        .skeleton-loading h2,
        .skeleton-loading h3,
        .skeleton-loading p,
        .skeleton-loading img {
            position: relative;
            overflow: hidden;
            background-color: <?php echo esc_attr($skeleton_color); ?> !important;
            color: transparent !important;
            border-radius: 4px;
            pointer-events: none;
        }

        .skeleton-loading article::before,
        .skeleton-loading .entry-content > *::before,
        .skeleton-loading h1::before,
        .skeleton-loading h2::before,
        .skeleton-loading h3::before,
        .skeleton-loading p::before,
        .skeleton-loading img::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(
                90deg,
                transparent,
                <?php echo esc_attr($skeleton_shimmer); ?>,
                transparent
            );
            animation: skeleton-shimmer 1.5s infinite;
        }

        @keyframes skeleton-shimmer {
            0% {
                left: -100%;
            }
            100% {
                left: 100%;
            }
        }

        /* Hide specific elements during skeleton loading */
        .skeleton-loading a,
        .skeleton-loading button,
        .skeleton-loading input,
        .skeleton-loading select,
        .skeleton-loading textarea {
            opacity: 0.3;
            pointer-events: none;
        }

        /* Smooth transition out of skeleton state */
        article,
        .entry-content > *,
        h1, h2, h3, p, img {
            transition: background-color 0.3s ease, color 0.3s ease;
        }
    </style>

    <script>
        (function() {
            'use strict';

            let isNavigating = false;

            function showSkeleton() {
                if (!isNavigating) {
                    isNavigating = true;
                    document.body.classList.add('skeleton-loading');
                }
            }

            function hideSkeleton() {
                isNavigating = false;
                document.body.classList.remove('skeleton-loading');
            }

            document.addEventListener('click', function(e) {
                const link = e.target.closest('a');

                if (link && link.href) {
                    const url = new URL(link.href, window.location.href);
                    const currentUrl = new URL(window.location.href);

                    if (url.origin === currentUrl.origin &&
                        !link.hasAttribute('target') &&
                        !link.getAttribute('href').startsWith('#') &&
                        !link.getAttribute('href').startsWith('mailto:') &&
                        !link.getAttribute('href').startsWith('tel:') &&
                        !link.classList.contains('no-loader')) {

                        showSkeleton();
                        setTimeout(hideSkeleton, 5000);
                    }
                }
            });

            window.addEventListener('load', hideSkeleton);
            window.addEventListener('pageshow', function(event) {
                if (event.persisted) {
                    hideSkeleton();
                }
            });

            hideSkeleton();
        })();
    </script>
    <?php
}
