<?php

abstract class Test_View_Base extends \PHPUnit_Extensions_Selenium2TestCase
{
	public static $browsers = array(
		/*
				array(
					'browserName' => 'safari',
					'host' => 'localhost',
					'port' => 4444,
				),
		*/
		array(
			'browserName' => 'firefox',
			'host'        => 'localhost',
			'port'        => 4444,
		),
	);

	public static $sample   = array();
	public static $url_base = '';

	public static function setUpBeforeClass()
	{
		parent::setUpBeforeClass();

		echo "\n\n";
		echo "Seleniumを使ったテストを実行します。\n";
		echo "このテストはSeleniumが起動していないとSkipされてしまいます。\n";
		echo "\n";

		static::set_samples();
	}

	protected function setUp()
	{
		$this->timeouts()->implicitWait(10000);
	}

	/**
	 * set sample datas
	 */
	public static function set_samples()
	{
		Test_Base::set_samples();
		static::$sample = Test_Base::$sample;
	}

	public function assertTitle()
	{
		$this->assertSame('Baseball Management System', $this->title());
	}

	/**
	 * 指定されたusernameでログインする
	 * @param string username
	 * @param string password(default:'password')
	 */
	public function login_by_username($username)
	{
		$this->logout();

		$this->url('/force_login/'.$username);
	}

	public function logout()
	{
		try {
			$this->url('/');
			$logout = $this->byCssSelector('a[role=logout]');
			$logout->click();
		}
		catch (Exception $e) {
		}
	}
}
