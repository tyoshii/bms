<?php

class Model_Stats_Fielding extends Model_Base
{
	protected static $_properties = array(
			'id',
			'player_id',
			'game_id',
			'team_id',
			'E',
			'created_at',
			'updated_at',
	);

	protected static $_observers = array(
			'Orm\Observer_CreatedAt' => array(
					'events'          => array('before_insert'),
					'mysql_timestamp' => false,
			),
			'Orm\Observer_UpdatedAt' => array(
					'events'          => array('before_update'),
					'mysql_timestamp' => false,
			),
	);
	protected static $_table_name = 'stats_fieldings';

	public static function clean($where)
	{
		Common::db_clean(self::$_table_name, $where);
	}

	public static function getStats($where)
	{
		return self::select_as_array(self::$_table_name, $where, 'player_id');
	}

	public static function regist($ids, $player_id, $stat)
	{
		$props = $ids + array('player_id' => $player_id);

		$field = self::query()->where($props)->get_one();
		if ( ! $field)
			$field = self::forge($props);

		$field->set($stat)->save();
	}
}
