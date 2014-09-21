<?php

/**
 * Tests for Controler_Team
 *
 * @group App
 * @group Controller
 * @group Controller_Team
 */
class Test_Controller_Team extends Test_Base
{
	public static $team_url = '';

	public static function setUpBeforeClass()
	{
		self::$team_url = '/team/'.Model_Team::find('first')->url_path;
	}

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
	public function test_チームトップページ()
	{
		$res = Request::forge(self::$team_url)->execute()->response();

		// assertion
		$this->assertSame(200, $res->status);

		foreach ($res->body->games as $game)
		{
			$this->assertTrue(is_object($game));
		}
	}

	/**
	 */
	public function test_チーム検索()
	{
		$res = $this->request('/team');

		// assertion
		$this->assertSame(200, $res->status);

		foreach ($res->body->teams as $team)
		{
			$this->assertTrue(is_object($team));
		}

		// 検索クエリセット
		$sample_team_name = Model_Team::find('first')->name;

		InputEx::reset();
		$_GET['query'] = $sample_team_name; 

		$res = $this->request('/team');

		// assertion
		$this->assertSame(200, $res->status);

		foreach ($res->body->teams as $team)
		{
			$this->assertRegExp('/'.$sample_team_name.'/', $team->name);
		}
	}

	/**
	 */
	public function test_チーム新規登録()
	{
		$res = $this->request('/team/regist');

		$this->assertSame(200, $res->status);
		$this->assertTrue(is_string($res->body->form));

		// post request

		// no paramter / validation error
		$res = $this->request('/team/regist', 'POST', array('key' => 'value'));
		$this->assertTrue(is_string(Session::get_flash('error')));
	}

	/**
	 */
	public function test_選手一覧_個人()
	{
		$res = $this->request(self::$team_url.'/player');

		$this->assertSame(200, $res->status);
		$this->assertTrue(is_array($res->body->players));

		// 選手個人の情報
		$sample_team_id = Model_Team::find('first')->id;
		$sample_player_id = Model_Player::find_by_team_id($sample_team_id)->id;

		$res = $this->request(self::$team_url.'/player/'.$sample_player_id);

		$this->assertSame(200, $res->status);
		$this->assertSame('Model_Player', get_class($res->body->player));

		// 存在しない選手
		$res = $this->request(self::$team_url.'/player/not_exist_player');

		$this->assertSame(302, $res->status);
		$this->assertSame('選手情報が取得できませんでした', Session::get_flash('error'));
	}

	/**
	 */
	public function test_成績()
	{
		$res = $this->request(self::$team_url.'/stats');

		$this->assertSame(200, $res->status);

		$this->assertTrue(is_array($res->body->result));
		$this->assertTrue(is_array($res->body->stats['teams']));
		$this->assertTrue(is_array($res->body->stats['selfs']));
	}

	/**
	 */
	public function test_オファー()
	{
		$res = $this->request(self::$team_url.'/offer');

		$this->assertSame(200, $res->status);
	}
}
