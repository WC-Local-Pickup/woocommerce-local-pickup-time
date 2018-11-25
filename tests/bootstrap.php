<?php
/**
 * PHPUnit bootstrap file
 *
 * @package WooCommerce Local Pickup Time Select
 */

/**
 * Setup additional debug options when testing.
 *
 * @param    bool $should_run  Controls whether the debugging setup should run.
 */
function setup_debugging( $should_run = false ) {

	if ( $should_run ) {
		if ( ! defined( 'WP_DEBUG_DISPLAY' ) ) {
			define( 'WP_DEBUG_DISPLAY', false );
		}
		if ( ! defined( 'WP_DEBUG_LOG' ) ) {
			define( 'WP_DEBUG_LOG', true );
		}
	}

}

if ( ! defined( 'WC_LOCAL_PICKUP_TIME_PLUGIN_FILE' ) ) {
	define( 'WC_LOCAL_PICKUP_TIME_PLUGIN_FILE', dirname( dirname( __FILE__ ) ) . '/woocommerce-local-pickup-time.php' );
}

// Check for and load the PSR-4 autoloader, built by Composer.
if ( file_exists( dirname( __DIR__ ) . '/vendor/autoload.php' ) ) {
	require_once( dirname( __DIR__ ) . '/vendor/autoload.php' );
}

$_tests_dir = getenv( 'WP_TESTS_DIR' );
if ( ! $_tests_dir ) {
	$_tests_dir = ( getenv( 'TMPDIR' ) ? getenv( 'TMPDIR' ) : '/tmp' ) . '/wordpress-tests-lib';
}

// Give access to tests_add_filter() function.
require_once $_tests_dir . '/includes/functions.php';

/**
 * Manually load the plugin being tested.
 */
function _manually_load_plugin() {
	require WC_LOCAL_PICKUP_TIME_PLUGIN_FILE;
}
tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

// Start up the WP testing environment.
require $_tests_dir . '/includes/bootstrap.php';
