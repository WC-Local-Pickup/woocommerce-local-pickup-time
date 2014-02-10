<?php
/**
 * Local Pickup Time.
 *
 * @package   Local_Pickup_Time
 * @author    Matt Banks <mjbanks@gmail.com>
 * @license   GPL-2.0+
 * @link      http://mattbanks.me
 * @copyright 2014 Matt Banks
 */

/**
 * Local_Pickup_Time class.
 * Defines public-facing functionality
 *
 * @package Local_Pickup_Time
 * @author  Your Name <mjbanks@gmail.com>
 */
class Local_Pickup_Time {

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since   1.0.0
	 *
	 * @var     string
	 */
	const VERSION = '1.0.0';

	/**
	 * Unique identifier for plugin.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_slug = 'local-plugin-time';

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Initialize the plugin by setting localization and loading public scripts
	 * and styles.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {

		// Load plugin text domain
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		// Activate plugin when new blog is added
		add_action( 'wpmu_new_blog', array( $this, 'activate_new_site' ) );

		// Add the local pickup time field to the checkout page
		add_action( 'woocommerce_after_order_notes', array( $this, 'local_pickup_time_select' ) );

		// Process the checkout
		add_action( 'woocommerce_checkout_process', array( $this, 'local_pickup_time_field_process' ) );

		// Update the order meta with local pickup time field value
		add_action( 'woocommerce_checkout_update_order_meta', array( $this, 'local_pickup_time_update_order_meta' ) );

	}

	/**
	 * Return the plugin slug.
	 *
	 * @since    1.0.0
	 *
	 * @return    Plugin slug variable.
	 */
	public function get_plugin_slug() {
		return $this->plugin_slug;
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Fired when the plugin is activated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses
	 *                                       "Network Activate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       activated on an individual blog.
	 */
	public static function activate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide  ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_activate();
				}

				restore_current_blog();

			} else {
				self::single_activate();
			}

		} else {
			self::single_activate();
		}

	}

	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses
	 *                                       "Network Deactivate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       deactivated on an individual blog.
	 */
	public static function deactivate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_deactivate();

				}

				restore_current_blog();

			} else {
				self::single_deactivate();
			}

		} else {
			self::single_deactivate();
		}

	}

	/**
	 * Fired when a new site is activated with a WPMU environment.
	 *
	 * @since    1.0.0
	 *
	 * @param    int    $blog_id    ID of the new blog.
	 */
	public function activate_new_site( $blog_id ) {

		if ( 1 !== did_action( 'wpmu_new_blog' ) ) {
			return;
		}

		switch_to_blog( $blog_id );
		self::single_activate();
		restore_current_blog();

	}

	/**
	 * Get all blog ids of blogs in the current network that are:
	 * - not archived
	 * - not spam
	 * - not deleted
	 *
	 * @since    1.0.0
	 *
	 * @return   array|false    The blog ids, false if no matches.
	 */
	private static function get_blog_ids() {

		global $wpdb;

		// get an array of blog ids
		$sql = "SELECT blog_id FROM $wpdb->blogs
			WHERE archived = '0' AND spam = '0'
			AND deleted = '0'";

		return $wpdb->get_col( $sql );

	}

	/**
	 * Fired for each blog when the plugin is activated.
	 *
	 * @since    1.0.0
	 */
	private static function single_activate() {
		// @TODO: Define activation functionality here
	}

	/**
	 * Fired for each blog when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 */
	private static function single_deactivate() {
		// @TODO: Define deactivation functionality here
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		$domain = $this->plugin_slug;
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );

	}

	/**
	 * Create an array of times starting with an hour past the current time
	 *
	 * @since    1.0.0
	 */
	public function local_pickup_create_hour_options() {
		date_default_timezone_set( 'America/New_York' );

		$one_hour_later_hour = date( 'H', strtotime( "+1 hour" ) );
		$one_hour_later_minute = date( 'i', strtotime( "+1 hour" ) );
		$two_hour_later_hour = date( 'H', strtotime( "+2 hours" ) );

		$open_time = "11:00";

		if ( date( 'l' ) === 'Sunday' ) {
			$close_time = "17:00";
		}
		else {
			$close_time = "19:00";
		}

		$pickup_time_begin = ( $one_hour_later_minute < 30 ) ? $one_hour_later_hour . ":30" : $two_hour_later_hour . ":00";

		$start_time = ( strtotime( $pickup_time_begin ) < strtotime( $open_time ) ) ? $open_time : $pickup_time_begin;

		// Today
		$tStart = strtotime( $start_time );
		$tEnd = strtotime( $close_time );
		$tNow = $tStart;

		$pickup_options = '';

		while( $tNow <= $tEnd ){
			$option_key = date( "l_h_i", $tNow );
			$option_value = 'Today ' . date( "g:i", $tNow );

			$pickup_options[$option_value] = $option_value;

			$tNow = strtotime( '+30 minutes', $tNow );
		}

		return $pickup_options;
	}

	/**
	 * Add the local pickup time field to the checkout page
	 *
	 * @since    1.0.0
	 */
	public function local_pickup_time_select( $checkout ) {
		echo '<div id="local-pickup-time-select"><h2>'.__( 'Pickup Time' ).'</h2>';

		woocommerce_form_field( 'local_pickup_time_select', array(
			'type'          => 'select',
			'class'         => array( 'local-pickup-time-select-field form-row-wide' ),
			'label'         => __( 'Pickup Time' ),
			'options'		=> self::local_pickup_create_hour_options()
		), $checkout->get_value( 'local_pickup_time_select' ));

		self::local_pickup_create_hour_options();

		echo '</div>';
	}

	/**
	 * Process the checkout
	 *
	 * @since    1.0.0
	 */
	public function local_pickup_time_field_process() {
		global $woocommerce;

		// Check if set, if its not set add an error.
		if ( !$_POST['local_pickup_time_select'] )
			 $woocommerce->add_error( __('Please select a pickup time.') );
	}

	/**
	 * Update the order meta with local pickup time field value
	 *
	 * @since    1.0.0
	 */
	public function local_pickup_time_update_order_meta( $order_id ) {
		if ( $_POST['local_pickup_time_select'] ) update_post_meta( $order_id, '_local_pickup_time_select', esc_attr($_POST['local_pickup_time_select']) );
	}

}
