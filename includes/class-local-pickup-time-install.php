<?php
/**
 * Local Pickup Time
 *
 * @package   Local_Pickup_Time_Install
 */

/**
 * Local_Pickup_Time_Install class
 * Defines installation functionality
 *
 * @package Local_Pickup_Time_Install
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
	 * Local Pickup Orders indexing base table name.
	 *
	 * The base table name to use along with the WordPress configured table
	 * prefix when referencing the plugin orders index table.
	 *
	 * @since     1.4.0
	 *
	 * @var       string
	 */
	protected $index_table_name = 'wlpt_orders';

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

		$db_index_table = new Local_Pickup_Time_Database_Handler( $wpdb, $this->index_table_name );

		// $max_index_length = 191;

		$tables = "
CREATE TABLE {$db_index_table->get_table_name()} (
  pickup_time_id BIGINT UNSIGNED NOT NULL auto_increment,
  pickup_time INT(11) UNSIGNED NOT NULL,
  order_id BIGINT UNSIGNED NOT NULL,
  PRIMARY KEY  (pickup_time_id),
  INDEX (pickup_time)
) {$db_index_table->get_collation()};
";

		return $tables;

	}

}
