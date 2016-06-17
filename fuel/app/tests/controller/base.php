<?php

/**
 * Tests for Controler_Base
 *
 * @group App
 * @group Controller
 * @group Controller_Base
 */
class Test_Controller_Base extends Test_Base
{
	protected function setUp()
	{
		parent::setUp();
	}

	protected function tearDown()
	{
		parent::tearDown();
	}

	/**
	 *
	 */
	public function test_View_set_globalのテスト()
	{
		$res = Request::forge('/')->execute()->response();

		$this->assertSame(Fuel::$env, $res->body->fuel_env);
		$this->assertSame(Common::get_usericon_url(), $res->body->usericon);
		$this->assertSame(Agent::is_mobiledevice(), $res->body->is_mobile);
	}

	/**
	 *
	 */
	public function test_最初アクセスすると未ログイン状態()
	{
		$res = Request::forge('/')->execute()->response();

		$this->assertFalse(Auth::check());
	}

	/**
	 *
	 */
	public function test_ログイン状態でアクセスするとログイン状態に()
	{
		$id = Model_User::find('first')->id;
		Auth::force_login($id);

		$res = Request::forge('/')->execute()->response();

		$this->assertTrue(Auth::check());

		Auth::logout();
	}
}
