<?php
/**
 * Class Local_Pickup_Time_Admin_Test
 *
 * @package   Local_Pickup_Time_Admin
 */

/**
 * Local Pickup Time Admin test case.
 */
class Local_Pickup_Time_Admin_Test extends WP_UnitTestCase {

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
	 * Test plugin admin get_instance method.
	 *
	 * @group AdminTests
	 */
	public function test_plugin_admin_returns_valid_instance() {

		$plugin_admin = new Local_Pickup_Time_Admin();

		$this->assertInstanceOf( Local_Pickup_Time_Admin::class, $plugin_admin->get_instance() );

	}

}
