<?php

/**
 * Tests for Model_Game
 *
 * @group App
 * @group Model
 * @group Model_Game
 */
class Test_Model_Game extends \Test_Model_Base
{
	public function setUp()
	{
	}

	public function tearDown()
	{
	}

	/**
	 *
	 */
	public function test_スキーマチェック()
	{
		$this->assertSchema();
	}

	/**
	 *
	 */
	public function test_get_infoで試合情報の一覧を取得します()
	{
		$games = Model_Game::get_info();

		foreach ($games as $game_id => $game)
		{
			// returnの型
			$this->assertSame('Model_Game', get_class($game));

			// DBからの戻り値以外で付与した属性があるかどうか
			$this->assertTrue(isset($game->tsum));
			$this->assertTrue(isset($game->bsum));
			$this->assertTrue(isset($game->own));
			$this->assertTrue(isset($game->top_result));
			$this->assertTrue(isset($game->bottom_result));
			$this->assertTrue(isset($game->result));

			// relationのデータ
			$this->assertTrue(isset($game->games_runningscore));
			$this->assertTrue(isset($game->games_team));
			$this->assertTrue(isset($game->games_teams));
			$this->assertTrue(isset($game->stats_players));
		}
	}

	/**
	 *
	 */
	public function test_get_info_by_team_idで値指定しなければ空配列()
	{
		$games = Model_Game::get_info_by_team_id();

		$this->assertTrue(is_array($games));
		$this->assertSame(0, count($games));
	}

	/**
	 *
	 */
	public function test_get_info_by_team_idで正しい値が返る()
	{
		$id = Model_Team::find('first')->id;
		$games = Model_Game::get_info_by_team_id($id);

		$this->assertTrue(is_array($games));

		// チーム情報が$idであること
		foreach ($games as $game)
		{
			$this->assertSame($game->games_team->team_id, $id);
		}
	}
}
