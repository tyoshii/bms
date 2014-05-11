<?php

class Model_Game extends \Orm\Model
{
	protected static $_properties = array(
		'id',
		'date',
		'team_top',
		'team_bottom',
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

  public static function createNewGame( $top, $bottom, $game_status )
  {
    $game = self::forge();

    // meta
    $game->date        = Input::post('date');
    $game->team_top    = $top;
    $game->team_bottom = $bottom;
    $game->game_status = $game_status;

    $game->save();

    Model_Games_Runningscore::createNewGame($game->id);
    Model_Games_Stat::createNewGame($game->id, $top, $bottom);

    return $game;
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
