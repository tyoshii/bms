<?php

abstract class Test_Controller_Team_Base extends Test_Base
{
	public static $team_url = '';

	public static function setUpBeforeClass()
	{
		parent::setUpBeforeClass();

		self::$team_url = 'team/'.Model_Team::find('first')->url_path;
	}
}
