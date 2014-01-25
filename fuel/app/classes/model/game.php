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

  public static function getOwnGames()
  {

    $query = DB::select(
      array( 'g.id', 'id' ),
      'g.id',
      'g.date',
      'g.game_status',
      DB::expr('(select name from teams as t where t.id = g.team_top) as team_top'),
      DB::expr('(select name from teams as t where t.id = g.team_bottom) as team_bottom')
    )->from(array('games', 'g'));

    if ( ! Auth::has_access('admin.admin') )
    {
      $my_team = Model_User::getMyTeamId();
      $query->where('team_top', $my_team );
      $query->or_where('team_bottom', $my_team );
      $query->order_by('date');
    }

    return $query->execute()->as_array();
  }
}
