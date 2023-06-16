<?php
/**
 * Videeco X Theme functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package videecox
 */

add_action( 'wp_enqueue_scripts', 'astra_parent_theme_enqueue_styles' );

/**
 * Enqueue scripts and styles.
 */
function astra_parent_theme_enqueue_styles() {
	wp_enqueue_style( 'astra-style', get_template_directory_uri() . '/style.css' );
	wp_enqueue_style( 'videecox-style',
		get_stylesheet_directory_uri() . '/style.css',
		array( 'astra-style' )
	);

}



/**
 * Default allow svg.
 */
function codeless_file_types_to_uploads($file_types){
		$new_filetypes = array();
		$new_filetypes['svg'] = 'image/svg+xml';
		$file_types = array_merge($file_types, $new_filetypes );
		return $file_types;
	}
add_filter('upload_mimes', 'codeless_file_types_to_uploads');
	

/**
 * Colore della barra degli indirizzi 
 */
add_action('wp_head', 'barra_indirizzi');
function barra_indirizzi(){
	?>
		<meta name="theme-color" content="#000000" />
	<?php
};