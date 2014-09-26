<?php

/**
 * Tests for Model_Batter_Result
 *
 * @group App
 * @group Model
 * @group Model_Batter_Result
 */
class Test_Model_Batter_Result extends \Test_Model_Base
{
	public function setUp()
	{
	}

	public function tearDown()
	{
	}

	public function test_スキーマチェック()
	{
		$this->assertSchema();
	}

	public function test_get_allのテスト()
	{
		$res = Model_Batter_Result::get_all();

		$this->assertTrue(is_array($res));
	}
}
