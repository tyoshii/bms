<?php

/**
 * Tests for Model_Games_Runningscore
 *
 * @group App
 * @group Model
 * @group Model_Games_Runningscore
 */
class Test_Model_Games_Runningscore extends \Test_Model_Base
{
	public function setUp()
	{
		parent::setUp();
	}

	public function tearDown()
	{
		parent::tearDown();
	}

	public function test_スキーマチェック()
	{
		$this->assertSchema();
	}

	public function test_registのテスト()
	{
		// 引数が足らない
		$res = Model_Games_Runningscore::regist();
		$this->assertFalse($res);

		// 登録
		$sample_id = Model_Game::find('last')->id;
		$res = Model_Games_Runningscore::regist($sample_id+1);

		$this->assertSame('Model_Games_Runningscore', get_class($res));

		// clean
		$res->delete();
	}
}
