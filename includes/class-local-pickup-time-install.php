<?php
/**
 * Local Pickup Time
 *
 * @package   Local_Pickup_Time_Install
 * @author    Tim Nolte <tim.nolte@ndigitals.com>
 * @license   GPL-2.0+
 * @link      https://www.ndigitals.com
 * @copyright 2014-2020 Local Pickup Time
 */

/**
 * Local_Pickup_Time_Install class
 * Defines installation functionality
 *
 * @package Local_Pickup_Time_Install
 * @author  Tim Nolte <tim.nolte@ndigitals.com>
 */
class Local_Pickup_Time_Install {

	/**
	 * Instance of this class.
	 *
	 * @since    1.4.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Local Pickup Orders indexing table name suffix.
	 *
	 * The suffix to use along with the WordPress configured table prefix when
	 * referencing the plugin orders index table.
	 *
	 * @since     1.4.0
	 *
	 * @var       string
	 */
	protected $index_table_suffix = '_wlpt_orders';

	/**
	 * Initialize the plugin by loading admin scripts & styles and adding a
	 * settings page and menu.
	 *
	 * @since     1.4.0
	 */
	private function __construct() {

		// Call $plugin_slug from public plugin class.
		$plugin = Local_Pickup_Time::get_instance();

	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.4.0
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
	 * Return the local pickup time database schema.
	 *
	 * @since    1.4.0
	 *
	 * @return   string    The SQL statment.
	 */
	public function get_schema() {

		global $wpdb;

		$collate = '';

		if ( $wpdb->has_cap( 'collation' ) ) {
			$collate = $wpdb->get_charset_collate();
		}

		$max_index_length = 191;

		$tables = "
CREATE TABLE {$wpdb->prefix}{$this->get_index_table_suffix()} (
  order_item_id BIGINT UNSIGNED NOT NULL auto_increment,
  order_item_name TEXT NOT NULL,
  order_item_type varchar(200) NOT NULL DEFAULT '',
  order_id BIGINT UNSIGNED NOT NULL,
  PRIMARY KEY  (order_item_id),
  KEY order_id (order_id)
) $collate;
";

		return $tables;

	}

}
