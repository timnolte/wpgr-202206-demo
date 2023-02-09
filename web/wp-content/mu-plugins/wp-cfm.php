<?php
/**
 * Plugin Name: WP-CFM Customization
 * Description: Provides customization for how the WP-CFM plugin functions on the site.
 *
 * @package  ForumOne_MuPlugins
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Use YAML for WP-CFM config files.
 *
 * @param  string $format - Default is 'json' format.
 *
 * @SuppressWarnings(PHPMD.UnusedFormalParameter) $format  does not need to be checked or used in this case.
 *
 * @return string
 */
function f1_use_yaml_config_format( $format ) {

	return 'yaml'; // Value can be 'yaml' or 'yml'.

}
add_filter( 'wpcfm_config_format', 'f1_use_yaml_config_format' );

/**
 * Change the default path for the bundle files on local environments when working with a Pantheon site.
 *
 * @param string $config_path The filesystem path or URL for the configuration.
 *
 * @return string
 */
function f1_change_config_path( $config_path ) {

	// Check for Pantheon configuration and configure WP-CFM configuration paths for Pantheon.
	$pantheon_config = dirname( __FILE__ ) . '/../../../pantheon.yml';

	if ( is_readable( $pantheon_config ) ) {
		$path = explode( '/', $config_path );

		// Check for Pantheon Environment and use predefined configuration.
		if ( defined( 'PANTHEON_ENVIRONMENT' ) && ! empty( $_ENV['PANTHEON_ENVIRONMENT'] ) ) {
			return implode( '/', $path );
		}

		// For local development these need to be set, to properly resolve the path in the `private` directory.
		if ( filter_var( $config_path, FILTER_VALIDATE_URL ) ) {
			return '/private/config/' . $path[ count( $path ) - 1 ];
		}

		return dirname( dirname( __DIR__ ) ) . '/private/config/' . $path[ count( $path ) - 1 ];
	}

	return $config_path;

}
add_filter( 'wpcfm_config_dir', 'f1_change_config_path' );
add_filter( 'wpcfm_config_url', 'f1_change_config_path' );

/**
 * Defines environments.
 *
 * @param array $environments - Default is an empty array [] meaning multi environment config is disabled.
 *
 * @return array
 */
function f1_enable_multi_env( $environments ) {

	// Check for Pantheon Environment variable and use that if present.
	if ( defined( 'PANTHEON_ENVIRONMENT' ) && ! empty( $_ENV['PANTHEON_ENVIRONMENT'] ) ) {
		return $environments;
	}

	// Define standard Pantheon environments on a Pantheon site.
	$pantheon_config = dirname( __FILE__ ) . '/../../../pantheon.yml';
	if ( is_readable( $pantheon_config ) ) {
		// Define an array containing the hosting environment names.
		// Or detect these with your own code logic if all are available in `$_ENV` or `$_SERVER` super-globals.
		return array(
			'dev',
			'test',
			'live',
		);
	}

	return array(
		'dev',
		'stage',
		'prod',
	);

}
add_filter( 'wpcfm_multi_env', 'f1_enable_multi_env' );

/**
 * Determines the current WordPress environment.
 *
 * @return string
 */
function f1_get_current_environment() {

	// Check for Pantheon Environment variable and use that if present.
	if ( defined( 'PANTHEON_ENVIRONMENT' ) && ! empty( $_ENV['PANTHEON_ENVIRONMENT'] ) ) {
		return $_ENV['PANTHEON_ENVIRONMENT'];
	}

	// Check for .env environment.
	if ( defined( 'WORDPRESS_ENV' ) ) {
		return WORDPRESS_ENV;
	}

	// Check for WordPress Core environment.
	// Note: we don't use `wp_get_environment_type()` as the default is 'production' when the constant isn't set.
	if ( defined( 'WP_ENVIRONMENT_TYPE' ) ) {
		return WP_ENVIRONMENT_TYPE;
	}

	// Default to a Development environment.
	return 'development';

}

/**
 * Sets the current WP-CFM environment based on a database naming convention
 * or the Pantheon environment.
 *
 * @param string $env Default is an empty string ''.
 *
 * @return string
 */
function f1_set_current_env( $env ) {

	// Detect with your own code logic the current environment the WordPress site is running.
	// Generally this will be defined in a constant inside `$_ENV` or `$_SERVER` super-globals.
	$config_env = f1_get_current_environment();
	$dev_env = 'dev';
	$stage_env = 'stage';
	$prod_env = 'prod';

	// Allow changing to Test & Live configs in local development.
	$pantheon_config = dirname( __FILE__ ) . '/../../../pantheon.yml';
	if ( ( defined( 'PANTHEON_ENVIRONMENT' ) && ! empty( $_ENV['PANTHEON_ENVIRONMENT'] ) ) || is_readable( $pantheon_config ) ) {
		$stage_env = 'test';
		$prod_env = 'live';
	}

	// This is just an initialized "default" fallback set in the event the WP_PRODUCTION_ENVS is not defined.
	$production_aliases = array(
		'production',
		'live',
	);
	if ( defined( 'WP_PRODUCTION_ENVS' ) ) {
		$production_aliases = explode( ',', WP_PRODUCTION_ENVS );
	}

	// This is just an initialized "default" fallback set in the event the WP_STAGING_ENVS is not defined.
	$staging_aliases = array(
		'staging',
		'test',
	);
	if ( defined( 'WP_STAGING_ENVS' ) ) {
		$staging_aliases = explode( ',', WP_STAGING_ENVS );
	}

	switch ( true ) {
		case ( in_array( $config_env, $production_aliases, true ) ):
			$env = $prod_env;
			break;
		case ( in_array( $config_env, $staging_aliases, true ) ):
			$env = $stage_env;
			break;
		default:
			$env = $dev_env;
	}

	return $env;

}
add_filter( 'wpcfm_current_env', 'f1_set_current_env' );
