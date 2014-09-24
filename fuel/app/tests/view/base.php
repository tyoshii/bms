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

		// sample data
		Test_Base::set_samples('player1');
		self::$sample = Test_Base::$sample;
	}

	protected function setUp()
	{
		$this->timeouts()->implicitWait(10000);
	}

	public function assertTitle()
	{
		$this->assertSame('Baseball Management System', $this->title());
	}

	public function login($username, $password = 'password')
	{
		$this->logout();

		$this->url('/');
    $this->byName('username')->value($username);
    $this->byName('password')->value($password);
    $this->byName('login')->submit();
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
