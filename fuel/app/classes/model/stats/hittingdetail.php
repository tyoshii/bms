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
			'events'          => array('before_insert'),
			'mysql_timestamp' => false,
		),
		'Orm\Observer_UpdatedAt' => array(
			'events'          => array('before_update'),
			'mysql_timestamp' => false,
		),
	);
	protected static $_table_name = 'stats_hittingdetails';

	protected static $_has_one = array(
		'batter_results' => array(
			'model_to'       => 'Model_Batter_Result',
			'key_from'       => 'result_id',
			'key_to'         => 'id',
			'cascade_save'   => false,
			'cascade_delete' => false,
		),
	);

	protected static $_belongs_to = array(
		'details' => array(
			'model_to'       => 'Model_Stats_Hitting',
			'key_from'       => 'game_id',
			'key_to'         => 'game_id',
			'cascade_save'   => false,
			'cascade_delete' => false,
		),
	);

	public static function clean($where)
	{
		Common::db_clean(self::$_table_name, $where);
	}

	public static function get_stats($where)
	{
		// データ取得
		$query = DB::select()->from(self::$_table_name);
		$query->order_by('bat_times');

		// where
		foreach ($where as $key => $val)
		{
			$query->where($key, $val);
		}

		$result = $query->execute()->as_array();

		// データ整形
		$stats = array();
		foreach ($result as $res)
		{
			$key = $res['player_id'];

			if ( ! array_key_exists($key, $stats))
				$stats[$key] = array();

			array_push($stats[$key], $res);
		}

		return $stats;
	}

	public static function regist($ids, $player_id, $bat_times, $stat)
	{
		$props = $ids + array(
			'player_id' => $player_id,
			'bat_times' => $bat_times,
			'direction' => $stat['direction'],
			'kind'      => $stat['kind'],
			'result_id' => $stat['result'],
		);

		self::forge($props)->save();
	}

	/**
	 * 指定されたplayer_idが出場した試合ごとの打撃成績を返す
	 *
	 * @param string player_id
	 * @return array
	 */
	public static function get_stats_per_game($player_id)
	{
		$played_games = Model_Stats_Player::get_played_games($player_id);

		$return = array();

		foreach ($played_games ?: array() as $stats_player) 
		{
			$stats = static::query()
				->where('player_id', $player_id)
				->where('game_id', $stats_player->game_id)
				->related('details')
					->order_by('details.bat_times')
				->get();

			$return[] = array(
				'game_id'            => $stats_player->games->id,
				'date'               => $stats_player->games->date,
				'opponent_team_id'   => $stats_player->games->games_team->opponent_team_id,
				'opponent_team_name' => $stats_player->games->games_team->opponent_team_name,
				'stats' => $details,
			);
		}

		return $return;
	}
}
