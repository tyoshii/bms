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
	protected function setUp()
	{
		$this->setBrowserUrl('http://localhost:8888/');
	}

	public function test_edit_other()
	{
		// url
		$url = self::$sample['url']['edit'].'/other';

		// 未ログイン状態だと、loginページへ飛ばされる
		$this->url($url);
		$this->assertRegExp('/login/', $this->url());

		// team adminでログイン
		$this->login(self::$sample['username']);

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
