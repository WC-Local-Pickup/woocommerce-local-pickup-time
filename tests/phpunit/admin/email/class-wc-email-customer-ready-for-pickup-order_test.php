<?php
/**
 * Class Local_Pickup_Time_Admin_Email_Test
 *
 * @package   Local_Pickup_Time_Admin_Email
 */

/**
 * Local Pickup Time Admin Email test case.
 */
class Local_Pickup_Time_Admin_Email_Test extends WP_UnitTestCase {

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
	 * Test plugin admin emails.
	 *
	 * @group AdminEmailTests
	 */
	public function test_plugin_admin_returns_valid_instance() {
		$this->assertTrue( true, 'Needs Unit Tests.' );
	}
}
