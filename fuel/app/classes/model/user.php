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
          'events'          => array('before_insert'),
          'mysql_timestamp' => false,
      ),
      'Orm\Observer_UpdatedAt' => array(
          'events'          => array('before_update'),
          'mysql_timestamp' => false,
      ),
  );
  protected static $_table_name = 'users';

  public static function get_username_list()
  {
    $users = DB::select()
        ->from(self::$_table_name)
        ->execute()->as_array();

    $return = array();
    foreach ($users as $user)
    {
      $return[$user['username']] = $user['username'];
    }

    return $return;
  }

  public static function regist()
  {
    try
    {
      // already check
      if (Model_User::find_by_username(Input::post('username')))
        throw new Exception('そのユーザー名は既に存在します。');

      if (Model_User::find_by_email(Input::post('email')))
        throw new Exception('そのメールアドレスは既に登録済みです。');

      // user create
      $result = Auth::create_user(
          Input::post('username'),
          Input::post('password'),
          Input::post('email'),
          Input::post('group') ? : 1,
          array('dispname' => Input::post('name'))
      );

      if ($result === false)
        throw new Exception('Internal Error');

      return true;

    } catch (SimpleUserUpdateException $e)
    {
      Session::set_flash('error', 'アカウントの作成に失敗しました：' . $e->getMessage());
      return false;

    } catch (Exception $e)
    {
      Session::set_flash('error', 'アカウントの作成に失敗しました：' . $e->getMessage());
      return false;
    }
  }

  private static function _update($username, $values)
  {
    // グループ更新
    try
    {
      Auth::update_user($values, $username);

    } catch (Exception $e)
    {
      Session::set_flash('error', $e->getMessage());
      return false;
    }

    return true;
  }

  public static function update_group($username, $group)
  {
    return self::_update($username, array(
        'group' => $group,
    ));
  }

  public static function disable($username)
  {
    if ($username === Auth::get('username'))
    {
      Session::set_flash('error', '自分自身のアカウントは無効にできません');
      return false;
    }

    // 無効化（グループで操作）
    return self::update_group($username, -1);
  }

  public static function get_users_only_my_team()
  {
    $team_id = Model_Player::get_my_team_id();
    if ( ! $team_id) return array();

    $players = Model_Player::query()
        ->where('team_id', $team_id)
        ->where('username', '!=', '')
        ->get();

    $usernames = array();
    foreach ($players as $player)
      $usernames[] = $player->username;

    return DB::select()->from(self::$_table_name)
        ->where('username', 'in', $usernames)
        ->execute()->as_array();
  }
}
