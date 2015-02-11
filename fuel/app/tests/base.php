<?php

abstract class Test_Base extends \TestCase
{
	public static $sample = array();

	public static function setUpBeforeClass()
	{
		parent::setUpBeforeClass();

		static::set_samples();
	}

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
		static::$sample[$key] = $value;
	}

	/**
	 * テスト用に、サンプルデータをセットする
	 * @param objecdt Model_Player
	 */
	public static function set_samples($player = null)
	{
		if (is_null($player))
		{
			$player = Model_Player::find('first');
		}

		$team       = Model_Team::find($player->team_id);
		$games_team = Model_Games_Team::find_by_team_id($team->id);
		$game       = Model_Game::find($games_team->id);

		$url = array(
			'team' => 'team/'.$team->url_path,
			'game' => 'team/'.$team->url_path.'/game/'.$game->id,
			'edit' => 'team/'.$team->url_path.'/game/'.$game->id.'/edit',
		);

		static::set_sample('username', $player->username);
		static::set_sample('player', $player);
		static::set_sample('team', $team);
		static::set_sample('game', $game);
		static::set_sample('url', $url);
	}

	/**
	 * 指定されたユーザーでログインする
	 * @param string username
	 */
	public static function login_by_username($username)
	{
		$id = Model_User::find_by_username($username)->id;
		Auth::force_login($id);
	}

	/**
	 * 指定されたグループのユーザーでログインする
	 * @parma integer group number(cf: config/simpleauth.php)
	 * @return boolean
	 */
	public static function login_by_group($group)
	{
		if ($user = Model_User::find_by_group($group))
		{
			Auth::force_login($user->id);
			return true;
		}

		Log::error('指定されたグループのユーザーが存在しません：group='.$group);
		return false;
	}

	/**
	 * チーム管理者でログインする
	 * 引数のteam_idがない場合には、適当なチームが選択される
	 * @param integer team_id
	 * @return boolean
	 */
	public static function login_by_team_admin($team_id = false)
	{
		$query = Model_Player::query()->where('role', 'admin');

		if ($team_id)
		{
			$query->where('team_id', $team_id);
		}

		if ($result = $query->get_one())
		{
			$id = Model_User::find_by_username($result->username)->id;
			Auth::force_login($id);

			return true;
		}

		Log::error('チーム管理者でのログインに失敗しました');
		return false;
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
