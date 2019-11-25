<?php
/**
 * Local Pickup Time
 *
 * @package   Local_Pickup_Time_Admin
 * @author    Matt Banks <mjbanks@gmail.com>
 * @license   GPL-2.0+
 * @link      http://mattbanks.me
 * @copyright 2014-2018 Matt Banks
 */

/**
 * Local_Pickup_Time_Admin class
 * Defines administrative functionality
 *
 * @package Local_Pickup_Time_Admin
 * @author  Matt Banks <mjbanks@gmail.com>
 */
class Local_Pickup_Time_Admin {

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Slug of the plugin screen.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_screen_hook_suffix = null;

	/**
	 * Initialize the plugin by loading admin scripts & styles and adding a
	 * settings page and menu.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {

		// Call $plugin_slug from public plugin class.
		$plugin            = Local_Pickup_Time::get_instance();
		$this->plugin_slug = $plugin->get_plugin_slug();
		// Load WordPress date/time formats from public plugin class.
		$this->date_format = $plugin->get_date_format();
		$this->time_format = $plugin->get_time_format();
		$this->gmt_offset  = $plugin->get_gmt_offset();
		$this->timezone    = $plugin->get_timezone();
		// Load Order meta_key defined for plugin.
		$this->order_meta_key = $plugin->get_order_meta_key();

		/*
		 * Show Pickup Time Settings in the WooCommerce -> General Admin Screen
		 */
		add_filter( 'woocommerce_general_settings', array( $this, 'add_hours_and_closings_options' ) );

		/*
		 * Show Pickup Time in the Order Details in the Admin Screen
		 */
		$admin_hooked_location = apply_filters( 'local_pickup_time_admin_location', 'woocommerce_admin_order_data_after_billing_address' );
		add_action( $admin_hooked_location, array( $this, 'show_metabox' ), 10, 1 );

		/*
		 * Show Pickup Time in the Orders List in the Admin Dashboard.
		 */
		add_filter( 'manage_edit-shop_order_columns', array( $this, 'add_orders_list_pickup_date_column_header' ) );
		add_action( 'manage_shop_order_posts_custom_column', array( $this, 'add_orders_list_pickup_date_column_content' ) );
		add_action( 'manage_edit-shop_order_sortable_columns', array( $this, 'add_orders_list_pickup_date_column_sorting' ) );
		add_action( 'pre_get_posts', array( $this, 'filter_orders_list_by_pickup_date' ) );

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
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Show Pickup Time Settings in the WooCommerce -> General Admin Screen
	 *
	 * @since    1.0.0
	 *
	 * @param array $settings The array of WooCommerce General Plugin Settings.
	 */
	public function add_hours_and_closings_options( $settings ) {
		$updated_settings = array();

		$updated_settings[] = array(
			array(
				'title' => __( 'Store Hours and Closings for Local Pickup', 'woocommerce-local-pickup-time' ),
				'type'  => 'title',
				'desc'  => __( 'The following options affect when order pickups begin and end each day, and which days to not allow order pickups.', 'woocommerce-local-pickup-time' ),
				'id'    => 'local_pickup_hours',
			),
			array(
				'title'    => __( 'Monday Pickup Start Time (use 24-hour time)', 'woocommerce-local-pickup-time' ),
				'desc'     => __( 'This sets the pickup start time for Monday. Use 24-hour time format.', 'woocommerce-local-pickup-time' ),
				'id'       => 'local_pickup_hours_monday_start',
				'css'      => 'width:120px;',
				'default'  => '10:00',
				'type'     => 'time',
				'desc_tip' => true,
			),
			array(
				'title'    => __( 'Monday Pickup End Time (use 24-hour time)', 'woocommerce-local-pickup-time' ),
				'desc'     => __( 'This sets the pickup end time for Monday. Use 24-hour time format.', 'woocommerce-local-pickup-time' ),
				'id'       => 'local_pickup_hours_monday_end',
				'css'      => 'width:120px;',
				'default'  => '19:00',
				'type'     => 'time',
				'desc_tip' => true,
			),
			array(
				'title'    => __( 'Tuesday Pickup Start Time (use 24-hour time)', 'woocommerce-local-pickup-time' ),
				'desc'     => __( 'This sets the pickup start time for Tuesday. Use 24-hour time format.', 'woocommerce-local-pickup-time' ),
				'id'       => 'local_pickup_hours_tuesday_start',
				'css'      => 'width:120px;',
				'default'  => '10:00',
				'type'     => 'time',
				'desc_tip' => true,
			),
			array(
				'title'    => __( 'Tuesday Pickup End Time (use 24-hour time)', 'woocommerce-local-pickup-time' ),
				'desc'     => __( 'This sets the pickup end time for Tuesday. Use 24-hour time format.', 'woocommerce-local-pickup-time' ),
				'id'       => 'local_pickup_hours_tuesday_end',
				'css'      => 'width:120px;',
				'default'  => '19:00',
				'type'     => 'time',
				'desc_tip' => true,
			),
			array(
				'title'    => __( 'Wednesday Pickup Start Time (use 24-hour time)', 'woocommerce-local-pickup-time' ),
				'desc'     => __( 'This sets the pickup start time for Wednesday. Use 24-hour time format.', 'woocommerce-local-pickup-time' ),
				'id'       => 'local_pickup_hours_wednesday_start',
				'css'      => 'width:120px;',
				'default'  => '10:00',
				'type'     => 'time',
				'desc_tip' => true,
			),
			array(
				'title'    => __( 'Wednesday Pickup End Time (use 24-hour time)', 'woocommerce-local-pickup-time' ),
				'desc'     => __( 'This sets the pickup end time for Wednesday. Use 24-hour time format.', 'woocommerce-local-pickup-time' ),
				'id'       => 'local_pickup_hours_wednesday_end',
				'css'      => 'width:120px;',
				'default'  => '19:00',
				'type'     => 'time',
				'desc_tip' => true,
			),
			array(
				'title'    => __( 'Thursday Pickup Start Time (use 24-hour time)', 'woocommerce-local-pickup-time' ),
				'desc'     => __( 'This sets the pickup start time for Thursday. Use 24-hour time format.', 'woocommerce-local-pickup-time' ),
				'id'       => 'local_pickup_hours_thursday_start',
				'css'      => 'width:120px;',
				'default'  => '10:00',
				'type'     => 'time',
				'desc_tip' => true,
			),
			array(
				'title'    => __( 'Thursday Pickup End Time (use 24-hour time)', 'woocommerce-local-pickup-time' ),
				'desc'     => __( 'This sets the pickup end time for Thursday. Use 24-hour time format.', 'woocommerce-local-pickup-time' ),
				'id'       => 'local_pickup_hours_thursday_end',
				'css'      => 'width:120px;',
				'default'  => '19:00',
				'type'     => 'time',
				'desc_tip' => true,
			),
			array(
				'title'    => __( 'Friday Pickup Start Time (use 24-hour time)', 'woocommerce-local-pickup-time' ),
				'desc'     => __( 'This sets the pickup start time for Friday. Use 24-hour time format.', 'woocommerce-local-pickup-time' ),
				'id'       => 'local_pickup_hours_friday_start',
				'css'      => 'width:120px;',
				'default'  => '10:00',
				'type'     => 'time',
				'desc_tip' => true,
			),
			array(
				'title'    => __( 'Friday Pickup End Time (use 24-hour time)', 'woocommerce-local-pickup-time' ),
				'desc'     => __( 'This sets the pickup end time for Friday. Use 24-hour time format.', 'woocommerce-local-pickup-time' ),
				'id'       => 'local_pickup_hours_friday_end',
				'css'      => 'width:120px;',
				'default'  => '19:00',
				'type'     => 'time',
				'desc_tip' => true,
			),
			array(
				'title'    => __( 'Saturday Pickup Start Time (use 24-hour time)', 'woocommerce-local-pickup-time' ),
				'desc'     => __( 'This sets the pickup start time for Saturday. Use 24-hour time format.', 'woocommerce-local-pickup-time' ),
				'id'       => 'local_pickup_hours_saturday_start',
				'css'      => 'width:120px;',
				'default'  => '10:00',
				'type'     => 'time',
				'desc_tip' => true,
			),
			array(
				'title'    => __( 'Saturday Pickup End Time (use 24-hour time)', 'woocommerce-local-pickup-time' ),
				'desc'     => __( 'This sets the pickup end time for Saturday. Use 24-hour time format.', 'woocommerce-local-pickup-time' ),
				'id'       => 'local_pickup_hours_saturday_end',
				'css'      => 'width:120px;',
				'default'  => '19:00',
				'type'     => 'time',
				'desc_tip' => true,
			),
			array(
				'title'    => __( 'Sunday Pickup Start Time (use 24-hour time)', 'woocommerce-local-pickup-time' ),
				'desc'     => __( 'This sets the pickup start time for Sunday. Use 24-hour time format.', 'woocommerce-local-pickup-time' ),
				'id'       => 'local_pickup_hours_sunday_start',
				'css'      => 'width:120px;',
				'default'  => '10:00',
				'type'     => 'time',
				'desc_tip' => true,
			),
			array(
				'title'    => __( 'Sunday Pickup End Time (use 24-hour time)', 'woocommerce-local-pickup-time' ),
				'desc'     => __( 'This sets the pickup end time for Sunday. Use 24-hour time format.', 'woocommerce-local-pickup-time' ),
				'id'       => 'local_pickup_hours_sunday_end',
				'css'      => 'width:120px;',
				'default'  => '19:00',
				'type'     => 'time',
				'desc_tip' => true,
			),
			array(
				'title'    => __( 'Store Closing Days (use MM/DD/YYYY format)', 'woocommerce-local-pickup-time' ),
				'desc'     => __( 'This sets the days the store is closed. Enter one date per line, in format MM/DD/YYYY.', 'woocommerce-local-pickup-time' ),
				'id'       => 'local_pickup_hours_closings',
				'css'      => 'width:250px;height:150px;',
				'default'  => '01/01/2014',
				'type'     => 'textarea',
				'desc_tip' => true,
			),
			array(
				'title'    => __( 'Pickup Time Interval', 'woocommerce-local-pickup-time' ),
				'desc'     => __( 'Choose the time interval for allowing local pickup orders.', 'woocommerce-local-pickup-time' ),
				'id'       => 'local_pickup_hours_interval',
				'css'      => 'width:100px;',
				'default'  => '30',
				'type'     => 'select',
				'class'    => 'chosen_select',
				'desc_tip' => true,
				'options'  => array(
					'5'   => __( '5 minutes', 'woocommerce-local-pickup-time' ),
					'10'  => __( '10 minutes', 'woocommerce-local-pickup-time' ),
					'15'  => __( '15 minutes', 'woocommerce-local-pickup-time' ),
					'20'  => __( '20 minutes', 'woocommerce-local-pickup-time' ),
					'30'  => __( '30 minutes', 'woocommerce-local-pickup-time' ),
					'45'  => __( '45 minutes', 'woocommerce-local-pickup-time' ),
					'60'  => __( '1 hour', 'woocommerce-local-pickup-time' ),
					'120' => __( '2 hours', 'woocommerce-local-pickup-time' ),
				),
			),
			array(
				'title'    => __( 'Pickup Time Delay', 'woocommerce-local-pickup-time' ),
				'desc'     => __( 'Choose the time delay from the time of ordering for allowing local pickup orders.', 'woocommerce-local-pickup-time' ),
				'id'       => 'local_pickup_delay_minutes',
				'css'      => 'width:100px;',
				'default'  => '60',
				'type'     => 'select',
				'class'    => 'chosen_select',
				'desc_tip' => true,
				'options'  => array(
					'5'     => __( '5 minutes', 'woocommerce-local-pickup-time' ),
					'10'    => __( '10 minutes', 'woocommerce-local-pickup-time' ),
					'15'    => __( '15 minutes', 'woocommerce-local-pickup-time' ),
					'20'    => __( '20 minutes', 'woocommerce-local-pickup-time' ),
					'30'    => __( '30 minutes', 'woocommerce-local-pickup-time' ),
					'45'    => __( '45 minutes', 'woocommerce-local-pickup-time' ),
					'60'    => __( '1 hour', 'woocommerce-local-pickup-time' ),
					'120'   => __( '2 hours', 'woocommerce-local-pickup-time' ),
					'240'   => __( '4 hours', 'woocommerce-local-pickup-time' ),
					'480'   => __( '8 hours', 'woocommerce-local-pickup-time' ),
					'960'   => __( '16 hours', 'woocommerce-local-pickup-time' ),
					'1440'  => __( '24 hours', 'woocommerce-local-pickup-time' ),
					'2160'  => __( '36 hours', 'woocommerce-local-pickup-time' ),
					'2880'  => __( '48 hours', 'woocommerce-local-pickup-time' ),
					'4320'  => __( '3 days', 'woocommerce-local-pickup-time' ),
					'7200'  => __( '5 days', 'woocommerce-local-pickup-time' ),
					'10080' => __( '1 week', 'woocommerce-local-pickup-time' ),
				),
			),
			array(
				'title'       => __( 'Pickup Time Open Days Ahead', 'woocommerce-local-pickup-time' ),
				'desc'        => __( 'Choose the number of open days ahead for allowing local pickup orders. This is inclusive of the current day, if timeslots are still available.', 'woocommerce-local-pickup-time' ),
				'id'          => 'local_pickup_days_ahead',
				'css'         => 'width:100px;',
				'default'     => '1',
				'type'        => 'number',
				'input_attrs' => array(
					'min'  => 0,
					'step' => 1,
				),
				'desc_tip'    => true,
			),
			array(
				'type' => 'sectionend',
				'id'   => 'pricing_options',
			),
		);

		$merge = array();

		foreach ( $updated_settings as $new_setting ) {
			$merge = array_merge( $settings, $new_setting );
		}

		return $merge;
	}

	/**
	 * Show Pickup Time in the Order Details in the Admin Screen
	 *
	 * @since    1.0.0
	 *
	 * @param object $order  The order object.
	 */
	public function show_metabox( $order ) {
		$order_meta = get_post_custom( $order->get_id() );

		echo '<p><strong>' . __( 'Pickup Time:', 'woocommerce-local-pickup-time' ) . '</strong> ' . $this->pickup_time_select_translatable( $order_meta[ $this->order_meta_key ][0] ) . '</p>';

	}

	/**
	 * Show Pickup Time on Orders List in Admin Dashboard.
	 *
	 * @since           1.3.2
	 *
	 * @param   array $columns    The Orders List columns array.
	 * @return  array   $new_columns    The updated Orders List columns array.
	 */
	public function add_orders_list_pickup_date_column_header( $columns ) {

		$new_columns = array();

		foreach ( $columns as $column_name => $column_info ) {

			$new_columns[ $column_name ] = $column_info;

			if ( 'order_date' === $column_name ) {
				$new_columns[ $this->order_meta_key ] = __( 'Pickup Time', 'woocommerce-local-pickup-time' );
			}
		}

		return $new_columns;

	}

	/**
	 * Show Pickup Time content on the Orders List in the Admin Dashboard.
	 *
	 * @since     1.3.2
	 *
	 * @param string $column  The column name in the Orders List.
	 */
	public function add_orders_list_pickup_date_column_content( $column ) {

		global $the_order;

		if ( $this->order_meta_key === $column ) {
			echo $this->pickup_time_select_translatable( $the_order->get_meta( $this->order_meta_key ) );
		}

	}

	/**
	 * Allow the Pickup Time columns to be sortable on the Orders List in the Admin Dashboard.
	 *
	 * @since     1.3.2
	 *
	 * @param array $columns  The array of Order columns.
	 * @return  array The updated array Order columns.
	 */
	public function add_orders_list_pickup_date_column_sorting( $columns ) {

		$new_columns                          = array();
		$new_columns[ $this->order_meta_key ] = 'pickup_time';

		return wp_parse_args( $new_columns, $columns );

	}

	/**
	 * Adds Local Pickup Time sorting to the query of the Orders List.
	 *
	 * @since     1.3.2
	 *
	 * @param object $query The posts query object.
	 * @return object $query The modified query object.
	 */
	public function filter_orders_list_by_pickup_date( $query ) {

		if ( is_admin() && 'shop_order' === $query->query_vars['post_type'] && ! empty( $_GET['orderby'] ) && 'pickup_time' === $_GET['orderby'] ) {
			$query->set( 'meta_key', $this->order_meta_key );
			$query->set( 'orderby', 'meta_value_num' );
			$query->set( 'order', ( ! empty( $_GET['order'] ) ) ? strtoupper( woocommerce_clean( $_GET['order'] ) ) : 'ASC' );
		}

		return $query;

	}

	/**
	 * Show Pickup Time content on the Order Preview in the Admin Dashboard.
	 *
	 * @since     1.3.2
	 *
	 * @param array $order_details  The array of order data.
	 * @return array  The order details array.
	 */
	public function woocommerce_admin_order_preview_get_order_details( $order_details ) {

		return $order_details;

	}

	/**
	 * Return translatable pickup time
	 *
	 * @since    1.3.0
	 *
	 * @param string $value   The pikcup time meta value for an order.
	 * @eturn string  The translated value of the order pickup time.
	 */
	public function pickup_time_select_translatable( $value ) {

		// Get an instance of the Public plugin.
		$plugin = Local_Pickup_Time::get_instance();

		// Call the Public plugin instance of this method to reduce code redundancy.
		return $plugin->pickup_time_select_translatable( $value );

	}

}
