<?php

function acf_update_avatar($user_id)
{
    $avatar = get_field('user_avatar', 'user_' . $user_id); // Get the uploaded avatar image URL from ACF field

    if ($avatar) {
        $upload_dir = wp_upload_dir(); // Get the WordPress upload directory info

        // Replace Gravatar with the uploaded avatar
        update_user_meta($user_id, 'wp_user_avatar', $upload_dir['baseurl'] . $avatar);
    }
}
add_action('acf/save_post', 'acf_update_avatar', 20);


function custom_user_avatar($avatar, $user_id, $size, $default, $alt)
{
    $user_avatar = get_user_meta($user_id, 'user_avatar', true);

    if ($user_avatar) {
        $avatar = '<img src="' . wp_get_attachment_url($user_avatar) . '" alt="' . esc_attr($alt) . '" class="avatar avatar-' . $size . '" width="' . $size . '" height="' . $size . '" />';
    }

    return $avatar;
}
add_filter('get_avatar', 'custom_user_avatar', 10, 5);
