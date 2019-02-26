<?php
/**
 * Class Local_Pickup_Time_Admin_Test
 *
 * @package   Local_Pickup_Time_Admin
 */

/**
 * Local Pickup Time Admin test case.
 */
class Local_Pickup_Time_Admin_Test extends WC_Unit_Test_Case {

	/**
	 * Test case setup method.
	 */
	public function setUp() {

		parent::setUp();

		$user_id = $this->factory->user->create( array( 'role' => 'administrator' ) );
		$user = wp_set_current_user( $user_id );
		set_current_screen( 'dashboard' );

	}

	/**
	 * Test case cleanup method.
	 */
	public function tearDown() {

		parent::tearDown();

	}

	/**
	 * Test that WordPress dashboard/admin panel is loaded.
	 *
	 * @group AdminTests
	 */
	public function test_is_admin() {

		$this->assertTrue( is_admin(), "WordPress Dashboard/Administration Panel shoud be loaded." );

	}

	/**
	 * Test plugin admin get_instance method.
	 *
	 * @group AdminTests
	 */
	public function test_plugin_admin_returns_valid_instance() {

		$this->assertInstanceOf( Local_Pickup_Time_Admin::class, Local_Pickup_Time_Admin::get_instance() );

	}

}
