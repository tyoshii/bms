<?php

/**
 * Tests for Model_Conventions_Team
 *
 * @group App
 * @group Model
 * @group Model_Conventions_Team
 */
class Test_Model_Conventions_Team extends \Test_Model_Base
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
	public function test_get_teamsチーム情報の取得()
	{
		// 基本はModel_Team::get_teamsのラッパー
		$this->assertSame(
			Model_Team::get_teams(),
			Model_Conventions_Team::get_teams()
		);

		// convention_idを付与すると、大会参加中のチームにentriedキーを付与してくれる
		$id = Model_Convention::find('first')->id;
		$teams = Model_Conventions_Team::get_teams();
		$this->assertTrue(is_array(Model_Conventions_Team::get_teams()));

		// パラメーターが足りない場合はfalse
		$this->assertFalse(Model_Team::regist());
		$this->assertFalse(Model_Team::regist(array('name' => 'name')));
		$this->assertFalse(Model_Team::regist(array('url_path' => 'url_path')));

		// 新規登録
		$props = array(
			'name'     => rand(),
			'url_path' => rand(),
		);
		$id = Model_Team::regist($props);

		// 登録したチームのモデル
		$team = Model_Team::find($id);

		$this->assertSame($id, $team->id);
		$this->assertSame($props['name'], $team->name);

		// clean up
		unset($team->players);
		$this->assertTrue(is_object($team->delete()));
	}
}
