<?php

function adds_logged_out_class($classes) {
    if (! ( is_user_logged_in() ) ) {
        $classes[] = 'logged-out';
    }
    return $classes;
}
add_filter('body_class','adds_logged_out_class');