<?php

/**
 * Tests for Controler_Api_Download
 *
 * @group App
 * @group Controller
 * @group Controller_Api_Download
 */
class Test_Controller_Api_Download extends Test_Base
{
	protected function setUp()
	{
		parent::setUp();
	}

	protected function tearDown()
	{
		parent::tearDown();
	}

	/**
	 *
	 */
	public function test_validationエラー()
	{
		$res = $this->request('/api/download/stats/itleague');
		$this->assertRedirect($res, 'error/400');
		
		$res = $this->request('/api/download/stats/itleague', 'GET', array('game_id' => 1));
		$this->assertRedirect($res, 'error/400');
		
		$res = $this->request('/api/download/stats/itleague', 'GET', array('team_id' => 1));
		$this->assertRedirect($res, 'error/400');
	}

	public function test_正常系()
	{
		// TODO
		$param = array(
			'game_id' => Model_Games_Team::find('first')->game_id,
			'team_id' => Model_Games_Team::find('first')->team_id,
		);

		// Fuel\Core\PhpErrorException: Cannot modify header information - headers already sent by
		// $res = $this->request('/api/download/stats/itleague', 'GET', $param);
	}
}
