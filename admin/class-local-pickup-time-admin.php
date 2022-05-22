<?php
/**
 * Local Pickup Time
 *
 * @package   Local_Pickup_Time_Admin
 */

/**
 * Local_Pickup_Time_Admin class
 * Defines administrative functionality
 *
 * @package Local_Pickup_Time_Admin
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
	 * Instance of the Plugin class.
	 *
	 * @since    1.4.0
	 *
	 * @var      Local_Pickup_Time
	 */
	protected $plugin = null;

	/**
	 * Initialize the plugin by loading admin scripts & styles and adding a
	 * settings page and menu.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {

		$this->plugin = Local_Pickup_Time::get_instance();

		/*
		 * Add settings quicklink to plugin entry in the plugins list.
		 */
		add_filter( 'plugin_action_links_' . WCLOCALPICKUPTIME_PLUGIN_BASE, array( $this, 'plugin_action_links' ) );

		/*
		 * Show Pickup Time Settings in the WooCommerce -> Shipping Screen
		 */
		add_filter( 'woocommerce_get_sections_shipping', array( $this, 'plugin_add_settings_section' ) );
		add_filter( 'woocommerce_get_settings_shipping', array( $this, 'plugin_settings' ), 10, 2 );

		/*
		 * Add Local Pickup Time enabled argument to Local Pickup Shipping methods.
		 */
		add_filter( 'woocommerce_shipping_method_add_rate_args', array( $this, 'shipping_method_add_rate_pickup_time_args' ) );

		/*
		 * Add an option to bind the Local Pickup Time to Local Pickup shipping methods.
		 */
		add_filter( 'woocommerce_shipping_methods', array( $this, 'shipping_methods_settings_override' ) );

		/*
		 * Add support for a Ready for Pickup Order Status.
		 */
		add_action( 'init', array( $this, 'register_post_status' ) );
		add_filter( 'wc_order_statuses', array( $this, 'wc_order_statuses' ), 10, 1 );
		add_filter( 'bulk_actions-edit-shop_order', array( $this, 'add_bulk_actions_edit_shop_order' ), 50, 1 );
		add_action( 'woocommerce_email_actions', array( $this, 'woocommerce_email_actions' ) );
		add_action( 'woocommerce_email_classes', array( $this, 'woocommerce_email_classes' ) );

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
	 * Add plugin Settings page link to plugin actions.
	 *
	 * @link https://developer.wordpress.org/reference/hooks/plugin_action_links_plugin_file/
	 *
	 * @since 1.4.0
	 *
	 * @param string[] $actions The plugin action links.
	 *
	 * @return string[]
	 */
	public function plugin_action_links( $actions ) {

		$settings_link = '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=shipping&section=' . $this->plugin->get_plugin_slug() ) . '">' . __( 'Settings', 'woocommerce-local-pickup-time-select' ) . '</a>';

		array_unshift( $actions, $settings_link );

		return $actions;

	}

	/**
	 * Add Pickup Time Settings section on the WooCommerce->Shipping settings Admin screen.
	 *
	 * @link https://woocommerce.com/document/adding-a-section-to-a-settings-tab/
	 *
	 * @since 1.4.0
	 *
	 * @param array<string> $sections The array of Settings screen sections.
	 *
	 * @return array<string>
	 */
	public function plugin_add_settings_section( $sections ) {

		$sections[ $this->plugin->get_plugin_slug() ] = __( 'Local Pickup Time settings', 'woocommerce-local-pickup-time-select' );

		return $sections;

	}

	/**
	 * Show Pickup Time Settings in the WooCommerce -> General Admin Screen
	 *
	 * @since    1.0.0
	 *
	 * @param array<mixed[]> $settings        The array of WooCommerce General Plugin Settings.
	 * @param string         $current_section The plugin settings section.
	 *
	 * @return array<mixed[]>
	 */
	public function plugin_settings( $settings, $current_section ) {

		if ( $this->plugin->get_plugin_slug() === $current_section ) {
			$plugin_settings = array();

			$plugin_settings = array(
				array(
					'title' => __( 'Store Hours for Local Pickup', 'woocommerce-local-pickup-time-select' ),
					'type'  => 'title',
					'desc'  => __( 'The following options affect when order pickups begin and end each day, and which days to not allow order pickups.', 'woocommerce-local-pickup-time-select' ),
					'id'    => 'local_pickup_hours',
				),
				array(
					'title'    => __( 'Monday Pickup Start Time', 'woocommerce-local-pickup-time-select' ),
					'desc'     => __( 'This sets the pickup start time for Monday. Use 24-hour time format.', 'woocommerce-local-pickup-time-select' ),
					'id'       => 'local_pickup_hours_monday_start',
					'css'      => 'width:120px;',
					'default'  => '10:00',
					'type'     => 'time',
					'desc_tip' => true,
				),
				array(
					'title'    => __( 'Monday Pickup End Time', 'woocommerce-local-pickup-time-select' ),
					'desc'     => __( 'This sets the pickup end time for Monday. Use 24-hour time format.', 'woocommerce-local-pickup-time-select' ),
					'id'       => 'local_pickup_hours_monday_end',
					'css'      => 'width:120px;',
					'default'  => '19:00',
					'type'     => 'time',
					'desc_tip' => true,
				),
				array(
					'title'    => __( 'Tuesday Pickup Start Time', 'woocommerce-local-pickup-time-select' ),
					'desc'     => __( 'This sets the pickup start time for Tuesday. Use 24-hour time format.', 'woocommerce-local-pickup-time-select' ),
					'id'       => 'local_pickup_hours_tuesday_start',
					'css'      => 'width:120px;',
					'default'  => '10:00',
					'type'     => 'time',
					'desc_tip' => true,
				),
				array(
					'title'    => __( 'Tuesday Pickup End Time', 'woocommerce-local-pickup-time-select' ),
					'desc'     => __( 'This sets the pickup end time for Tuesday. Use 24-hour time format.', 'woocommerce-local-pickup-time-select' ),
					'id'       => 'local_pickup_hours_tuesday_end',
					'css'      => 'width:120px;',
					'default'  => '19:00',
					'type'     => 'time',
					'desc_tip' => true,
				),
				array(
					'title'    => __( 'Wednesday Pickup Start Time', 'woocommerce-local-pickup-time-select' ),
					'desc'     => __( 'This sets the pickup start time for Wednesday. Use 24-hour time format.', 'woocommerce-local-pickup-time-select' ),
					'id'       => 'local_pickup_hours_wednesday_start',
					'css'      => 'width:120px;',
					'default'  => '10:00',
					'type'     => 'time',
					'desc_tip' => true,
				),
				array(
					'title'    => __( 'Wednesday Pickup End Time', 'woocommerce-local-pickup-time-select' ),
					'desc'     => __( 'This sets the pickup end time for Wednesday. Use 24-hour time format.', 'woocommerce-local-pickup-time-select' ),
					'id'       => 'local_pickup_hours_wednesday_end',
					'css'      => 'width:120px;',
					'default'  => '19:00',
					'type'     => 'time',
					'desc_tip' => true,
				),
				array(
					'title'    => __( 'Thursday Pickup Start Time', 'woocommerce-local-pickup-time-select' ),
					'desc'     => __( 'This sets the pickup start time for Thursday. Use 24-hour time format.', 'woocommerce-local-pickup-time-select' ),
					'id'       => 'local_pickup_hours_thursday_start',
					'css'      => 'width:120px;',
					'default'  => '10:00',
					'type'     => 'time',
					'desc_tip' => true,
				),
				array(
					'title'    => __( 'Thursday Pickup End Time', 'woocommerce-local-pickup-time-select' ),
					'desc'     => __( 'This sets the pickup end time for Thursday. Use 24-hour time format.', 'woocommerce-local-pickup-time-select' ),
					'id'       => 'local_pickup_hours_thursday_end',
					'css'      => 'width:120px;',
					'default'  => '19:00',
					'type'     => 'time',
					'desc_tip' => true,
				),
				array(
					'title'    => __( 'Friday Pickup Start Time', 'woocommerce-local-pickup-time-select' ),
					'desc'     => __( 'This sets the pickup start time for Friday. Use 24-hour time format.', 'woocommerce-local-pickup-time-select' ),
					'id'       => 'local_pickup_hours_friday_start',
					'css'      => 'width:120px;',
					'default'  => '10:00',
					'type'     => 'time',
					'desc_tip' => true,
				),
				array(
					'title'    => __( 'Friday Pickup End Time', 'woocommerce-local-pickup-time-select' ),
					'desc'     => __( 'This sets the pickup end time for Friday. Use 24-hour time format.', 'woocommerce-local-pickup-time-select' ),
					'id'       => 'local_pickup_hours_friday_end',
					'css'      => 'width:120px;',
					'default'  => '19:00',
					'type'     => 'time',
					'desc_tip' => true,
				),
				array(
					'title'    => __( 'Saturday Pickup Start Time', 'woocommerce-local-pickup-time-select' ),
					'desc'     => __( 'This sets the pickup start time for Saturday. Use 24-hour time format.', 'woocommerce-local-pickup-time-select' ),
					'id'       => 'local_pickup_hours_saturday_start',
					'css'      => 'width:120px;',
					'default'  => '10:00',
					'type'     => 'time',
					'desc_tip' => true,
				),
				array(
					'title'    => __( 'Saturday Pickup End Time', 'woocommerce-local-pickup-time-select' ),
					'desc'     => __( 'This sets the pickup end time for Saturday. Use 24-hour time format.', 'woocommerce-local-pickup-time-select' ),
					'id'       => 'local_pickup_hours_saturday_end',
					'css'      => 'width:120px;',
					'default'  => '19:00',
					'type'     => 'time',
					'desc_tip' => true,
				),
				array(
					'title'    => __( 'Sunday Pickup Start Time', 'woocommerce-local-pickup-time-select' ),
					'desc'     => __( 'This sets the pickup start time for Sunday. Use 24-hour time format.', 'woocommerce-local-pickup-time-select' ),
					'id'       => 'local_pickup_hours_sunday_start',
					'css'      => 'width:120px;',
					'default'  => '10:00',
					'type'     => 'time',
					'desc_tip' => true,
				),
				array(
					'title'    => __( 'Sunday Pickup End Time', 'woocommerce-local-pickup-time-select' ),
					'desc'     => __( 'This sets the pickup end time for Sunday. Use 24-hour time format.', 'woocommerce-local-pickup-time-select' ),
					'id'       => 'local_pickup_hours_sunday_end',
					'css'      => 'width:120px;',
					'default'  => '19:00',
					'type'     => 'time',
					'desc_tip' => true,
				),
				array(
					'type' => 'sectionend',
					'id'   => 'local_pickup_hours',
				),
				array(
					'title' => __( 'Store Closed for Local Pickup', 'woocommerce-local-pickup-time-select' ),
					'type'  => 'title',
					'desc'  => __( 'The following options affect which days to not allow order pickups.', 'woocommerce-local-pickup-time-select' ),
					'id'    => 'local_pickup_closed',
				),
				array(
					'title'    => __( 'Store Closing Days (use MM/DD/YYYY format)', 'woocommerce-local-pickup-time-select' ),
					'desc'     => __( 'This sets the days the store is closed. Enter one date per line, in format MM/DD/YYYY.', 'woocommerce-local-pickup-time-select' ),
					'id'       => 'local_pickup_hours_closings',
					'css'      => 'width:250px;height:150px;',
					'default'  => '01/01/2014',
					'type'     => 'textarea',
					'desc_tip' => true,
				),
				array(
					'type' => 'sectionend',
					'id'   => 'local_pickup_closed',
				),
				array(
					'title' => __( 'Order Pickup Intervals & Delays', 'woocommerce-local-pickup-time-select' ),
					'type'  => 'title',
					'desc'  => __( 'The following options are used to calculate the available time slots for pickup.', 'woocommerce-local-pickup-time-select' ),
					'id'    => 'local_pickup_time_slots',
				),
				array(
					'title'    => __( 'Pickup Time Interval', 'woocommerce-local-pickup-time-select' ),
					'desc'     => __( 'Choose the time interval for allowing local pickup orders.', 'woocommerce-local-pickup-time-select' ),
					'id'       => 'local_pickup_hours_interval',
					'css'      => 'width:100px;',
					'default'  => '30',
					'type'     => 'select',
					'class'    => 'chosen_select',
					'desc_tip' => true,
					'options'  => array(
						'5'   => __( '5 minutes', 'woocommerce-local-pickup-time-select' ),
						'10'  => __( '10 minutes', 'woocommerce-local-pickup-time-select' ),
						'15'  => __( '15 minutes', 'woocommerce-local-pickup-time-select' ),
						'20'  => __( '20 minutes', 'woocommerce-local-pickup-time-select' ),
						'30'  => __( '30 minutes', 'woocommerce-local-pickup-time-select' ),
						'45'  => __( '45 minutes', 'woocommerce-local-pickup-time-select' ),
						'60'  => __( '1 hour', 'woocommerce-local-pickup-time-select' ),
						'120' => __( '2 hours', 'woocommerce-local-pickup-time-select' ),
					),
				),
				array(
					'title'    => __( 'Pickup Time Delay', 'woocommerce-local-pickup-time-select' ),
					'desc'     => __( 'Choose the time delay from the time of ordering for allowing local pickup orders.', 'woocommerce-local-pickup-time-select' ),
					'id'       => 'local_pickup_delay_minutes',
					'css'      => 'width:100px;',
					'default'  => '60',
					'type'     => 'select',
					'class'    => 'chosen_select',
					'desc_tip' => true,
					'options'  => array(
						'5'     => __( '5 minutes', 'woocommerce-local-pickup-time-select' ),
						'10'    => __( '10 minutes', 'woocommerce-local-pickup-time-select' ),
						'15'    => __( '15 minutes', 'woocommerce-local-pickup-time-select' ),
						'20'    => __( '20 minutes', 'woocommerce-local-pickup-time-select' ),
						'30'    => __( '30 minutes', 'woocommerce-local-pickup-time-select' ),
						'45'    => __( '45 minutes', 'woocommerce-local-pickup-time-select' ),
						'60'    => __( '1 hour', 'woocommerce-local-pickup-time-select' ),
						'120'   => __( '2 hours', 'woocommerce-local-pickup-time-select' ),
						'240'   => __( '4 hours', 'woocommerce-local-pickup-time-select' ),
						'480'   => __( '8 hours', 'woocommerce-local-pickup-time-select' ),
						'960'   => __( '16 hours', 'woocommerce-local-pickup-time-select' ),
						'1440'  => __( '24 hours', 'woocommerce-local-pickup-time-select' ),
						'2160'  => __( '36 hours', 'woocommerce-local-pickup-time-select' ),
						'2880'  => __( '48 hours', 'woocommerce-local-pickup-time-select' ),
						'4320'  => __( '3 days', 'woocommerce-local-pickup-time-select' ),
						'7200'  => __( '5 days', 'woocommerce-local-pickup-time-select' ),
						'10080' => __( '1 week', 'woocommerce-local-pickup-time-select' ),
					),
				),
				array(
					'title'       => __( 'Pickup Time Open Days Ahead', 'woocommerce-local-pickup-time-select' ),
					'desc'        => __( 'Choose the number of open days ahead for allowing local pickup orders. This is inclusive of the current day, if timeslots are still available.', 'woocommerce-local-pickup-time-select' ),
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
					'id'   => 'local_pickup_time_slots',
				),
				array(
					'title' => __( 'Additional Settings', 'woocommerce-local-pickup-time-select' ),
					'type'  => 'title',
					'desc'  => __( 'The following options provide additional capabilities for customization.', 'woocommerce-local-pickup-time-select' ),
					'id'    => 'local_pickup_additional',
				),
				array(
					'title'         => __( 'Require Checkout Pickup Time?', 'woocommerce-local-pickup-time-select' ),
					'label'         => __( 'Required', 'woocommerce-local-pickup-time-select' ),
					'desc'          => __( 'This controls whether a Pickup Time is required during checkout.', 'woocommerce-local-pickup-time-select' ),
					'id'            => 'checkout_time_req',
					'type'          => 'checkbox',
					'checkboxgroup' => 'start',
					'default'       => 'yes',
					'desc_tip'      => true,
				),
				array(
					'title'         => __( 'Limit to Local Pickup Shipping Methods?', 'woocommerce-local-pickup-time-select' ),
					'label'         => __( 'Limit', 'woocommerce-local-pickup-time-select' ),
					'desc'          => __( 'This controls whether Local Pickup Times are restricted to Local Shipping methods. <strong>This requires enabling "Pickup Time" on each individual Local Pickup Shipping method, within each Shiping Zone.</strong>', 'woocommerce-local-pickup-time-select' ),
					'id'            => 'local_pickup_only',
					'type'          => 'checkbox',
					'checkboxgroup' => 'start',
					'default'       => 'no',
					'desc_tip'      => true,
				),
				array(
					'type' => 'sectionend',
					'id'   => 'local_pickup_additional',
				),
			);

			return $plugin_settings;

		}

		return $settings;

	}

	/**
	 * Optionally adds the Local Pickup Time enabling option to a Local Pickup Shipping method.
	 *
	 * @since 1.4.0
	 *
	 * @param array<mixed[]> $shipping_methods An array of WC_Shipping methods.
	 *
	 * @return array<mixed[]>
	 */
	public function shipping_methods_settings_override( $shipping_methods ) {

		if ( 'yes' === $this->plugin->get_local_pickup_only() ) {
			foreach ( $shipping_methods as $shipping_method => $class_name ) {
				if ( 'local_pickup' === $shipping_method ) {
					add_filter( 'woocommerce_shipping_instance_form_fields_' . $shipping_method, array( $this, 'shipping_instance_form_add_extra_fields' ) );
				}
			}
		}

		return $shipping_methods;

	}

	/**
	 * Adds a Local Pickup Time flag to a shipping method.
	 *
	 * @since 1.4.0
	 *
	 * @param array<mixed[]> $fields The array of settings fields.
	 *
	 * @return array<mixed[]>
	 */
	public function shipping_instance_form_add_extra_fields( $fields ) {

		$fields['wclpt_shipping_method_enabled'] = array(
			'title'         => __( 'Pickup Time', 'woocommerce-local-pickup-time-select' ),
			'label'         => __( 'Enable', 'woocommerce-local-pickup-time-select' ),
			'description'   => __( 'This controls whether a Pickup Time is tied to the shipping method.', 'woocommerce-local-pickup-time-select' ),
			'type'          => 'checkbox',
			'checkboxgroup' => 'start',
			'default'       => 'no',
			'desc_tip'      => true,
		);

		return $fields;

	}

	/**
	 * Support processing the Local Pickup Time enabled option on Local Pickup shipping instances.
	 *
	 * @since 1.4.0
	 *
	 * @param array<mixed[]>          $args            The shipping method arguments.
	 * @param WC_Shipping_Method|null $shipping_method The WC_Shipping_Method instance object.
	 *
	 * @return array<mixed[]>
	 */
	public function shipping_method_add_rate_pickup_time_args( $args, $shipping_method = null ) {

		if ( empty( $shipping_method ) ) {
			return $args;
		}

		if ( 'yes' === $this->plugin->get_local_pickup_only() && 'local_pickup' === $shipping_method->get_rate_id() ) {
			$args['meta_data']['wclpt_shipping_method_enabled'] = $shipping_method->get_option( 'wclpt_shipping_method_enabled' );
		}

		return $args;

	}

	/**
	 * Add a post status for Ready for Pickup for WooCommerce.
	 *
	 * @since 1.4.0
	 *
	 * @return void
	 */
	public function register_post_status() {
		register_post_status(
			'wc-ready-for-pickup',
			array(
				'label'                     => _x( 'Ready for Pickup', 'Order status', 'woocommerce-local-pickup-time-select' ),
				'public'                    => true,
				'exclude_from_search'       => false,
				'show_in_admin_all_list'    => true,
				'show_in_admin_status_list' => true,
				/* translators: %s: number of orders */
				'label_count'               => _n_noop( 'Ready for Pickup <span class="count">(%s)</span>', 'Ready for Pickup <span class="count">(%s)</span>', 'woocommerce-local-pickup-time-select' ),
			)
		);
	}

	/**
	 * Add a Ready for Pickup Order Status to WooCommerce.
	 *
	 * @since 1.4.0
	 *
	 * @param array<string> $order_statuses The array of WooCommerce Order Statuses.
	 *
	 * @return array<string>
	 */
	public function wc_order_statuses( $order_statuses ) {

		$order_statuses['wc-ready-for-pickup'] = _x( 'Ready for Pickup', 'Order status', 'woocommerce-local-pickup-time-select' );

		return $order_statuses;

	}

	/**
	 * Add a bulk order action to change statuses to Ready for Pickup.
	 *
	 * @since 1.4.0
	 *
	 * @param array<string> $actions The array of bulk order actions from the Order listing.
	 *
	 * @return array<string>
	 */
	public function add_bulk_actions_edit_shop_order( $actions ) {

		$actions['mark_ready-for-pickup'] = __( 'Change status to ready for pickup', 'woocommerce-local-pickup-time-select' );

		return $actions;

	}

	/**
	 * Add a Ready for Pickup Order Status email action to WooCommerce..
	 *
	 * @since 1.4.0
	 *
	 * @param array<string> $email_actions The array of transactional emails in WooCommerce.
	 *
	 * @return array<string>
	 */
	public function woocommerce_email_actions( $email_actions ) {

		$email_actions[] = 'woocommerce_order_status_ready-for-pickup';

		return $email_actions;

	}

	/**
	 * Add a Ready for Pickup Order Status email class to WooCommerce.
	 *
	 * @since 1.4.0
	 *
	 * @param array<mixed> $email_classes The array of email class files.
	 *
	 * @return array<mixed>
	 */
	public function woocommerce_email_classes( $email_classes ) {

		$email_classes['WC_Email_Customer_Ready_For_Pickup_Order'] = include __DIR__ . '/emails/class-wc-email-customer-ready-for-pickup-order.php';

		return $email_classes;

	}

	/**
	 * Show Pickup Time in the Order Details in the Admin Screen
	 *
	 * @since    1.0.0
	 *
	 * @param WC_Order $order  The order object.
	 *
	 * @return void
	 */
	public function show_metabox( $order ) {

		$order_meta  = get_post_custom( $order->get_id() );
		$pickup_time = $order_meta[ $this->plugin->get_order_meta_key() ][0];

		$allowed_html = array(
			'p' => array(),
			'strong' => array(),
		);

		echo wp_kses( '<p><strong>' . __( 'Pickup Time:', 'woocommerce-local-pickup-time-select' ) . '</strong> ' . esc_html( $this->pickup_time_select_translatable( $pickup_time ) ) . '</p>', $allowed_html );

	}

	/**
	 * Show Pickup Time on Orders List in Admin Dashboard.
	 *
	 * @since           1.3.2
	 *
	 * @param   array<string> $columns    The Orders List columns array.
	 * @return  array<string> $new_columns    The updated Orders List columns array.
	 */
	public function add_orders_list_pickup_date_column_header( $columns ) {

		$new_columns = array();

		foreach ( $columns as $column_name => $column_info ) {

			$new_columns[ $column_name ] = $column_info;

			if ( 'order_date' === $column_name ) {
				$new_columns[ $this->plugin->get_order_meta_key() ] = __( 'Pickup Time', 'woocommerce-local-pickup-time-select' );
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
	 *
	 * @return void
	 */
	public function add_orders_list_pickup_date_column_content( $column ) {

		global $the_order;

		if ( $this->plugin->get_order_meta_key() === $column ) {
			echo esc_html( $this->pickup_time_select_translatable( $the_order->get_meta( $this->plugin->get_order_meta_key() ) ) );
		}

	}

	/**
	 * Allow the Pickup Time columns to be sortable on the Orders List in the Admin Dashboard.
	 *
	 * @since     1.3.2
	 *
	 * @param array<string> $columns  The array of Order columns.
	 * @return array<string> The updated array Order columns.
	 */
	public function add_orders_list_pickup_date_column_sorting( $columns ) {

		$new_columns                          = array();
		$new_columns[ $this->plugin->get_order_meta_key() ] = 'pickup_time';

		return wp_parse_args( $new_columns, $columns );

	}

	/**
	 * Adds Local Pickup Time sorting to the query of the Orders List.
	 *
	 * @since     1.3.2
	 *
	 * @param WP_Query $query The posts query object.
	 * @return WP_Query $query The modified query object.
	 */
	public function filter_orders_list_by_pickup_date( $query ) {

		if ( is_admin() && 'shop_order' === $query->query_vars['post_type'] && ! empty( $_GET['orderby'] ) && 'pickup_time' === $_GET['orderby'] ) {
			$order = ( ! empty( $_GET['order'] ) ) ? strtoupper( sanitize_key( $_GET['order'] ) ) : 'ASC';
			$query->set( 'meta_key', $this->plugin->get_order_meta_key() );
			$query->set( 'orderby', 'meta_value_num' );
			$query->set( 'order', $order );
		}

		return $query;

	}

	/**
	 * Show Pickup Time content on the Order Preview in the Admin Dashboard.
	 *
	 * @since     1.3.2
	 *
	 * @param WC_Order|array<mixed> $order_details  The array of order data.
	 * @return WC_Order|array<mixed>  The order details array.
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
	 * @return string  The translated value of the order pickup time.
	 */
	public function pickup_time_select_translatable( $value ) {

		// Get an instance of the Public plugin.
		$plugin = Local_Pickup_Time::get_instance();

		// Call the Public plugin instance of this method to reduce code redundancy.
		return $plugin->pickup_time_select_translatable( $value );

	}

}
