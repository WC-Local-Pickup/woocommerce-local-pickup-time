<?php
/**
 * Class WooCommerce_Local_Pickup_Time_Test
 *
 * @package   WooCommerce_Local_Pickup_Time
 */

/**
 * Local Pickup Time test case.
 */
class WooCommerce_Local_Pickup_Time_Test extends WC_Unit_Test_Case {

	/**
	 * Test case setup method.
	 */
	public function setUp() {

		parent::setUp();

	}

	/**
	 * Test case cleanup method.
	 */
	public function tearDown() {

		parent::tearDown();

	}

	/**
	 * Test plugin is installed.
	 *
	 * @group GenericTests
	 */
	public function test_plugin_installed() {

		$this->assertArrayHasKey(
			'woocommerce-local-pickup-time/woocommerce-local-pickup-time.php',
			get_plugins(),
			'Plugin should be installed. Found: ' . print_r( get_plugins(), true )
		);

	}

}
