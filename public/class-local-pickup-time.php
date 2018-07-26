<?php
/**
 * Local Pickup Time
 *
 * @package   Local_Pickup_Time
 * @author    Matt Banks <mjbanks@gmail.com>
 * @license   GPL-2.0+
 * @link      http://mattbanks.me
 * @copyright 2014-2017 Matt Banks
 */

/**
 * Local_Pickup_Time class.
 * Defines public-facing functionality
 *
 * @package Local_Pickup_Time
 * @author  Matt Banks <mjbanks@gmail.com>
 */
class Local_Pickup_Time {

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since   1.0.0
	 *
	 * @var     string
	 */
	const VERSION = '1.3.1';

	/**
	 * Unique identifier for plugin.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_slug = 'woocommerce-local-pickup-time';

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
		$public_hooked_location = apply_filters( 'local_pickup_time_select_location', 'woocommerce_after_order_notes' );
		add_action( $public_hooked_location, array( $this, 'time_select' ) );

		// Process the checkout
		add_action( 'woocommerce_checkout_process', array( $this, 'field_process' ) );

		// Update the order meta with local pickup time field value
		add_action( 'woocommerce_checkout_update_order_meta', array( $this, 'update_order_meta' ) );

		// Add local pickup time field to order emails
		add_filter('woocommerce_email_order_meta_fields', array( $this, 'update_order_email_fields' ), 10, 3 );


	}

	/**
	 * Return the plugin slug.
	 *
	 * @since    1.0.0
	 *
	 * @return    Local_Pickup_Time slug variable.
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
		// No activation functionality needed... yet
	}

	/**
	 * Fired for each blog when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 */
	private static function single_deactivate() {
		// No deactivation functionality needed... yet
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
		load_plugin_textdomain( $domain, FALSE, basename( plugin_dir_path( dirname( __FILE__ ) ) ) . '/languages/' );

	}

	/**
	 * Create an array of times starting with an hour past the current time
	 *
	 * @since    1.0.0
	 */
	public function create_hour_options() {
		// Make sure we have a time zone set
		$offset = get_option( 'gmt_offset' );
		$timezone_setting = get_option( 'timezone_string' );

		if ( $timezone_setting ) {
			date_default_timezone_set( get_option( 'timezone_string', 'America/New_York' ) );
		}
		else {
			$timezone = timezone_name_from_abbr( null, $offset * 3600, true );
			if( $timezone === false ) $timezone = timezone_name_from_abbr( null, $offset * 3600, false );
			date_default_timezone_set( $timezone );
		}

		// Get days closed textarea from settings, explode into an array
		$closing_days_raw = trim( get_option( 'local_pickup_hours_closings' ) );
		$closing_days = explode( "\n", preg_replace('/\v(?:[\v\h]+)/', "\n", $closing_days_raw ) );

		// Get delay, interval, and number of days ahead settings
		$delay_minutes = get_option( 'local_pickup_delay_minutes', 60 );
		$interval = get_option( 'local_pickup_hours_interval', 30 );
		$num_days_allowed = get_option( 'local_pickup_days_ahead', 1 );

		// Setup time variables for calculations
		$today_name = strtolower( date( 'l' ) );
		$today_date = date( 'm/d/Y' );

		// Create an empty array for our dates
		$pickup_options = array();

		//Translateble days
		__( 'Monday', $this->plugin_slug, 'woocommerce-local-pickup-time' );
		__( 'Tuesday', $this->plugin_slug, 'woocommerce-local-pickup-time' );
		__( 'Wednesday', $this->plugin_slug, 'woocommerce-local-pickup-time' );
		__( 'Thursday', $this->plugin_slug, 'woocommerce-local-pickup-time' );
		__( 'Friday', $this->plugin_slug, 'woocommerce-local-pickup-time' );
		__( 'Saturday', $this->plugin_slug, 'woocommerce-local-pickup-time' );
		__( 'Sunday', $this->plugin_slug, 'woocommerce-local-pickup-time' );

		// Add empty option
		$pickup_options[''] = __( 'Select time', $this->plugin_slug, 'woocommerce-local-pickup-time' );

		// Loop through all days ahead and add the pickup time options to the array
		for ( $i = 0; $i < $num_days_allowed; $i++ ) {

			// Get the date of current iteration
			$current_date = date( 'm/d/Y', strtotime( "+$i days" ) );
			$current_date_fmt = date( 'l, ' . get_option( 'date_format' ), strtotime( "+$i days" ) );
			$current_day_name = date( 'l', strtotime( "+$i days" ) );
			$current_day_name_lower = strtolower( $current_day_name );

			// Get the day's opening and closing times
			$open_time = get_option( 'local_pickup_hours_' . $current_day_name_lower . '_start', '10:00' );
			$close_time = get_option( 'local_pickup_hours_' . $current_day_name_lower . '_end', '19:00' );

			// Today
			$tStart = strtotime( "$current_date $open_time" );
			$tEnd = strtotime( "$current_date $close_time" );
			$tNow = $tStart;
			$current_time = time();

			// Date format based on user settings
			$date_format = get_option('time_format');
			$date_format_key = preg_replace("/[^\w]+/", "_", $date_format);

			// If Closed today or today's pickup times are over, don't allow a pickup option
			if ( ( in_array( $today_date, $closing_days ) || ( $current_time >= $tEnd ) )  && $num_days_allowed == 1 ) {

				// Set drop down text to let user know store is closed
				$pickup_options['closed_today'] = __( 'Closed today, please check back tomorrow!', $this->plugin_slug, 'woocommerce-local-pickup-time' );

				// Hide Order Review so user doesn't order anything today
				remove_action( 'woocommerce_checkout_order_review', 'woocommerce_order_review', 10 );
				remove_action( 'woocommerce_checkout_order_review', 'woocommerce_checkout_payment', 20 );

      }
			elseif ( in_array( $current_date, $closing_days ) ) {
				continue;
			}
			else {
				// Create array of time options to return to woocommerce_form_field

				// Today
				if ( $i == 0) {

					// Check if it's not too late for pickup
					if ( $current_time < $tEnd ) {

						// Fix tNow if is pickup possible today
						$todayStart = $tStart;
						$delayStart = strtotime("+$delay_minutes minutes", $current_time);
						while ( $todayStart <= $delayStart ) {
							$todayStart = strtotime("+$interval minutes", $todayStart);
						}
						$tNow = $todayStart;

						while ( $tNow <= $tEnd ) {

							$day_name = __( 'Today', $this->plugin_slug, 'woocommerce-local-pickup-time' );

							$option_key = $current_date . date( $date_format_key, $tNow );
							$option_value = $day_name . ' @ ' . date( $date_format, $tNow );

							$pickup_options[$option_key] = $option_value;

							$tNow = strtotime( "+$interval minutes", $tNow );
						}

					}

				// Other days
				} else {

					$delayStart = strtotime( "+$delay_minutes minutes" );
					if ( !empty($open_time) && !empty($close_time )) {

						while ( $tNow <= $tEnd ) {
              
							if ( $tNow > $delayStart ) {
								$day_name = __( $current_date_fmt, $this->plugin_slug, 'woocommerce-local-pickup-time' );

								$option_key = $current_date . date( $date_format_key, $tNow );
								$option_value = $day_name . ' @ ' . date( $date_format, $tNow );

								$pickup_options[$option_key] = $option_value;

							}

							$tNow = strtotime( "+$interval minutes", $tNow );

						}

					}
				}

			}

		} // end for loop

		if ( count($pickup_options) == 1) {
			// Set drop down text to let user know store is closed
			$pickup_options['closed_today'] = __( 'Closed today, please check back tomorrow!', $this->plugin_slug, 'woocommerce-local-pickup-time' );

			// Hide Order Review so user doesn't order anything today
			remove_action( 'woocommerce_checkout_order_review', 'woocommerce_order_review', 10 );
			remove_action( 'woocommerce_checkout_order_review', 'woocommerce_checkout_payment', 20 );
		}

		return $pickup_options;
	}

	/**
	 * Add the local pickup time field to the checkout page
	 *
	 * @since    1.0.0
	 */
	public function time_select( $checkout ) {
		echo '<div id="local-pickup-time-select"><h2>' . __( 'Pickup Time', $this->plugin_slug, 'woocommerce-local-pickup-time' ) . '</h2>';

		woocommerce_form_field( 'local_pickup_time_select', array(
			'type'          => 'select',
			'class'         => array( 'local-pickup-time-select-field form-row-wide' ),
			'label'         => __( 'Pickup Time', $this->plugin_slug, 'woocommerce-local-pickup-time' ),
			'required'		=> true,
			'options'		=> self::create_hour_options()
		), $checkout->get_value( 'local_pickup_time_select' ));

		self::create_hour_options();

		echo '</div>';
	}

	/**
	 * Process the checkout
	 *
	 * @since    1.3.0
	 */
	public function field_process() {
		global $woocommerce;

		// Check if set, if its not set add an error.
 		if (!$_POST['local_pickup_time_select']) wc_add_notice(__( 'Please select a pickup time.', $this->plugin_slug, 'woocommerce-local-pickup-time' ), 'error');

	}

	/**
	 * Update the order meta with local pickup time field value
	 *
	 * @since    1.0.0
	 */
	public function update_order_meta( $order_id ) {
		if ( $_POST['local_pickup_time_select'] ) update_post_meta( $order_id, '_local_pickup_time_select', esc_attr( $_POST['local_pickup_time_select']) );
	}

	/**
	 * Add local pickup time fields to order emails, since the previous function has been deprecated
	 *
	 * @since    1.3.0
	 */
	public function update_order_email_fields ( $fields, $sent_to_admin, $order ) {

		$value = $this->pickup_time_select_translatable( get_post_meta( $order->id, '_local_pickup_time_select', true ));
		$fields['meta_key'] = array(
				'label' => __('Pickup Time', $this->plugin_slug, 'woocommerce-local-pickup-time'),
				'value' => $value);

		return $fields;
	}

	/**
	 * Return translatable pickup time
	 *
	 * @since    1.3.0
	 */
	public function pickup_time_select_translatable( $value ) {
		$value = preg_replace('/(\d)_(\d)/','$1:$2', $value);
		$value = explode('_', $value);
		$return = __( $value[0], $this->plugin_slug, 'woocommerce-local-pickup-time' ). ' ' .$value[1];
		return $return;
	}


}
