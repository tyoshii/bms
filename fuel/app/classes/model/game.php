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

  public static function getGameInfo()
  {

  }

  public static function getGames()
  {
    $query = DB::select()->from(self::$_table_name);

    $query->join('games_runningscores')->on('games.id', '=', 'games_runningscores.id');
  
    $query->where('game_status', '!=', 0);
    $query->order_by('date', 'desc');
    
    $result = $query->execute()->as_array();

    // ログインしている場合、自分のチームの試合にflag
    if ( Auth::check() && $team_id = Model_Player::getMyTeamId() )
    {
      foreach ( $result as $index => $res )
      {
        if ( $res['team_top']    == $team_id ||
             $res['team_bottom'] == $team_id )
        {
          $result[$index]['own'] = 1;
        }
        else
        {
          $result[$index]['own'] = 0;
        }
      }
    }

    return $result;
  }
}
