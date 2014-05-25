<?php

class Model_Game extends \Orm\Model
{
	protected static $_properties = array(
		'id',
		'date',
		'team_top',
		'team_top_name',
		'team_bottom',
		'team_bottom_name',
		'game_status',
    'top_status' => array(
      'default' => 1,
    ),
    'bottom_status' => array(
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
	protected static $_table_name = 'games';

  protected static $_has_many = array('games' => array(
    'model_to' => 'Model_Game',
    'key_from' => 'id',
    'key_to' => 'id',
    'cascade_save' => true,
    'cascade_delete' => false,
  ));

  public static function createNewGame($data)
  {
    try {
      DB::start_transaction();

      // チーム名
      $team_top_name    = $data['top_name'] ?: Model_Team::find($data['top'])->name;
      $team_bottom_name = $data['bottom_name'] ?: Model_Team::find($data['bottom'])->name;
      // games insert
      $game = self::forge(array(
        'date'             => $data['date'],
        'game_status'      => 1,
        'team_top'         => $data['top'] ?: 0,
        'team_top_name'    => $team_top_name,
        'team_bottom'      => $data['bottom'] ?: 0,
        'team_bottom_name' => $team_bottom_name,
      ));
  
      $game->save();
  
      // other table default value
      Model_Games_Runningscore::createNewGame($game->id);
      Model_Stats_Player::createNewGame($game->id, $data['top']);
      Model_Stats_Player::createNewGame($game->id, $data['bottom']);

      // json data のデフォルト値
      // - TODO なくしたい
      Model_Games_Stat::createNewGame($game->id, $data['top'], $data['bottom']);
  
      DB::commit_transaction();

    } catch ( Exception $e ) {
      DB::rollback_transaction();
      Session::set_flash('error', '内部処理エラー:'.$e->getMessage() );
      return false;
    }

    return true;
  }

  public static function getGameInfo($game_id)
  {
    $query = self::_getGamesQuery();

    $query->where('games.id', $game_id);

    return $query->execute()->as_array();
  }

  public static function getGames()
  {
    $query  = self::_getGamesQuery();
    $result = $query->execute()->as_array();

    // add value
    foreach ( $result as $index => $res )
    {
      // ログインしている場合、自分のチームの試合にflag
      // - 加えて、game.statusをセット
      $result[$index]['own'] = false;

      if ( Auth::has_access('admin.admin') )
      {
        $result[$index]['own']    = 'admin';
        $result[$index]['status'] = $result[$index]['game_status'];
      }

      if ( $team_id = Model_Player::getMyTeamId() )
      {
        if ( $res['team_top'] == $team_id ) 
        {
          $result[$index]['own']    = 'top';
          $result[$index]['status'] = $result[$index]['top_status'];
        }
        else if ( $res['team_bottom'] == $team_id )
        {
          $result[$index]['own']    = 'bottom';
          $result[$index]['status'] = $result[$index]['bottom_status'];
        }
      }
      
      // 試合結果を配列に付与
      if ( $res['tsum'] > $res['bsum'] )
      {
        $result[$index]['top_result'] = '○';
        $result[$index]['bottom_result'] = '●';
          
        $result[$index]['result'] = $result[$index]['own'] === 'top' ? 'win' : 'lose';
      }
      else if ( $res['tsum'] < $res['bsum'] )
      {
        $result[$index]['top_result'] = '●';
        $result[$index]['bottom_result'] = '○';

        $result[$index]['result'] = $result[$index]['own'] === 'top' ? 'lose' : 'win';
      }
      else
      {
        $result[$index]['top_result'] = '△';
        $result[$index]['bottom_result'] = '△';

        $result[$index]['result'] = 'even';
      }

    }

    return $result;
  }

  public static function getGamesOnlyMyTeam()
  {
    $result = array();

    if ( Auth::check() && $team_id = Model_Player::getMyTeamId() )
    {
      $query  = self::_getGamesQuery();

      $query->where_open();
      $query->or_where('team_top', $team_id);
      $query->or_where('team_bottom', $team_id);
      $query->where_close();

      $result = $query->execute()->as_array();
    }

    return $result;
  }

  private static function _getGamesQuery()
  {
    $query = DB::select()->from(self::$_table_name);

    $query->join('games_runningscores')->on('games.id', '=', 'games_runningscores.id');
  
    $query->where('game_status', '!=', 0);
    $query->order_by('date', 'desc');
    
    return $query;
  }

}
