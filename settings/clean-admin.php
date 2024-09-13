<?php

// hide some elements to non admins 
function hide_elements() {
    if (!current_user_can('administrator')) {
        echo '<style>
            #toplevel_page_jet-engine, 
            #adminmenu > li.wp-not-current-submenu.wp-menu-separator.separator-croco.separator-croco--plugins-before,
            li#toplevel_page_mailchimp-woocommerce,
            li#toplevel_page_wpseo_workouts,
            li#menu-tools {
                display: none !important;
            }
        </style>';
    }
}
add_action('admin_head', 'hide_elements');