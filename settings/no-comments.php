<?php


// Disable comments globally
function disable_comments_globally() {
    // Remove comments from post types
    $post_types = get_post_types();
    foreach ($post_types as $post_type) {
        if (post_type_supports($post_type, 'comments')) {
            remove_post_type_support($post_type, 'comments');
            remove_post_type_support($post_type, 'trackbacks');
        }
    }
 
    // Close comments on the front-end
    add_filter('comments_open', '__return_false', 20, 2);
    add_filter('pings_open', '__return_false', 20, 2);
 
    // Hide existing comments
    add_filter('comments_array', '__return_empty_array', 10, 2);
 
    // Remove comments page in admin menu
    add_action('admin_menu', 'disable_comments_admin_menu');
 
    // Redirect any comments page requests
    add_action('admin_init', 'disable_comments_admin_redirect');
}
add_action('init', 'disable_comments_globally');

// Hide comments menu from admin menu
function disable_comments_admin_menu() {
    remove_menu_page('edit-comments.php');
}

// Redirect comments page to dashboard
function disable_comments_admin_redirect() {
    global $pagenow;
    if ($pagenow === 'edit-comments.php') {
        wp_redirect(admin_url());
        exit;
    }
}

// Hide comments metabox from post editor
function disable_comments_metaboxes() {
    remove_meta_box('commentstatusdiv', 'post', 'normal');
    remove_meta_box('commentsdiv', 'post', 'normal');
}
add_action('admin_menu', 'disable_comments_metaboxes');

// Hide comments from admin bar
function disable_comments_admin_bar() {
    if (is_admin_bar_showing()) {
        remove_action('admin_bar_menu', 'wp_admin_bar_comments_menu', 60);
    }
}
add_action('init', 'disable_comments_admin_bar');
