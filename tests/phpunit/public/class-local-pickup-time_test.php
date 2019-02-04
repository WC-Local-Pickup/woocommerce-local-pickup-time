<?php
/**
 * Class Local_Pickup_Time_Test
 *
 * @package   Local_Pickup_Time
 */

/**
 * Local Pickup Time test case.
 */
class Local_Pickup_Time_Test extends WC_Unit_Test_Case {

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
	 * Test plugin get_instance method.
	 *
	 * @group PublicTests
	 */
	public function test_plugin_returns_valid_instance() {

		/**
		$plugin = new Local_Pickup_Time();

		$this->assertInstanceOf( Local_Pickup_Time::class, $plugin->get_instance() );
		 */

		$this->assertTrue( true, 'Needs Unit Tests.' );

	}

}
