<?php
/**
 * Class Local_Pickup_Time_Plugin_Test
 *
 * @package   Local_Pickup_Time
 */

/**
 * Local Pickup Time plugin test case.
 */
class Local_Pickup_Time_Plugin_Test extends WP_UnitTestCase {

	/**
	 * Test case setup method.
	 *
	 * @return void
	 */
	public function setUp(): void {
		parent::setUp();
	}

	/**
	 * Test case cleanup method.
	 *
	 * @return void
	 */
	public function tearDown(): void {
		parent::tearDown();
	}

	/**
	 * Test plugin initialization.
	 *
	 * @group PluginTests
	 */
	public function test_plugin_returns_valid_instance() {
		$this->assertTrue( true, 'Needs Unit Tests.' );
	}
}
