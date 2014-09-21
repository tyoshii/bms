<?php

/**
 * Tests for View_Team
 *
 * @group App
 * @group View
 * @group View_Team
 */
class Test_View_Team extends Test_View_Base
{
	protected function setUp()
	{
		$this->setBrowserUrl('http://localhost:8888/');
	}

	public function test_未ログイン状態で正常系テスト()
	{
		// 検索ページ
		$this->url('/team');

		// 検索フォームがあるかチェック
		$this->byCssSelector('form.form-inline[role=search]');

		// チームのページへ
		$team = Model_Team::find('first');
		$this->url('/team/'.$team->url_path);

		// チーム名
		$team_name = $this->byCssSelector('div#team-name h1 a');
		$this->assertSame($team->name, $team_name->text());

		// サイドメニュー
		$team_info = $this->byCssSelector('label#menu-team-info');
		$this->assertSame('チーム情報', $team_info->text());

		$offer = $this->byCssSelector('label#menu-offer');
		$this->assertSame('オファー', $offer->text());

		// 試合一覧/所属選手/成績 へ正常に異動
		$base_url = $this->url();
		foreach (array('game', 'player', 'stats') as $kind)
		{
			$url = $base_url.'/'.$kind;
			$this->url($url);
			$this->assertSame($url, $this->url());
			$this->assertTitle();
		}

		// $this->byName('username')->value('cannot-login-user');
		// $this->byName('password')->value('dummy');
		// $this->byName('login')->submit();

		// $alert = $this->byCssSelector('div.alert[role=alert-error] span');
		// $this->assertSame('Error! ログインに失敗しました', $alert->text());
		// $this->assertStringEndsWith('/login', $this->url());
	}
}
