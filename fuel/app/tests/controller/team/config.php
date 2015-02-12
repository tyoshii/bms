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
	public static function setUpBeforeClass()
	{
		parent::setUpBeforeClass();
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
		// チーム管理者でログイン
		$this->login_by_team_admin();
		
		$paths = array(
			'info',
			'player',
			'admin',
			'delete',
			'leave',
		);
		foreach ($paths as $path)
		{
			$res = $this->request(self::$sample['url']['team'].'/config/'.$path);
			$this->assertSame(200, $res->status);
		}

		// 存在しないconfig
		$res = $this->request(self::$sample['url']['team'].'/config/not_exist_config');

		$this->assertSession('error', '存在しないURLです');
		$this->assertRedirect($res, self::$sample['url']['team']);
	}
}
