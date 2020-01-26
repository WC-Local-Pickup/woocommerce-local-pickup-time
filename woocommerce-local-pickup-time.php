<?php
/**
 * WooCommerce Local Pickup Time Select
 *
 * Plugin to add an an option to WooCommerce checkout pages for Local Pickup only
 * that allows the user to choose a pickup time. Time choices begin 1 hour from order time
 * rounded to closest hour or half hour and go in 30 minute intervals until store closing time.
 *
 * @package   WooCommerce Local Pickup Time Select
 * @author    Matt Banks <mjbanks@gmail.com>
 * @license   GPL-2.0+
 * @link      http://mattbanks.me
 * @copyright 2014-2018 Matt Banks
 *
 * @wordpress-plugin
 * Plugin Name:       WooCommerce Local Pickup Time Select
 * Plugin URI:        http://mattbanks.me
 * Description:       Add an an option to WooCommerce checkout pages for Local Pickup that allows the user to choose a pickup time.
 * Version:           1.3.12
 * Author:            Matt Banks
 * Author URI:        http://mattbanks.me
 * Text Domain:       woocommerce-local-pickup-time
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 * GitHub Plugin URI: https://github.com/mattbanks/woocommerce-local-pickup-time
 */

/**
 * If this file is called directly, abort.
 **/
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * Check if WooCommerce is active
 */
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ), true ) ) {

	/**
	 * ----------------------------------------------------------------------------
	 * Public-Facing Functionality
	 * ----------------------------------------------------------------------------
	 */

	/**
	 * Require public facing functionality
	 */
	require_once( plugin_dir_path( __FILE__ ) . 'public/class-local-pickup-time.php' );

	/**
	 * Register hooks that are fired when the plugin is activated or deactivated.
	 * When the plugin is deleted, the uninstall.php file is loaded.
	 */
	register_activation_hook( __FILE__, array( 'Local_Pickup_Time', 'activate' ) );
	register_deactivation_hook( __FILE__, array( 'Local_Pickup_Time', 'deactivate' ) );

	/**
	 * Get instance
	 */
	add_action( 'plugins_loaded', array( 'Local_Pickup_Time', 'get_instance' ) );

	/**
	 * ----------------------------------------------------------------------------
	 * Dashboard and Administrative Functionality
	 * ----------------------------------------------------------------------------
	 */

	/**
	 * Require admin functionality
	 */
	if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {

		require_once( plugin_dir_path( __FILE__ ) . 'admin/class-local-pickup-time-admin.php' );
		add_action( 'plugins_loaded', array( 'Local_Pickup_Time_Admin', 'get_instance' ) );

	}
}
