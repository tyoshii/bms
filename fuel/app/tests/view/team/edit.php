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

	public function test_保存と登録が動くことをテスト()
	{
		// login
		$this->login(self::$sample['username']);

		// score
		$this->url(self::$sample['url']['edit'].'/score');
		$this->byCssSelector("div.stats-post[role=score] button")->click();
		$this->assertSame('成績が保存/登録されました。', $this->alertText());
		$this->acceptAlert();
		
		// other
		$this->url(self::$sample['url']['edit'].'/score');
		$this->byCssSelector("div.stats-post[role=other] button")->click();
		$this->assertSame('成績が保存/登録されました。', $this->alertText());
		$this->acceptAlert();

		// 選手
		// $this->url(self::$sample['url']['edit'].'/player');
		// $this->byCssSelector("div button:last")->click();
		// $this->assertSame('成績が保存/登録されました。', $this->alertText());
		// $this->acceptAlert();

		// TODO: その他のページのテスト
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
