<?php
/**
 * Plugin Name: Google Site Kit Customizations
 * Description: Customizes how the Google Site Kit plugin functions on the site.
 * Author: Forum One
 * Author URI: https://forumone.com
 *
 * @package ForumOne_MuPlugins
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Filters the list of environments to allow testing GA/GTM in non-Production environments.
 */
add_filter(
	'googlesitekit_allowed_tag_environment_types',
	function ( $allowed_environments ) {
		$allowed_environments[] = 'staging';
		return $allowed_environments;
	}
);
