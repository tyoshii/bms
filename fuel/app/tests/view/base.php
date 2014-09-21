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

	public static function setUpBeforeClass()
	{
		echo "\n\n";
		echo "Seleniumを使ったテストを実行します。\n";
		echo "このテストはSeleniumが起動していないとSkipされてしまいます。\n";
		echo "\n";
	}

	protected function setUp()
	{
		$this->timeouts()->implicitWait(10000);
	}

	public function assertTitle()
	{
		$this->assertSame('Baseball Management System', $this->title());
	}
}
