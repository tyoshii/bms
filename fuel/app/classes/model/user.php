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

  public static function regist()
  {
    try {
      // already check
      if ( Model_User::find_by_username(Input::post('username')) )
        throw new Exception('そのユーザー名は既に存在します。');
 
      if ( Model_User::find_by_email(Input::post('email')) )
        throw new Exception('そのメールアドレスは既に登録済みです。');
 
      // user create
      $result = Auth::create_user(
        Input::post('username'),
        Input::post('password'),
        Input::post('email'),
        Input::post('group'),
        array('dispname' => Input::post('name'))
      );
 
      if ( $result === false )
        throw new Exception('Internal Error');
 
      return true;
 
    } catch ( SimpleUserUpdateException $e ) {
      Session::set_flash('error', 'アカウントの作成に失敗しました：'.$e->getMessage());
      return false;

    } catch ( Exception $e ) {
      Session::set_flash('error', 'アカウントの作成に失敗しました：'.$e->getMessage());
      return false;
    }
  }
  
  // update is already
  public static function updates()
  {
    // グループ更新
    try {
      Auth::update_user(
        array('group' => Input::post('group')),
        Input::post('username')
      );

    } catch ( Exception $e ) {
      Session::set_flash('error', $e->getMessage());
      return false;
    }

    return true;
  }

  public static function disable()
  {
    $username = Input::post('username');
        
    if ( $username === Auth::get('username') )
    {
      Session::set_flash('error', '自分自身のアカウントは無効にできません');
      return false;
    }

    try {
      // 無効化（グループで操作）
      Auth::update_user(array('group' => -1), $uname );

    } catch ( Exception $e ) {
      Session::set_flash('error', $e->getMessage());
      return false;
    }
      
    return true;
  }
}
