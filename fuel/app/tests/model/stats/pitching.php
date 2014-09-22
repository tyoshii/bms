<?php

/**
 * Tests for Model_Stats_Pitching
 *
 * @group App
 * @group Model
 * @group Model_Stats_Pitching
 */
class Test_Model_Stats_Pitching extends \Test_Model_Base
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

	public function test_関数get_stats_by_playeds()
	{
		// 引数の足らないエラー
		$this->assertException(function ()
		{
			Model_Stats_Pitching::get_stats_by_playeds();
		});
		$this->assertException(function ()
		{
			Model_Stats_Pitching::get_stats_by_playeds('game_id');
		});

		// 0件マッチ
		$res = Model_Stats_Pitching::get_stats_by_playeds('game_id', 'team_id');
		$this->assertSame(0, count($res));

		// 取得
		// TODO: 初期データのinsert
	}
}
