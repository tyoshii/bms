<?php

/**
 * Tests for Model_Stats_Hitting
 *
 * @group App
 * @group Model
 * @group Model_Stats_Hitting
 */
class Test_Model_Stats_Hitting extends \Test_Model_Base
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
		$this->assertException( function() {
			Model_Stats_Hitting::get_stats_by_playeds();
		});
		$this->assertException( function() {
			Model_Stats_Hitting::get_stats_by_playeds('game_id');
		});

		// 0件マッチ
		$res = Model_Stats_Hitting::get_stats_by_playeds('game_id', 'team_id');
		$this->assertSame(0, count($res));

		// 取得
		// TODO: 初期データのinsert
	}

	public function test_関数get_stats_total()
	{
		// 引数の足らないエラー
		$this->assertException( function() {
			Model_Stats_Hitting::get_stats_total();
		});
		$this->assertException( function() {
			Model_Stats_Hitting::get_stats_total('game_id');
		});

		// 取得
		$res = Model_Stats_Hitting::get_stats_total('game_id', 'team_id');
		$this->assertTrue(is_array($res));
	}
}
