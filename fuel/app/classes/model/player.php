<?php

class Model_Player extends \Orm\Model
{
  protected static $_properties = array(
      'id',
      'team_id',
      'name',
      'number',
      'username',
      'status' => array(
          'default' => 1,
      ),
      'role'   => array(
          'default' => 'user',
      ),
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
  protected static $_table_name = 'players';

  protected static $_belongs_to = array(
      'teams' => array(
          'model_to'       => 'Model_Team',
          'key_from'       => 'team_id',
          'key_to'         => 'id',
          'cascade_save'   => false,
          'cascade_delete' => false,
      ));

  public static function get_name_by_username($username = null)
  {
    if ( ! $username)
      return null;

    if ($player = self::find_by_username($username))
      return $player->name;

    return null;
  }

  public static function get_my_player_id()
  {
    if ($res = self::find_by_username(Auth::get_screen_name()))
      return $res->id;

    return null;
  }

  public static function get_my_team_name()
  {
    if ($team_id = self::get_my_team_id())
    {
      return Model_Team::find($team_id)->name;
    }
  }

  public static function get_my_team_id()
  {
    if ($res = self::find_by_username(Auth::get_screen_name()))
      return $res->team_id;

    return null;
  }

  public static function get_players($team_id = null)
  {
    $query = DB::select('p.*', array('teams.name', 'teamname'))
        ->from(array(self::$_table_name, 'p'))
        ->join('teams', 'LEFT')->on('p.team_id', '=', 'teams.id')
        ->where('p.status', '!=', -1)
        ->order_by(DB::expr('CAST(p.number as SIGNED)'));

    if ($team_id)
      $query->where('p.team_id', $team_id);

    return $query->execute()->as_array();
  }

  /**
   * 選手登録
   *
   * @param $props
   * @param array properties
   *              - team_id
   *              - name
   *              - number
   *              - username
   *
   * @return bool
   */
  public static function regist($props, $id = null)
  {
    try
    {
      $player = $id ? self::find($id) : self::forge();

      // 既に登録されたusernameかチェック
      if ($props['username'] and $props['username'] !== $player->username)
      {
        $already = self::query()->where(array(
            array('username', $props['username']),
            array('team_id', $props['team_id']),
        ))->get();

        if ($already)
        {
          throw new Exception('そのユーザーは既に他の選手に紐づいています');
        }
      }

      // 登録/更新
      $player->set($props);
      $player->save();

      return true;

    } catch (Exception $e)
    {
      Session::set_flash('error', $e->getMessage());
      return false;
    }
  }

  public static function disable($id)
  {
    try
    {
      $player = self::find($id);

      $player->number = '';
      $player->username = '';
      $player->status = -1;

      $player->save();

      return true;

    } catch (Exception $e)
    {
      Session::set_flash('error', $e->getMessage());
      return false;
    }
  }

  public static function get_player_email($player_id)
  {
    $user = DB::select()
        ->from(array(self::$_table_name, 'p'))
        ->join(array('users', 'u'))->on('p.username', '=', 'u.username')
        ->where('p.id', $player_id)
        ->limit(1)
        ->execute()->as_array();

    $user = $user[0];

    if ($user['username'] === '')
    {
      return '';
    }

    return $user['email'];
  }

  /**
   * チームの管理者権限をもっているかどうか
   *
   * @param string team_id
   *
   * @return boolean
   */
  public static function has_team_admin($team_id)
  {
    $res = self::query()->where(array(
        array('username', Auth::get_screen_name()),
        array('team_id', $team_id)
    ))->get_one();

    return $res and $res->role === 'admin';
  }

  /**
   * player.roleを更新
   *
   * @param string team_id
   * @param string player_id
   * @param string role
   *
   * @return boolean
   */
  public static function update_role($team_id, $player_id, $role)
  {
    $player = self::find($player_id, array(
        'where' => array(array('team_id', $team_id)),
    ));

    if ( ! $player)
    {
      Log::error('選手が存在しません');
      return false;
    }

    if ( ! in_array($role, array('user', 'admin')))
    {
      Log::error('存在しないroleです');
      return false;
    }

    // update
    $player->role = $role;
    $player->save();

    return true;
  }
}
