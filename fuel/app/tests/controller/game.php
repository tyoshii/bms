<?php

/**
 * Tests for Controler_Game
 *
 * @group App
 * @group Controller
 * @group Controller_Game
 */
class Test_Controller_Game extends Test_Base
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
	 *
	 */
	public function test_成績入力_不正なURLで試合一覧に戻される()
	{
		$urls = array(
			// game_idとteam_idは数字じゃないと行けない
				'/game/game_id/batter/1',
				'/game/1/batter/team_id',

			// kindが不正な値
				'/game/1/dummy/1',
		);

		foreach ($urls as $url)
		{
			$res = Request::forge($url)->execute()->response();

			$this->assertSame('不正なURLです。試合一覧に戻されました。', Session::get_flash('error'));
			$this->assertSame(302, $res->status);
		}
	}

	/**
	 *
	 */
	public function test_成績入力_typeのチェック()
	{
		// parameter
		InputEx::reset();
		$_GET['type'] = 'all';

		// 権限のないrequest
		$res = Request::forge('/game/1/batter/1')->execute()->response();

		$this->assertSame('権限がありません', Session::get_flash('error'));
		$this->assertSame(302, $res->status);

		// moderator.moderator権限があればOK
		$id = Model_User::find_by_username('moderator')->id;
		Auth::force_login($id);

		InputEx::reset();
		$_GET['type'] = 'all';

		$res = Request::forge('/game/1/batter/1')->execute()->response();
		$this->assertSame(200, $res->status);
	}
}
