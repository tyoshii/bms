<?php

/**
 * Tests for Controler_Team_Game
 *
 * @group App
 * @group Controller
 * @group Controller_Team_Game
 */
class Test_Controller_Team_Game extends Test_Controller_Team_Base
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
	 */
	public function test_ゲーム一覧()
	{
		$res = $this->request(static::$sample['url']['team'].'/game');

		$this->assertSame(200, $res->status);
		$this->assertTrue(is_array($res->body->games));

		foreach ($res->body->games as $game )
		{
			$this->assertSame('Model_Game', get_class($game));
		}
	}

	/**
	 */
	public function test_ゲーム追加()
	{
		# no login状態で302
		$res = $this->request(static::$sample['url']['team'].'/game/add');
		$this->assertSame(302, $res->status);

		# チーム管理権限でログイン
		$this->login_by_team_admin(static::$sample['team']->id);

		# ゲーム追加ページの表示
		$res = $this->request(static::$sample['url']['team'].'/game/add');
		$this->assertSame(200, $res->status);
		$this->assertTrue(is_string($res->body->form));

		// post request

		// validation error
		$res = $this->request(static::$sample['url']['team'].'/game/add', 'POST', array('key' => 'value'));

		$this->assertSame(200, $res->status);
		$this->assertTrue(is_string(Session::get_flash('error')));
	}

	/**
	 */
	public function test_試合詳細()
	{
		$sample_game    = Model_Game::find('first');
		$sample_game_id = $sample_game->id;
		$res = $this->request(static::$sample['url']['team'].'/game/'.$sample_game_id);

		$this->assertSame(200, $res->status);
		$this->assertTrue(is_string($res->body->team_top));
		$this->assertTrue(is_string($res->body->team_bottom));
	}

	/**
	 */
	public function test_成績編集()
	{
		// TODO:

		$sample_game    = Model_Game::find('first');
		$sample_game_id = $sample_game->id;

		$paths = array(
			'score',
			'player',
			'other',
			'batter',
			'pitcher',
		);
		foreach ($paths as $path)
		{
			$res = $this->request(static::$sample['url']['team'].'/game/'.$sample_game_id.'/edit/'.$path);
		}
	}
}
