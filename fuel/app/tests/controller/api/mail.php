<?php

/**
 * Tests for Controler_Api_Mail
 *
 * @group App
 * @group Controller
 * @group Controller_Api_Mail
 */
class Test_Controller_Api_Mail extends Test_Base
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
	public function test_未ログイン状態で権限なしテスト()
	{
		$res = Request::forge('/api/mail/remind')->set_method('post')->execute()->response();
		$this->assertRedirect($res, 'error/403');
	}
}
