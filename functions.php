<?php

/**
 * Videeco X Theme functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package videecox
 */


/**
 * Enqueue scripts and styles.
 */
function astra_parent_theme_enqueue_styles()
{
	wp_enqueue_style('astra-style', get_template_directory_uri() . '/style.css');
	wp_enqueue_style(
		'videecox-style',
		get_stylesheet_directory_uri() . '/style.css',
		array('astra-style')
	);
	wp_enqueue_style(
		'vid-classes-css',
		get_stylesheet_directory_uri() . '/vid-classes.css',
		array('astra-style')
	);
}
add_action('wp_enqueue_scripts', 'astra_parent_theme_enqueue_styles');

/**
 * Include settings file
 * comment to disable
 */

include_once 'settings/no-comments.php';
include_once 'settings/svg-allowed.php';
include_once 'settings/address-bar-color.php';
include_once 'settings/avatar-acf.php';
include_once 'settings/helper-classes.php';
include_once 'settings/clean-admin.php';
include_once 'settings/smooth-animations.php';
include_once 'settings/business-info.php';
//include_once 'settings/codice-fiscale.php';
