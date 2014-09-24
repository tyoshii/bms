<?php

/**
 * Tests for Model_Stats_Award
 *
 * @group App
 * @group Model
 * @group Model_Stats_Award
 */
class Test_Model_Stats_Award extends \Test_Model_Base
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

	public function test_get_statsのテスト()
	{
		// 引数が足らない
		$this->assertException(function() {
			Model_Stats_Award::get_stats();
		});
		$this->assertException(function() {
			Model_Stats_Award::get_stats('game_id');
		});

		$sample_game_id = Model_Game::find('first')->id;
		$sample_team_id = Model_Team::find('first')->id;

		$res = Model_Stats_Award::get_stats($sample_game_id, $sample_team_id);

		$this->assertTrue(is_array($res));

		$this->assertTrue(array_key_exists('mvp_player_id',          $res));
		$this->assertTrue(array_key_exists('mvp_player_name',        $res));
		$this->assertTrue(array_key_exists('second_mvp_player_id',   $res));
		$this->assertTrue(array_key_exists('second_mvp_player_name', $res));
	}

	public function test_registのテスト()
	{
		// 引数が足らない
		$this->assertException(function() {
			Model_Stats_Award::regist();
		});
		$this->assertException(function() {
			Model_Stats_Award::regist('game_id');
		});
		$this->assertException(function() {
			Model_Stats_Award::regist('game_id', 'team_id');
		});

		$sample_game_id = Model_Game::find('first')->id;
		$sample_team_id = Model_Team::find('first')->id;
		$sample_stats   = array();

		$res = Model_Stats_Award::regist($sample_game_id, $sample_team_id, $sample_stats);

		$this->assertSame('Model_Stats_Award', get_class($res));
		$this->assertSame($sample_game_id, $res->game_id);
		$this->assertSame($sample_team_id, $res->team_id);
	}
}
