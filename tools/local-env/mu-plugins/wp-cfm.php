<?php
/**
 * Plugin Name: WP-CFM Customization
 * Description: Provides customization for how the WP-CFM plugin functions on the site.
 *
 * @package  OpenID_Connect_Generic_MuPlugins
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
