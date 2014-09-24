<?php

/**
 * Tests for View_Team_Edit
 *
 * @group App
 * @group View
 * @group View_Team_Edit
 */
class Test_View_Team_Edit extends Test_View_Base
{
	public static $username;
	public static $sample_player;
	public static $sample_team;
	public static $sample_game;
	public static $url_base;

	public static function setUpBeforeClass()
	{
		self::$username      = 'player1';
		self::$sample_player = Model_Player::find_by_username('player1');
		self::$sample_team   = Model_Team::find(self::$sample_player->team_id);

		$games_team = Model_Games_Team::find_by_team_id(self::$sample_team->id);
		self::$sample_game = Model_Game::find($games_team->id);

		// url_base
		self::$url_base = 'team/'.self::$sample_team->url_path.'/game/'.self::$sample_game->id.'/edit';
	}

	protected function setUp()
	{
		$this->setBrowserUrl('http://localhost:8888/');
	}

	public function test_edit_other()
	{
		// url
		$url = self::$url_base.'/other';

		// 未ログイン状態だと、loginページへ飛ばされる
		$this->url($url);
		$this->assertRegExp('/login/', $this->url());

		// team adminでログイン
		$this->byName('username')->value(self::$username);
		$this->byName('password')->value('password');
		$this->byName('login')->submit();

		// request
		$this->url($url);

		// assert
		$remind= $this->byCssSelector('a[role=remind]');
		$this->assertSame('成績入力のリマインドをメールで送信', $remind->text());

		$remind= $this->byCssSelector('a[role=download]');
		$this->assertRegExp('/成績ダウンロード/', $remind->text());

		// logout
		$this->logout();
	}
}
