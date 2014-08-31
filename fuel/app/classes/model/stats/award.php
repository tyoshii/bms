<?php

#class Model_Stats_Award extends \Orm\Model
class Model_Stats_Award extends Model_Bms
{
	protected static $_properties = array(
		'id',
		'game_id',
		'team_id',
		'mvp_player_id' => array(
      'default' => 0,
    ),
		'second_mvp_player_id' => array(
      'default' => 0,
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

	protected static $_table_name = 'stats_awards';

  public static function get_stats($game_id, $team_id)
  {
    $props = array(
      'game_id' => $game_id,
      'team_id' => $team_id,
    );
    return self::_get_one_or_forge($props);
  }

  public static function regist($game_id, $team_id, $stats)
  {
    $props = array(
      'game_id' => $game_id,
      'team_id' => $team_id,
    );
    $award = self::_get_one_or_forge($props);

    $award->set($stats);
    $award->save();
  }
}
