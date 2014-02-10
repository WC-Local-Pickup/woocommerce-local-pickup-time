<?php
/**
 * Local Pickup Time.
 *
 * @package   Local_Pickup_Time_Admin
 * @author    Matt Banks <mjbanks@gmail.com>
 * @license   GPL-2.0+
 * @link      http://mattbanks.me
 * @copyright 2014 Matt Banks
 */

/**
 * Plugin class. This class should ideally be used to work with the
 * administrative side of the WordPress site.
 *
 * If you're interested in introducing public-facing
 * functionality, then refer to `class-plugin-name.php`
 *
 * @TODO: Rename this class to a proper name for your plugin.
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
		 * Show Pickup Time in the Order Details in the Admin Screen
		 */
		add_action( 'woocommerce_admin_order_data_after_billing_address', array( $this, 'local_pickup_time_show_metabox' ), 10, 1 );

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
	 * Show Pickup Time in the Order Details in the Admin Screen
	 * @since    1.0.0
	 */
	public function local_pickup_time_show_metabox( $order ){
		echo "<p><strong>Pickup Time:</strong> " . $order->order_custom_fields['_local_pickup_time_select'][0] . "</p>";
	}

}
