<?php
/**
 * WooCommerce & Local Pickup Time Unit Tests Bootstrap
 *
 * @since   1.3.2
 * @package WC_Local_Pickup_Time_Unit_Tests_Bootstrap
 * @author  Tim Nolte <tim.nolte@ndigitals.com>
 */

/**
 * WC_Local_Pickup_Time_Unit_Tests_Bootstrap class.
 * Sets up unit test environment.
 *
 * @package WC_Local_Pickup_Time_Unit_Tests_Bootstrap
 * @author  Tim Nolte <tim.nolte@ndigitals.com>
 */
class WC_Local_Pickup_Time_Unit_Tests_Bootstrap {

	/**
	 * WC_Local_Pickup_Time_Unit_Tests_Bootstrap instance.
	 *
	 * @since 1.3.2
	 *
	 * @var object
	 */
	protected static $instance = null;

	/**
	 * Directory where wordpress-tests-lib is installed.
	 *
	 * @since 1.3.2
	 *
	 * @var string
	 */
	public $wp_tests_dir;

	/**
	 * Testing directory .
	 *
	 * @since 1.3.2
	 *
	 * @var string testing directory.
	 */
	public $tests_dir;

	/**
	 * Plugin directory.
	 *
	 * @since 1.3.2
	 *
	 * @var string
	 */
	public $plugin_dir;

	/**
	 * Plugin loader file.
	 *
	 * @since 1.3.2
	 *
	 * @var string
	 */
	protected $plugin_file = 'woocommerce-local-pickup-time.php';

	/**
	 * Composer installed WooCommerce source path.
	 *
	 * @since 1.3.2
	 *
	 * @var string
	 */
	public $wc_dir;

	/**
	 * WooCommerce tests directory.
	 *
	 * @since 1.3.2
	 *
	 * @var string
	 */
	public $wc_tests_dir;

	/**
	 * Setup the unit testing environment.
	 *
	 * @since 1.3.2
	 */
	public function __construct() {

		// phpcs:disable WordPress.PHP.DiscouragedPHPFunctions, WordPress.PHP.DevelopmentFunctions .
		ini_set( 'display_errors', 'on' );
		error_reporting( E_ALL );
		// phpcs:enable WordPress.PHP.DiscouragedPHPFunctions, WordPress.PHP.DevelopmentFunctions .
		// Ensure server variable is set for WP email functions.
		// phpcs:disable WordPress.VIP.SuperGlobalInputUsage.AccessDetected .
		if ( ! isset( $_SERVER['SERVER_NAME'] ) ) {
			$_SERVER['SERVER_NAME'] = 'localhost';
		}
		// phpcs:enable WordPress.VIP.SuperGlobalInputUsage.AccessDetected .
		$this->tests_dir    = dirname( __FILE__ );
		$this->plugin_dir   = dirname( $this->tests_dir );
		$this->wc_dir       = $this->plugin_dir . '/wordpress/wp-content/plugins/woocommerce';
		$this->wc_tests_dir = $this->wc_dir . '/tests';
		$this->wp_tests_dir = getenv( 'WP_TESTS_DIR' ) ? getenv( 'WP_TESTS_DIR' ) : ( getenv( 'TMPDIR' ) ? getenv( 'TMPDIR' ) : '/tmp' ) . '/wordpress-tests-lib';

		// Load test function so tests_add_filter() is available.
		require_once $this->wp_tests_dir . '/includes/functions.php';

		// Load WooCommerce.
		tests_add_filter( 'muplugins_loaded', array( $this, 'load_wc' ) );

		// Load Local Pickup Time plugin manually.
		tests_add_filter( 'muplugins_loaded', array( $this, 'load_local_pickup_time' ) );

		// Install WooCommerce.
		tests_add_filter( 'setup_theme', array( $this, 'install_wc' ) );

		// Load the WordPress testing environment.
		require_once $this->wp_tests_dir . '/includes/bootstrap.php';

		// Load the WooCommerce testing framework.
		$this->includes();
	}

	/**
	 * Load WooCommerce.
	 *
	 * @since 1.3.2
	 */
	public function load_wc() {
		define( 'WC_TAX_ROUNDING_MODE', 'auto' );
		define( 'WC_USE_TRANSACTIONS', false );
		require_once $this->wc_dir . '/woocommerce.php';
	}

	/**
	 * Load Local Pickup Time WooCommerce Extension Plugin.
	 *
	 * @since 1.3.2
	 */
	public function load_local_pickup_time() {
		require_once $this->plugin_dir . '/' . $this->plugin_file;
	}

	/**
	 * Install WooCommerce after the test environment and WC have been loaded.
	 *
	 * @since 1.3.2
	 */
	public function install_wc() {

		// Clean existing install first.
		define( 'WP_UNINSTALL_PLUGIN', true );
		define( 'WC_REMOVE_ALL_DATA', true );
		include $this->wc_dir . '/uninstall.php';

		WC_Install::install();

		// Reload capabilities after install, see https://core.trac.wordpress.org/ticket/28374 .
		if ( version_compare( $GLOBALS['wp_version'], '4.7', '<' ) ) {
			$GLOBALS['wp_roles']->reinit();
		} else {
			$GLOBALS['wp_roles'] = null; // WPCS: override ok.
			wp_roles();
		}

		echo esc_html( 'Installing WooCommerce...' . PHP_EOL );
	}

	/**
	 * Load WC-specific test cases and factories.
	 *
	 * @since 1.3.2
	 */
	public function includes() {

		// Load WooCommerce unit tests framework.
		require_once $this->wc_tests_dir . '/framework/class-wc-unit-test-factory.php';
		require_once $this->wc_tests_dir . '/framework/class-wc-mock-session-handler.php';
		require_once $this->wc_tests_dir . '/framework/class-wc-mock-wc-data.php';
		require_once $this->wc_tests_dir . '/framework/class-wc-mock-wc-object-query.php';
		require_once $this->wc_tests_dir . '/framework/class-wc-mock-payment-gateway.php';
		require_once $this->wc_tests_dir . '/framework/class-wc-payment-token-stub.php';
		require_once $this->wc_tests_dir . '/framework/vendor/class-wp-test-spy-rest-server.php';

		// Load WooCommerce test cases.
		require_once $this->wc_tests_dir . '/includes/wp-http-testcase.php';
		require_once $this->wc_tests_dir . '/framework/class-wc-unit-test-case.php';
		require_once $this->wc_tests_dir . '/framework/class-wc-api-unit-test-case.php';
		require_once $this->wc_tests_dir . '/framework/class-wc-rest-unit-test-case.php';

		// Load WooCommerce unit test helpers.
		require_once $this->wc_tests_dir . '/framework/helpers/class-wc-helper-product.php';
		require_once $this->wc_tests_dir . '/framework/helpers/class-wc-helper-coupon.php';
		require_once $this->wc_tests_dir . '/framework/helpers/class-wc-helper-fee.php';
		require_once $this->wc_tests_dir . '/framework/helpers/class-wc-helper-shipping.php';
		require_once $this->wc_tests_dir . '/framework/helpers/class-wc-helper-customer.php';
		require_once $this->wc_tests_dir . '/framework/helpers/class-wc-helper-order.php';
		require_once $this->wc_tests_dir . '/framework/helpers/class-wc-helper-shipping-zones.php';
		require_once $this->wc_tests_dir . '/framework/helpers/class-wc-helper-payment-token.php';
		require_once $this->wc_tests_dir . '/framework/helpers/class-wc-helper-settings.php';
	}

	/**
	 * Get the single class instance.
	 *
	 * @since 1.3.2
	 *
	 * @return WC_Unit_Tests_Bootstrap
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}

WC_Local_Pickup_Time_Unit_Tests_Bootstrap::instance();
