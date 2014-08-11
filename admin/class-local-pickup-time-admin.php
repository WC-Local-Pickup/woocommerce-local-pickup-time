<?php
/**
 * Local Pickup Time
 *
 * @package   Local_Pickup_Time_Admin
 * @author    Matt Banks <mjbanks@gmail.com>
 * @license   GPL-2.0+
 * @link      http://mattbanks.me
 * @copyright 2014 Matt Banks
 */

/**
 * Local_Pickup_Time_Admin class
 * Defines administrative functionality
 *
 * @package Local_Pickup_Time_Admin
 * @author  Your Name <mjbanks@gmail.com>
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

		/*
		 * Call $plugin_slug from public plugin class.
		 */
		$plugin = Local_Pickup_Time::get_instance();
		$this->plugin_slug = $plugin->get_plugin_slug();

		/*
		 * Show Pickup Time Settings in the WooCommerce -> General Admin Screen
		 */
		add_filter( 'woocommerce_general_settings', array( $this, 'add_hours_and_closings_options' ) );

		/*
		 * Show Pickup Time in the Order Details in the Admin Screen
		 */
		$admin_hooked_location = apply_filters( 'local_pickup_time_admin_location', 'woocommerce_admin_order_data_after_billing_address' );
		add_action( $admin_hooked_location, array( $this, 'show_metabox' ), 10, 1 );

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
	 * Show Pickup Time Settings in the WooCommerce -> General Admin Screen
	 * @since    1.0.0
	 */
	public function add_hours_and_closings_options( $settings ) {
		$updated_settings = array();

		$updated_settings[] = array(
			array(
				'title' => __( 'Store Hours and Closings for Local Pickup', $this->plugin_slug ),
				'type' => 'title',
				'desc' => __('The following options affect when order pickups begin and end each day, and which days to not allow order pickups.', $this->plugin_slug ),
				'id' => 'local_pickup_hours'
			),
			array(
				'title'     => __( 'Monday Pickup Start Time (use 24-hour time)', $this->plugin_slug ),
				'desc'     => __( 'This sets the pickup start time for Monday. Use 24-hour time format.', $this->plugin_slug ),
				'id'       => 'local_pickup_hours_monday_start',
				'css'      => 'width:100px;',
				'default'  => '10:00',
				'type'     => 'text',
				'desc_tip' =>  true,
			),
			array(
				'title'     => __( 'Monday Pickup End Time (use 24-hour time)', $this->plugin_slug ),
				'desc'     => __( 'This sets the pickup end time for Monday. Use 24-hour time format.', $this->plugin_slug ),
				'id'       => 'local_pickup_hours_monday_end',
				'css'      => 'width:100px;',
				'default'  => '19:00',
				'type'     => 'text',
				'desc_tip' =>  true,
			),
			array(
				'title'     => __( 'Tuesday Pickup Start Time (use 24-hour time)', $this->plugin_slug ),
				'desc'     => __( 'This sets the pickup start time for Tuesday. Use 24-hour time format.', $this->plugin_slug ),
				'id'       => 'local_pickup_hours_tuesday_start',
				'css'      => 'width:100px;',
				'default'  => '10:00',
				'type'     => 'text',
				'desc_tip' =>  true,
			),
			array(
				'title'     => __( 'Tuesday Pickup End Time (use 24-hour time)', $this->plugin_slug ),
				'desc'     => __( 'This sets the pickup end time for Tuesday. Use 24-hour time format.', $this->plugin_slug ),
				'id'       => 'local_pickup_hours_tuesday_end',
				'css'      => 'width:100px;',
				'default'  => '19:00',
				'type'     => 'text',
				'desc_tip' =>  true,
			),
			array(
				'title'     => __( 'Wednesday Pickup Start Time (use 24-hour time)', $this->plugin_slug ),
				'desc'     => __( 'This sets the pickup start time for Wednesday. Use 24-hour time format.', $this->plugin_slug ),
				'id'       => 'local_pickup_hours_wednesday_start',
				'css'      => 'width:100px;',
				'default'  => '10:00',
				'type'     => 'text',
				'desc_tip' =>  true,
			),
			array(
				'title'     => __( 'Wednesday Pickup End Time (use 24-hour time)', $this->plugin_slug ),
				'desc'     => __( 'This sets the pickup end time for Wednesday. Use 24-hour time format.', $this->plugin_slug ),
				'id'       => 'local_pickup_hours_wednesday_end',
				'css'      => 'width:100px;',
				'default'  => '19:00',
				'type'     => 'text',
				'desc_tip' =>  true,
			),
			array(
				'title'     => __( 'Thursday Pickup Start Time (use 24-hour time)', $this->plugin_slug ),
				'desc'     => __( 'This sets the pickup start time for Thursday. Use 24-hour time format.', $this->plugin_slug ),
				'id'       => 'local_pickup_hours_thursday_start',
				'css'      => 'width:100px;',
				'default'  => '10:00',
				'type'     => 'text',
				'desc_tip' =>  true,
			),
			array(
				'title'     => __( 'Thursday Pickup End Time (use 24-hour time)', $this->plugin_slug ),
				'desc'     => __( 'This sets the pickup end time for Thursday. Use 24-hour time format.', $this->plugin_slug ),
				'id'       => 'local_pickup_hours_thursday_end',
				'css'      => 'width:100px;',
				'default'  => '19:00',
				'type'     => 'text',
				'desc_tip' =>  true,
			),
			array(
				'title'     => __( 'Friday Pickup Start Time (use 24-hour time)', $this->plugin_slug ),
				'desc'     => __( 'This sets the pickup start time for Friday. Use 24-hour time format.', $this->plugin_slug ),
				'id'       => 'local_pickup_hours_friday_start',
				'css'      => 'width:100px;',
				'default'  => '10:00',
				'type'     => 'text',
				'desc_tip' =>  true,
			),
			array(
				'title'     => __( 'Friday Pickup End Time (use 24-hour time)', $this->plugin_slug ),
				'desc'     => __( 'This sets the pickup end time for Friday. Use 24-hour time format.', $this->plugin_slug ),
				'id'       => 'local_pickup_hours_friday_end',
				'css'      => 'width:100px;',
				'default'  => '19:00',
				'type'     => 'text',
				'desc_tip' =>  true,
			),
			array(
				'title'     => __( 'Saturday Pickup Start Time (use 24-hour time)', $this->plugin_slug ),
				'desc'     => __( 'This sets the pickup start time for Saturday. Use 24-hour time format.', $this->plugin_slug ),
				'id'       => 'local_pickup_hours_saturday_start',
				'css'      => 'width:100px;',
				'default'  => '10:00',
				'type'     => 'text',
				'desc_tip' =>  true,
			),
			array(
				'title'     => __( 'Saturday Pickup End Time (use 24-hour time)', $this->plugin_slug ),
				'desc'     => __( 'This sets the pickup end time for Saturday. Use 24-hour time format.', $this->plugin_slug ),
				'id'       => 'local_pickup_hours_saturday_end',
				'css'      => 'width:100px;',
				'default'  => '19:00',
				'type'     => 'text',
				'desc_tip' =>  true,
			),
			array(
				'title'     => __( 'Sunday Pickup Start Time (use 24-hour time)', $this->plugin_slug ),
				'desc'     => __( 'This sets the pickup start time for Sunday. Use 24-hour time format.', $this->plugin_slug ),
				'id'       => 'local_pickup_hours_sunday_start',
				'css'      => 'width:100px;',
				'default'  => '10:00',
				'type'     => 'text',
				'desc_tip' =>  true,
			),
			array(
				'title'     => __( 'Sunday Pickup End Time (use 24-hour time)', $this->plugin_slug ),
				'desc'     => __( 'This sets the pickup end time for Sunday. Use 24-hour time format.', $this->plugin_slug ),
				'id'       => 'local_pickup_hours_sunday_end',
				'css'      => 'width:100px;',
				'default'  => '19:00',
				'type'     => 'text',
				'desc_tip' =>  true,
			),
			array(
				'title'     => __( 'Store Closing Days (use MM/DD/YYYY format)', $this->plugin_slug ),
				'desc'     => __( 'This sets the days the store is closed. Enter one date per line, in format MM/DD/YYYY.', $this->plugin_slug ),
				'id'       => 'local_pickup_hours_closings',
				'css'      => 'width:250px;height:150px;',
				'default'  => '01/01/2014',
				'type'     => 'textarea',
				'desc_tip' =>  true,
			),
			array(
				'title'     => __( 'Pickup Time Interval', $this->plugin_slug ),
				'desc'     => __( 'Choose the time interval for allowing local pickup orders.', $this->plugin_slug ),
				'id'       => 'local_pickup_hours_interval',
				'css'      => 'width:100px;',
				'default'  => '30',
				'type'     => 'select',
				'class'		=> 'chosen_select',
				'desc_tip' =>  true,
				'options' => array(
					'15'      => __( '15 minutes', $this->plugin_slug ),
					'30'	  => __( '30 minutes', $this->plugin_slug ),
					'60'	  => __( '1 hour', $this->plugin_slug ),
				)
			),
			array( 'type' => 'sectionend', 'id' => 'pricing_options' ),
		);

		$merge = array();

		foreach ($updated_settings as $new_setting) {
			$merge = array_merge( $settings, $new_setting );
		}

		return $merge;
	}

	/**
	 * Show Pickup Time in the Order Details in the Admin Screen
	 * @since    1.0.0
	 */
	public function show_metabox( $order ){
		$order_meta = get_post_custom( $order->id );

		echo '<p><strong>' . __( 'Pickup Time:', $this->plugin_slug ) . '</strong> ' . $order_meta['_local_pickup_time_select'][0] . '</p>';
	}

}
