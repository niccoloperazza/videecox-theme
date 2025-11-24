<?php

/**
 * Page Loader Settings
 * Adds customizer option to enable minimal page transition loader
 */

// Add page loader section to WordPress Customizer
add_action('customize_register', 'videecox_page_loader_customizer');

function videecox_page_loader_customizer($wp_customize) {
    // Add section
    $wp_customize->add_section('videecox_page_loader', array(
        'title'       => __('Page Loader', 'videecox'),
        'description' => __('Attiva un loader minimale durante le transizioni tra pagine.', 'videecox'),
        'priority'    => 35,
    ));

    // Enable page loader
    $wp_customize->add_setting('videecox_enable_page_loader', array(
        'default'           => false,
        'sanitize_callback' => 'wp_validate_boolean',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('videecox_enable_page_loader', array(
        'label'       => __('Abilita Page Loader', 'videecox'),
        'description' => __('Mostra un loader minimale durante il caricamento delle pagine', 'videecox'),
        'section'     => 'videecox_page_loader',
        'type'        => 'checkbox',
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
        'description' => __('Scegli il colore del loader', 'videecox'),
        'section'     => 'videecox_page_loader',
        'priority'    => 20,
    )));
}

// Render page loader in footer
add_action('wp_footer', 'videecox_render_page_loader');

function videecox_render_page_loader() {
    $enable_loader = get_theme_mod('videecox_enable_page_loader', false);

    if (!$enable_loader) {
        return;
    }

    $loader_color = get_theme_mod('videecox_loader_color', '#2c3e50');

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
            border-top-color: <?php echo esc_attr($loader_color); ?>;
            border-radius: 50%;
            animation: page-loader-spin 0.8s linear infinite;
        }

        @keyframes page-loader-spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* Hide loader on initial page load to avoid flash */
        body.page-loading .page-loader {
            opacity: 0;
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

            // Function to show loader
            function showLoader() {
                if (loader && !isNavigating) {
                    isNavigating = true;
                    loader.classList.add('active');
                }
            }

            // Function to hide loader
            function hideLoader() {
                if (loader) {
                    isNavigating = false;
                    loader.classList.remove('active');
                }
            }

            // Show loader on link clicks (internal links only)
            document.addEventListener('click', function(e) {
                const link = e.target.closest('a');

                if (link && link.href) {
                    const url = new URL(link.href, window.location.href);
                    const currentUrl = new URL(window.location.href);

                    // Only show loader for internal links (same origin)
                    if (url.origin === currentUrl.origin &&
                        !link.hasAttribute('target') &&
                        !link.getAttribute('href').startsWith('#') &&
                        !link.getAttribute('href').startsWith('mailto:') &&
                        !link.getAttribute('href').startsWith('tel:') &&
                        !link.classList.contains('no-loader')) {

                        showLoader();

                        // Fallback to hide loader after 5 seconds
                        setTimeout(hideLoader, 5000);
                    }
                }
            });

            // Hide loader when page loads
            window.addEventListener('load', hideLoader);

            // Hide loader on page show (for back/forward navigation)
            window.addEventListener('pageshow', function(event) {
                // If page is loaded from cache
                if (event.persisted) {
                    hideLoader();
                }
            });

            // Hide loader if navigation is cancelled
            window.addEventListener('beforeunload', function() {
                // Timeout to check if navigation actually happened
                setTimeout(function() {
                    if (document.visibilityState === 'visible') {
                        hideLoader();
                    }
                }, 100);
            });

            // Hide loader on initial page load
            hideLoader();
        })();
    </script>
    <?php
}
