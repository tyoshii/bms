<?php

/**
 * Tests for Model_Games_Team
 *
 * @group App
 * @group Model
 * @group Model_Games_Team
 */
class Test_Model_Games_Team extends \Test_Model_Base
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

		// game_idがない
		$res = Model_Games_Team::regist();
		$this->assertFalse($res);

		// team_idがない
		$res = Model_Games_Team::regist(array('game_id' => 'game_id'));
		$this->assertFalse($res);

		// opponent_team_id or opponent_team_name がない
		$res = Model_Games_Team::regist(array(
			'game_id' => 'game_id',
			'team_id' => 'team_id',
		));
		$this->assertFalse($res);
		
		// 存在しないteam_id
		$res = Model_Games_Team::regist(array(
			'game_id'          => 'game_id',
			'team_id'          => 'team_id',
			'opponent_team_id' => 'opponent_team_id',
		));
		$this->assertFalse($res);
		
		// 存在しないopponent_team_id
		$res = Model_Games_Team::regist(array(
			'game_id'          => 'game_id',
			'team_id'          => self::$sample['team']->id,
			'opponent_team_id' => 'opponent_team_id',
		));
		$this->assertFalse($res);


		// 登録
		$res = Model_Games_Team::regist(array(
			'game_id'          => self::$sample['game']->id,
			'team_id'          => self::$sample['team']->id,
			'opponent_team_id' => self::$sample['team']->id,
		));

		$this->assertSame('Model_Games_Team', get_class($res));

		// clean
		$res->delete();
	}
}
