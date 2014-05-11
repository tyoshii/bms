<?php

class Model_Game extends \Orm\Model
{
	protected static $_properties = array(
		'id',
		'date',
		'team_top' => array( 'default' => 0 ),
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

      // games insert
      $game = self::forge(array(
        'date'             => $data['date'],
        'game_status'      => 1,
        'team_top'         => $data['top_name']    ? 0 : $data['top'],
        'team_top_name'    => $data['top_name']    ?: '',
        'team_bottom'      => $data['bottom_name'] ? 0 : $data['bottom'],
        'team_bottom_name' => $data['bottom_name'] ?: '',
      ));
  
      $game->save();
  
      // other table default value
      Model_Games_Runningscore::createNewGame($game->id);
      Model_Games_Stat::createNewGame($game->id, $data['top'], $data['bottom']);
  
      DB::commit_transaction();

    } catch ( Exception $e ) {
      DB::rollback_transaction();
      Session::set_flash('error', '内部処理エラー:'.$e->getMessage() );
      return false;
    }

    return true;
  }

  public static function getGames()
  {
    $query = DB::select(
      array( 'g.id', 'id' ),
      'g.id',
      'g.date',
      'g.game_status',
      'g.team_top',
      'g.team_bottom',
      'games_runningscores.tsum',
      'games_runningscores.bsum',
      DB::expr('(select name from teams as t where t.id = g.team_top) as team_top_name'),
      DB::expr('(select name from teams as t where t.id = g.team_bottom) as team_bottom_name')
    )->from(array('games', 'g'));

    $query->join('games_runningscores')->on('g.id', '=', 'games_runningscores.id');
  
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
