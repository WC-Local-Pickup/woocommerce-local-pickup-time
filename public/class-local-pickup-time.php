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

use phpDocumentor\Reflection\Types\Integer;

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
	const VERSION = '1.4.0';

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
	 * @var      Local_Pickup_Time
	 */
	protected static $instance = null;

	/**
	 * Configured WordPress Date Format. Default: 'Y/m/d'.
	 *
	 * @since     1.3.2
	 *
	 * @var       string
	 */
	protected $date_format = 'Y/m/d';

	/**
	 * Configured WordPress Time Format. Default: 'g:i:s a'.
	 *
	 * @since     1.3.2
	 *
	 * @var       string
	 */
	protected $time_format = 'g:i:s a';

	/**
	 * Configured WordPress GMT offset. Default: 0.
	 *
	 * @since     1.3.2
	 *
	 * @var       integer
	 */
	protected $gmt_offset = 0;

	/**
	 * Configured WordPress timezone string. Default: 'America/New_York'.
	 *
	 * @since     1.3.2
	 *
	 * @var       string
	 */
	protected $timezone = 'America/New_York';

	/**
	 * Configured WordPress timezone as a DateTimeZone object. Default: null.
	 *
	 * @since     1.3.12
	 *
	 * @var       DateTimeZone
	 */
	protected $wp_timezone = null;

	/**
	 * Order meta key for storing Local Pickup Time.
	 *
	 * @since     1.3.2
	 *
	 * @var       string
	 */
	protected $order_meta_key = '_local_pickup_time_select';

	/**
	 * Initialize the plugin by setting localization and loading public scripts
	 * and styles.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {

		// Load WordPress date/time formats.
		$this->date_format = get_option( 'date_format', $this->date_format );
		$this->time_format = get_option( 'time_format', $this->time_format );
		$this->gmt_offset  = get_option( 'gmt_offset', $this->gmt_offset );
		$this->timezone    = get_option( 'timezone_string', $this->timezone );

		// Make sure we have a time zone set.
		if ( empty( $this->timezone ) ) {

			$tz_name = timezone_name_from_abbr( '', $this->get_gmt_offset() * 3600, 1 ) ? timezone_name_from_abbr( '', $this->get_gmt_offset() * 3600, 1 ) : timezone_name_from_abbr( '', $this->get_gmt_offset() * 3600, 0 );
			$this->timezone = ( ! empty( $tz_name ) ) ? (string) $tz_name : $this->timezone;

		}

		// Create our own wp_timezone for backwards compatibility.
		$this->wp_timezone = new DateTimeZone( $this->get_timezone() );

		// Load plugin text domain.
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		// Activate plugin when new blog is added.
		add_action( 'wpmu_new_blog', array( $this, 'activate_new_site' ) );

		// Add the local pickup time field to the checkout page.
		$public_hooked_location = apply_filters( 'local_pickup_time_select_location', 'woocommerce_after_order_notes' );
		add_action( $public_hooked_location, array( $this, 'time_select' ) );

		// Process the checkout.
		add_action( 'woocommerce_checkout_process', array( $this, 'field_process' ) );

		// Update the order meta with local pickup time field value.
		add_action( 'woocommerce_checkout_update_order_meta', array( $this, 'update_order_meta' ) );

		// Add local pickup time field to order emails.
		add_filter( 'woocommerce_email_order_meta_fields', array( $this, 'update_order_email_fields' ), 10, 3 );

	}

	/**
	 * Return the plugin slug.
	 *
	 * @since    1.0.0
	 *
	 * @return string  The Local_Pickup_Time slug variable.
	 */
	public function get_plugin_slug() {

		return $this->plugin_slug;

	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    Local_Pickup_Time    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Return the plugin date format value.
	 *
	 * @since     1.3.2
	 *
	 * @return string   The date format string.
	 */
	public function get_date_format() {

		return $this->date_format;

	}

	/**
	 * Return the plugin time format value.
	 *
	 * @since     1.3.2
	 *
	 * @return string   The time format string.
	 */
	public function get_time_format() {

		return $this->time_format;

	}

	/**
	 * Return the plugin GMT offset value.
	 *
	 * @since     1.3.2
	 *
	 * @return int   The GMT offset number.
	 */
	public function get_gmt_offset() {

		return $this->gmt_offset;

	}

	/**
	 * Return the plugin timezone value.
	 *
	 * @since     1.3.2
	 *
	 * @return string   The timezone string.
	 */
	public function get_timezone() {

		return $this->timezone;

	}

	/**
	 * Return the plugin timezone as a DateTimeZone object..
	 *
	 * @since     1.3.12
	 *
	 * @return DateTimeZone   The timezone object.
	 */
	public function get_wp_timezone() {

		return $this->wp_timezone;

	}

	/**
	 * Return the plugin order meta key used for storing the Local Pickup Time.
	 *
	 * @since     1.3.2
	 *
	 * @return string   The order meta_key that stores the Local Pickup Time.
	 */
	public function get_order_meta_key() {

		return $this->order_meta_key;

	}

	/**
	 * Fired when the plugin is activated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean $network_wide    True if WPMU superadmin uses
	 *                                    "Network Activate" action, false if
	 *                                    WPMU is disabled or plugin is
	 *                                    activated on an individual blog.
	 *
	 * @return void
	 */
	public static function activate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide ) {

				// Get all blog ids.
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
	 * @param    boolean $network_wide    True if WPMU superadmin uses
	 *                                    "Network Deactivate" action, false if
	 *                                    WPMU is disabled or plugin is
	 *                                    deactivated on an individual blog.
	 *
	 * @return void
	 */
	public static function deactivate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide ) {

				// Get all blog ids.
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
	 * @param    int $blog_id    ID of the new blog.
	 *
	 * @return void
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
	 * @return   array<int>    The blog ids, false if no matches.
	 */
	private static function get_blog_ids() {

		global $wpdb;

		// Get an array of blog ids.
		return $wpdb->get_col(
			"SELECT blog_id FROM $wpdb->blogs
			WHERE archived = '0' AND spam = '0'
			AND deleted = '0'"
		);

	}

	/**
	 * Fired for each blog when the plugin is activated.
	 *
	 * @since    1.0.0
	 *
	 * @return void
	 */
	private static function single_activate() {

		// Set, or update, the WP option to track database versions for the plugin.
		// update_option( 'wlpt_db_version', self::VERSION, TRUE );.
	}

	/**
	 * Fired for each blog when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 *
	 * @return void
	 */
	private static function single_deactivate() {
		// No deactivation functionality needed... yet.
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 *
	 * @return void
	 */
	public function load_plugin_textdomain() {

		$domain = $this->plugin_slug;
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, false, basename( plugin_dir_path( dirname( __FILE__ ) ) ) . '/languages/' );

	}

	/**
	 * Perform a version check of the plugin against the last activated version.
	 *
	 * @since    1.4.0
	 *
	 * @return boolean   Returns TRUE if the plugin and database versions match, otherwise FALSE if the values don't match.
	 */
	public function plugin_version_check() {

		return version_compare( self::VERSION, get_option( 'wlpt_db_version' ), '>=' );

	}

	/**
	 * Load list of full pickup times.
	 *
	 * @since     1.4.0
	 *
	 * @param integer $max_interval_orders The maximum number of orders allowed per interval.
	 *
	 * @return array<integer, string> The list of pickup times that are closed for selection.
	 */
	public function get_full_pickup_times( $max_interval_orders = 0 ) {

		$full_pickup_times = array();

		// The order statuses to use to limit the orders queryied for pickup times.
		$limit_order_statuses = array(
			'pending',
			'processing',
			'on-hold',
		);

		// Get the current WordPress-based date/time.
		$current_wp_datetime  = new DateTime( 'now', $this->get_wp_timezone() );
		$current_wp_timestamp = $current_wp_datetime->getTimestamp();

		if ( 0 === $max_interval_orders ) {

			return $full_pickup_times;

		}

		// Get all the open order IDs with a pickup time greater than right now.
		$args = array(
			'post_type'      => 'shop_order',
			'post_status'    => $limit_order_statuses,
			'limit'          => -1,
			'meta_key'       => $this->order_meta_key,
			'meta_value_num' => $current_wp_timestamp,
			'meta_compare'   => '>=',
			'fields'         => 'ids',
		);
		$order_qry = new WP_Query( $args );

		if ( $order_qry->have_posts() ) {

			/**
			 * An array of order IDs.
			 *
			 * @var int[] $orders
			 */
			$orders = $order_qry->get_posts();

			foreach ( $orders as $order_id ) {
				$full_pickup_times[] = get_post_meta( $order_id, $this->order_meta_key, true );
			}

			// $pickup_times_counts = array_count_values( $pickup_times );
			arsort( $full_pickup_times );

		}

		return $full_pickup_times;

	}

	/**
	 * Build pickup time options for checkout.
	 *
	 * @since     1.3.2
	 *
	 * @return array<string> The pickup time options.
	 */
	public function get_pickup_time_options() {

		// Get dates closed from settings and explode into an array.
		$dates_closed = preg_replace( '/\v(?:[\v\h]+)/', "\n", trim( get_option( 'local_pickup_hours_closings', '' ) ) );
		$dates_closed = ( ! empty( $dates_closed ) ) ? $dates_closed : '';
		$dates_closed = explode( "\n", $dates_closed );

		// Get delay, interval, and number of days ahead settings.
		$delay_minutes       = get_option( 'local_pickup_delay_minutes', 60 );
		$minutes_interval    = get_option( 'local_pickup_hours_interval', 30 );
		$max_interval_orders = get_option( 'local_pickup_interval_orders_max', 0 );
		$num_days_ahead      = get_option( 'local_pickup_days_ahead', 1 );

		// Translateble days.
		__( 'Monday', 'woocommerce-local-pickup-time' );
		__( 'Tuesday', 'woocommerce-local-pickup-time' );
		__( 'Wednesday', 'woocommerce-local-pickup-time' );
		__( 'Thursday', 'woocommerce-local-pickup-time' );
		__( 'Friday', 'woocommerce-local-pickup-time' );
		__( 'Saturday', 'woocommerce-local-pickup-time' );
		__( 'Sunday', 'woocommerce-local-pickup-time' );

		// Get the current WordPress-based date/time.
		$current_wp_datetime  = new DateTime( 'now', $this->get_wp_timezone() );
		$current_wp_timestamp = $current_wp_datetime->getTimestamp();
		// Initialize DateTime objects for further calculations.
		$current_datetime = new DateTime( "@$current_wp_timestamp" );
		// Ensure we set the timezone so that the final time is correct.
		$current_datetime->setTimezone( $this->get_wp_timezone() );
		$pickup_datetime  = new DateTime( "@$current_wp_timestamp" );
		// Ensure the timezone is set so that the final time is correct.
		$pickup_datetime->setTimezone( $this->get_wp_timezone() );
		// Get to the start of the hour.
		$pickup_datetime->setTimestamp( (int) floor( $pickup_datetime->getTimestamp() / 3600 ) * 3600 );
		// Make sure we start at the next interval past the current time.
		while ( $pickup_datetime->getTimestamp() <= $current_datetime->getTimestamp() ) {
			// Adjust to next interval past the current time.
			$pickup_datetime->modify( "+$minutes_interval minute" );
		}

		// Adjust for time delay.
		$pickup_datetime->modify( "+$delay_minutes minute" );

		// Setup options array with empty first item.
		$pickup_options[''] = __( 'Select time', 'woocommerce-local-pickup-time' );

		// Initialize firt interval state.
		$first_interval = true;

		// Build options.
		for ( $days = 1; $days <= $num_days_ahead; $days++ ) {

			// Get the day's opening and closing times.
			$pickup_day_name       = strtolower( $pickup_datetime->format( 'l' ) );
			$pickup_day_open_time  = get_option( 'local_pickup_hours_' . $pickup_day_name . '_start', '' );
			$pickup_day_close_time = get_option( 'local_pickup_hours_' . $pickup_day_name . '_end', '' );

			if (
				! in_array( $pickup_datetime->format( 'm/d/Y' ), $dates_closed, true ) &&
				! empty( $pickup_day_open_time ) &&
				! empty( $pickup_day_close_time )
			) {

				// Get the intervals for the day and merge the results with the previous array of intervals.
				$pickup_options = array_replace(
					$pickup_options,
					$this->get_pickup_time_intervals(
						$pickup_datetime->getTimestamp(),
						$minutes_interval,
						$pickup_day_open_time,
						$pickup_day_close_time,
						$first_interval
					)
				);

			} else {

				// Rollback the days counter to ensure the number of days ahead reflect number of open days.
				$days = ( $days < 1 ) ? 0 : $days - 1;

			}

			// Clear first interval state.
			$first_interval = false;

			// Advance to the next day.
			$pickup_datetime->modify( '+1 day' );

			// Reset the interval starting time.
			// Note: PHP pre-7.1 doesn't support milliseconds with the setTime() call.
			if ( ! defined( 'PHP_VERSION' ) || ! function_exists( 'version_compare' ) || version_compare( PHP_VERSION, '7.1.0', '<' ) ) {
				$pickup_datetime->setTime( 0, 0, 0 );
			} else {
				$pickup_datetime->setTime( 0, 0, 0, 0 );
			}
		}

		return $pickup_options;

	}

	/**
	 * Build pickup time day intervals.
	 *
	 * @since     1.3.2
	 *
	 * @param integer $pickup_timestamp   A starting pickup timestamp.
	 * @param integer $minutes_interval   A number of minutes to use for an interval.
	 * @param string  $pickup_day_open_time   The open time for the pickup timestamp day.
	 * @param string  $pickup_day_close_time    The close time for the pickup timestamp day.
	 * @param boolean $first_interval   A flag to disable the time delay when called as the first interval.
	 *
	 * @return array<string> An array of pickup times for a given day.
	 */
	public function get_pickup_time_intervals( $pickup_timestamp, $minutes_interval, $pickup_day_open_time, $pickup_day_close_time, $first_interval = false ) {

		// Initialize starting DateTime.
		$pickup_start_datetime = new DateTime( "@$pickup_timestamp" );
		$pickup_start_datetime->setTimezone( $this->get_wp_timezone() );

		// Initialize opening DateTime.
		$pickup_open_datetime = new DateTime( "@$pickup_timestamp" );
		$pickup_open_datetime->setTimezone( $this->get_wp_timezone() );

		// Set the open DateTime to the configured open time.
		$pickup_day_hours_time_array = explode( ':', $pickup_day_open_time );
		// Note: PHP pre-7.1 doesn't support milliseconds with the setTime() call.
		if ( ! defined( 'PHP_VERSION' ) || ! function_exists( 'version_compare' ) || version_compare( PHP_VERSION, '7.1.0', '<' ) ) {
			$pickup_open_datetime->setTime( (int) $pickup_day_hours_time_array[0], (int) $pickup_day_hours_time_array[1], 0 );
		} else {
			$pickup_open_datetime->setTime( (int) $pickup_day_hours_time_array[0], (int) $pickup_day_hours_time_array[1], 0, 0 );
		}

		// Initialize ending DateTime based on day closed time.
		$pickup_end_datetime = new DateTime( "@$pickup_timestamp" );
		$pickup_end_datetime->setTimezone( $this->get_wp_timezone() );
		// Set ending hour based on close time.
		$pickup_day_hours_time_array = explode( ':', $pickup_day_close_time );

		// Note: PHP pre-7.1 doesn't support milliseconds with the setTime() call.
		if ( ! defined( 'PHP_VERSION' ) || ! function_exists( 'version_compare' ) || version_compare( PHP_VERSION, '7.1.0', '<' ) ) {
			$pickup_end_datetime->setTime( (int) $pickup_day_hours_time_array[0], (int) $pickup_day_hours_time_array[1], 0 );
		} else {
			$pickup_end_datetime->setTime( (int) $pickup_day_hours_time_array[0], (int) $pickup_day_hours_time_array[1], 0, 0 );
		}
		// Advance to 1 interval past the close time so that close time is inclusive.
		$pickup_end_datetime->modify( "+$minutes_interval minute" );

		// Initialize a pickup time period object for traversing through the day intervals.
		$pickup_dateperiod = ( $pickup_start_datetime->getTimestamp() >= $pickup_open_datetime->getTimestamp() )
			? new DatePeriod( $pickup_start_datetime, ( new DateInterval( 'PT' . $minutes_interval . 'M' ) ), $pickup_end_datetime )
			: new DatePeriod( $pickup_open_datetime, ( new DateInterval( 'PT' . $minutes_interval . 'M' ) ), $pickup_end_datetime );

		foreach ( $pickup_dateperiod as $pickup_datetime ) {

			$pickup_day_options[ "{$pickup_datetime->getTimestamp()}" ] = $this->pickup_time_select_translatable( $pickup_datetime->getTimestamp(), ' @ ' );

		}

		return ! empty( $pickup_day_options ) ? $pickup_day_options : array(); // Return an empty array if there were now DatePeriod iterations.

	}

	/**
	 * Add the local pickup time field to the checkout page
	 *
	 * @since    1.0.0
	 *
	 * @param WC_Checkout $checkout The checkout object.
	 *
	 * @return void
	 */
	public function time_select( $checkout ) {
		echo '<div id="local-pickup-time-select"><h2>' . __( 'Pickup Time', 'woocommerce-local-pickup-time' ) . '</h2>';

		woocommerce_form_field(
			'local_pickup_time_select',
			array(
				'type'     => 'select',
				'class'    => array( 'local-pickup-time-select-field form-row-wide' ),
				'label'    => __( 'Pickup Time', 'woocommerce-local-pickup-time' ),
				'required' => true,
				'options'  => $this->get_pickup_time_options(),
			),
			$checkout->get_value( 'local_pickup_time_select' )
		);

		echo '</div>';
	}

	/**
	 * Process the checkout
	 *
	 * @since    1.3.0
	 *
	 * @return void
	 */
	public function field_process() {
		global $woocommerce;

		// Check if set, if its not set add an error.
		if ( ! $_POST['local_pickup_time_select'] ) {
			wc_add_notice( __( 'Please select a pickup time.', 'woocommerce-local-pickup-time' ), 'error' );
		}

	}

	/**
	 * Update the order meta with local pickup time field value
	 *
	 * @since    1.0.0
	 *
	 * @param integer $order_id The ID of the order you want meta data for.
	 *
	 * @return void
	 */
	public function update_order_meta( $order_id ) {
		if ( $_POST['local_pickup_time_select'] ) {
			update_post_meta( $order_id, $this->get_order_meta_key(), esc_attr( $_POST['local_pickup_time_select'] ) );
		}
	}

	/**
	 * Add local pickup time fields to order emails, since the previous function has been deprecated
	 *
	 * @since    1.3.0
	 *
	 * @param array<array> $fields The array of pickup time fields.
	 * @param boolean      $sent_to_admin Flag that indicates whether the email is being sent to an admin user or not.
	 * @param WC_Order     $order The order object that holds all the order attributes.
	 * @return array<array>    The array of order email fields including the pickup time field.
	 */
	public function update_order_email_fields( $fields, $sent_to_admin, $order ) {

		$value              = $this->pickup_time_select_translatable( get_post_meta( $order->get_id(), $this->get_order_meta_key(), true ) );
		$fields['meta_key'] = array(
			'label' => __( 'Pickup Time', 'woocommerce-local-pickup-time' ),
			'value' => $value,
		);

		return $fields;
	}

	/**
	 * Return translatable pickup time
	 *
	 * @since    1.3.0
	 *
	 * @param string $value         The pickup time meta value for an order.
	 * @param string $separator     The separator to use between the date & the time. (Default = ' ').
	 * @return string  The translated value of the order pickup time.
	 */
	public function pickup_time_select_translatable( $value, $separator = ' ' ) {

		// Only attempt date/time adjustments when a value is set.
		if ( empty( $value ) ) {
			return __( 'None', 'woocommerce-local-pickup-time' );
		}

		// This match is specifically to address the bug introduced in 1.3.1.
		if ( preg_match( '/^\d{2}\/\d{2}\/\d{4}\d{1,2}_\d{2}\_[amp]{2}$/', $value ) ) {

			$datetime = DateTime::createFromFormat( 'm/d/Y' . preg_replace( '/[^\w]+/', '_', $this->get_time_format() ), $value );
			$value = ( ! empty( $datetime ) ) ? (string) $datetime->getTimestamp() : $value;

		}

		// When using the latest pickup time meta of a timestamp return using the WordPress i18n method.
		if ( preg_match( '/^\d*$/', $value ) ) {
			if ( function_exists( 'wp_date' ) ) {
				return wp_date( $this->get_date_format(), (int) $value, $this->get_wp_timezone() ) . $separator . wp_date( $this->get_time_format(), (int) $value, $this->get_wp_timezone() );
			} else {
				return date_i18n( $this->get_date_format(), (int) $value + $this->get_gmt_offset() ) . $separator . date_i18n( $this->get_time_format(), (int) $value + $this->get_gmt_offset() );
			}
		}

		return $value;

	}

}
