<?php

/**
 * Tests for Controler_Team_Config
 *
 * @group App
 * @group Controller
 * @group Controller_Team_Config
 */
class Test_Controller_Team_Config extends Test_Base
{
	public static $team_url = '';

	public static function setUpBeforeClass()
	{
		parent::setUpBeforeClass();

		self::$team_url = 'team/'.Model_Team::find('first')->url_path;
	}

  protected function setUp()
  {
    parent::setUp();
  }
  protected function tearDown()
  {
    parent::tearDown();
  }

	/**
	 */
	public function test_正常系()
	{
		// admin権限でログイン
		$this->login('player1');
		
		$paths = array(
			'info',
			'admin',
			'delete',
			'profile',
			'leave',
		);
		foreach ($paths as $path)
		{
			$res = $this->request(self::$sample['url']['team'].'/config/'.$path);
			$this->assertSame(200, $res->status);
		}

		// 存在しないconfig
		$res = $this->request(self::$team_url.'/config/not_exist_config');

		$this->assertSession('error', '存在しないURLです');
		$this->assertRedirect($res, self::$team_url);
	}
}
