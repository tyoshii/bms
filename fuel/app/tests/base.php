<?php

abstract class Test_Base extends \TestCase
{
	public static $sample = array();

	protected function setUp()
	{
		parent::setUp();
		Auth::logout();
	}

	protected function tearDown()
	{
		parent::tearDown();
	}

	public static function set_sample($key, $value)
	{
		self::$sample[$key] = $value;
	}

	public static function set_samples($username = null)
	{
		if (is_null($username))
		{
			$username = Model_User::find('first')->username;
		}

		$player     = Model_Player::find_by_username($username);
		$team       = Model_Team::find($player->team_id);
		$games_team = Model_Games_Team::find_by_team_id($team->id);
		$game       = Model_Game::find($games_team->id);

		$url = array(
			'team' => '/team/'.$team->url_path,
			'game' => '/team/'.$team->url_path.'/game/'.$game->id,
			'edit' => '/team/'.$team->url_path.'/game/'.$game->id.'/edit',
		);

		self::set_sample('username', $username);
		self::set_sample('player', $player);
		self::set_sample('team', $team);
		self::set_sample('game', $game);
		self::set_sample('url', $url);
	}

	public function login($username)
	{
		$id = Model_User::find_by_username($username)->id;
		Auth::force_login($id);
	}

	public function request($path, $method = 'GET', $param = array())
	{
		// rest
		Request::reset_request(true);
		FieldsetEx::reset();

		// request object
		$req = Request::forge($path)->set_method($method);

		if ($param)
		{
			InputEx::reset();
			$method === 'POST' ? $_POST = $param : $_GET = $param;
		}

		return $req->execute()->response();
	}

	public function get_property($class_name, $prop_name)
	{
		$class = new ReflectionClass($class_name);
		$prop  = $class->getProperty($prop_name);
		$prop->setAccessible(true);

		$orig = new $class_name;

		return $prop->getValue($orig);
	}

	public function assertSession($type, $message)
	{
		$this->assertSame($message, Session::get_flash($type));
	}

	public function assertRedirect($res, $location, $code = 302)
	{
		$this->assertSame($code, $res->status);
		$this->assertSame($location, $res->headers['Location']);
	}

	public function assertException($func)
	{
		try
		{
			$func();
			$this->assertTrue(false);
		}
		catch (Exception $e)
		{
			$this->assertTrue(true);
		}
	}
}
