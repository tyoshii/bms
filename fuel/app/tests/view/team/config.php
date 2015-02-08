<?php

/**
 * Tests for View_Team_Config
 *
 * @group App
 * @group View
 * @group View_Team_Config
 */
class Test_View_Team_Config extends Test_View_Base
{
	protected function setUp()
	{
		$this->setBrowserUrl('http://localhost:8888/');
	}

	public function test_選手情報更新_正常系テスト()
	{
		// 管理者権限でログイン
		$this->login('player1');

		// 選手一覧に詳細カラムが追加されている
		$url = self::$sample['url']['team'].'/player';
		$this->url($url);

		$table = $this->byCssSelector('table.table thead tr');
		$this->assertRegExp('/編集/', $table->text());

		// request
		$url = self::$sample['url']['team'].'/config/player/'.self::$sample['player']->id;
		$this->url($url);

		// 自分のプロフィール編集画面
		$this->assertSame(self::$sample['player']->name, $this->byName('name')->value());	
		$this->assertSame(self::$sample['player']->number, $this->byName('number')->value());	

		// 同じチームの他人の編集画面
		// TODO:
/*
		$other = Model_Player::query()
			->where('team_id', self::$sample['team']->id)
			->where('player_id', '!=', self::$sample['player']->id)
			->get_one()->id;
*/
	}
}
