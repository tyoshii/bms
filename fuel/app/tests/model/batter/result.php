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
}
