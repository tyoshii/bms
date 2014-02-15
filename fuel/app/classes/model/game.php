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

    // stamen
    $stamen = array();
    for ( $i = 1; $i <= 9; $i++ )
    {
      $stamen[] = array(
        'order'     => $i,
        'member_id' => 0,
        'position'  => array(0,0,0,0,0,0),
      );
    }
    $game->players = json_encode($stamen);
    $game->pitchers = '';
    $game->save();

    Model_Score::createNewGame($game->id);

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
      'scores.tsum',
      'scores.bsum',
      DB::expr('(select name from teams as t where t.id = g.team_top) as team_top_name'),
      DB::expr('(select name from teams as t where t.id = g.team_bottom) as team_bottom_name')
    )->from(array('games', 'g'));

    $query->join('scores')->on('g.id', '=', 'scores.id');
  

    if ( ! Auth::has_access('admin.admin') )
    {
      $query->where('game_status', '!=', 0);

      $my_team = Model_User::getMyTeamId();
      $query->where_opne();
      $query->where('team_top', $my_team );
      $query->or_where('team_bottom', $my_team );
      $query->where_close();
    }

    $query->order_by('date', 'desc');

    return $query->execute()->as_array();
  }
}
