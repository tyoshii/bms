<?php

class Model_Player extends \Orm\Model
{
	protected static $_properties = array(
		'id',
		'team',
		'name',
		'number',
    'username',
    'status' => array(
      'default' => 1,
    ),
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
	protected static $_table_name = 'players';

  protected static $_belongs_to = array(
    'teams' => array(
      'model_to' => 'Model_Team',
      'key_from' => 'team',
      'key_to' => 'id',
      'cascade_save' => true,
      'cascade_delete' => false,
    ));

  public static function get_name_by_username($username = null)
  {
    if ( ! $username )
      return null;

    if ( $player = self::find_by_username($username) )
      return $player->name;

    return null;
  }

  public static function getMyPlayerID()
  {
    if ( $res = self::find_by_username(Auth::get_screen_name()) )
      return $res->id;
      
    return null;
  }

  public static function get_my_team_name()
  {
    if ( $team_id = self::getMyTeamId() )
    {
      return Model_Team::find($team_id)->name;
    }
  }

  public static function getMyTeamId()
  {
    if ( $res = self::find_by_username(Auth::get_screen_name()) )
      return $res->team;
      
    return null;
  }

  public static function get_players($team_id = null)
  {
    $query = DB::select('p.*', array('teams.name', 'teamname'))
              ->from(array(self::$_table_name, 'p'))
              ->join('teams', 'LEFT')->on('p.team', '=', 'teams.id')
              ->where('p.status', '!=', -1) 
              ->order_by( DB::expr('CAST(p.number as SIGNED)') );

    if ( $team_id )
      $query->where('p.team', $team_id);

    return $query->execute()->as_array();
  }

  public static function regist($props, $id = null)
  {
    try {
      $player = $id ? self::find($id) : self::forge();

      // 既に登録されたusernameかチェック
      if ( $props['username'] and 
           $props['username'] !== $player->username and
           self::find_by_username($props['username']) )
      {
        throw new Exception('そのユーザーは既に他の選手に紐づいています');
      }

      // 背番号のダブリをチェック
      if ( $props['number'] !== $player->number )
      {
        if ( self::query()
              ->where('team', $props['team'])
              ->where('number', $props['number'])
              ->get_one() )
        {
          throw new Exception('その背番号は既に使われています');
        }
      }
  
      // 登録/更新
      $player->set($props);
      $player->save();

      return true;

    } catch ( Exception $e ) {
      Session::set_flash('error', $e->getMessage());
      return false;
    }
  }

  public static function disable($id)
  {
    try {
      $player = self::find($id);
  
      $player->number   = '';
      $player->username = '';
      $player->status   = -1;
  
      $player->save();

      return true;
    
    } catch ( Exception $e ) {
      Session::set_flash('error', $e->getMessage());
      return false;
    }
  }

  public static function get_player_email($player_id)
  {
    $user = DB::select()
      ->from( array(self::$_table_name, 'p') )
      ->join( array('users', 'u') )->on('p.username', '=', 'u.username')
      ->where('p.id', $player_id)
      ->limit(1)
      ->execute()->as_array();

    $user = $user[0];

    if ( $user['username'] === '' )
    {
      return '';
    }

    return $user['email'];
  }
}
