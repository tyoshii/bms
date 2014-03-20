<?php

class Model_Game extends \Orm\Model
{
	protected static $_properties = array(
		'id',
		'date',
		'team_top',
		'team_bottom',
		'game_status',
		'players',
		'pitchers',
		'batters',
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

    // players
    $players = array();
    for ( $i = 1; $i <= 9; $i++ )
    {
      $players[] = array(
        'order'     => $i,
        'member_id' => 0,
        'position'  => array(0,0,0,0,0,0),
      );
    }
    $game->players = json_encode($players);
    $game->pitchers = '';
    $game->batters  = '';
    $game->save();

    Model_Games_Runningscore::createNewGame($game->id);
    Model_Games_Stat::createNewGame($game->id, $top, $bottom);

    return $game;
  }

  public static function getOwnGames()
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

    if ( ! Auth::has_access('admin.admin') )
    {

      $my_team = Model_User::getMyTeamId();
      $query->where_open();
      $query->where('team_top', $my_team );
      $query->or_where('team_bottom', $my_team );
      $query->where_close();
    }

    $query->order_by('date', 'desc');

    return $query->execute()->as_array();
  }
}
