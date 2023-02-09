<?php
/**
 * Plugin Name: Define ACF JSON Location
 * Description: Enforce ACF Settings to be stored in a custom location.
 *
 * @package  ForumOne_MuPlugins
 *
 * @link https://www.advancedcustomfields.com/resources/local-json/
 * @link https://www.advancedcustomfields.com/resources/acf-settings/
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Uses a custom location for acf-json folder out of the theme directory
 * when loading.
 *
 * @param array $path The plugin settings path.
 *
 * @return array
 */
function f1_acf_local_json_dir_load( $path ) {

	// Set custom path in /wp-content/acf-json.
	unset( $path[0] );
	$path[] = WP_CONTENT_DIR . '/acf-json';

	return $path;

}
add_filter( 'acf/settings/load_json', 'f1_acf_local_json_dir_load' );

/**
 * Sets a custom location for acf-json folder out of the theme directory
 * when saving.
 *
 * @param string $path The plugin settings path.
 *
 * @return string
 */
function f1_acf_local_json_dir_save( $path ) {

	// Set custom path in /wp-content/acf-json.
	$path = WP_CONTENT_DIR . '/acf-json';

	return $path;

}
add_filter( 'acf/settings/save_json', 'f1_acf_local_json_dir_save' );
