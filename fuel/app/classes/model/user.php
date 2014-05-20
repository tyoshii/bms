<?php

class Model_User extends \Orm\Model
{
	protected static $_properties = array(
		'id',
		'username',
		'password',
		'group',
		'email',
		'last_login',
		'login_hash',
		'profile_fields',
		'created_at',
		'updated_at',
	);

	protected static $_observers = array(
		'Orm\Observer_CreatedAt' => array(
			'events' => array('before_insert'),
			'mysql_timestamp' => false,
		),
		'Orm\Observer_UpdatedAt' => array(
			'events' => array('before_update'),
			'mysql_timestamp' => false,
		),
	);
	protected static $_table_name = 'users';

  public static function get_noregist_player_user()
  {
    $users = DB::select('u.username')
              ->from(array(self::$_table_name, 'u'))
                ->join(array('players', 'p'), 'LEFT')
                ->on('u.username', '=', 'p.username')
              ->where('p.username', null)
              ->where('u.username', '!=', 'admin')
              ->execute()->as_array('username');

    return array_keys($users);
  }
}
