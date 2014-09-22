<?php

/**
 * Tests for Model_Player
 *
 * @group App
 * @group Model
 * @group Model_Player
 */
class Test_Model_Player extends \Test_Model_Base
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
