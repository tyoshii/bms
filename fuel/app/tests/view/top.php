<?php

/**
 * Tests for View_Top
 *
 * @group App
 * @group View
 * @group View_Top
 */
class Test_View_Top extends Test_View_Base
{
	protected function setUp()
	{
		$this->setBrowserUrl('http://localhost:8888/');

	}

	public function test_ログイン失敗()
	{
		// login
		$this->url('/');
		$this->byName('email')->value('cannot-login-user');
		$this->byName('password')->value('dummy');
		$this->byName('login')->submit();

		// ログイン失敗のメッセージ
		$alert = $this->byCssSelector('div.alert[role=alert-error] span');
		$this->assertSame('Error! ログインに失敗しました', $alert->text());

		// ログイン専用ページに飛ばされる
		$this->assertStringEndsWith('/login', $this->url());
	}
}
