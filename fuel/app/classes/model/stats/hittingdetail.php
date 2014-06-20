<?php

class Model_Stats_Hittingdetail extends \Orm\Model
{
	protected static $_properties = array(
		'id',
		'player_id',
		'game_id',
		'team_id',
		'bat_times',
		'direction',
		'kind',
		'result_id',
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
	protected static $_table_name = 'stats_hittingdetails';

  public static function clean($where)
  {
    Common::db_clean(self::$_table_name, $where);
  }

  public static function getStats($where)
  {
    $result = Model_Stat::getStats(self::$_table_name, $where, null);

    $stats = array();
    foreach ( $result as $res )
    {
      $key = $res['player_id'];

      if ( ! array_key_exists($key, $stats) )
        $stats[$key] = array();

      array_push($stats[$key], $res);
    }

    return $stats;
  }
}
