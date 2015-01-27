<?php

class Model_Stats_Hitting extends Model_Base
{
	protected static $_properties = array(
		'id',
		'input_status' => array('default' => 'save'),
		'player_id',
		'game_id',
		'team_id',
		'TPA',
		'AB',
		'H',
		'2B',
		'3B',
		'HR',
		'SO',
		'BB',
		'HBP',
		'SAC',
		'SF',
		'RBI',
		'R',
		'SB',
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
	protected static $_table_name = 'stats_hittings';

	protected static $_has_many = array(
		'details' => array(
			'model_to'       => 'Model_Stats_Hittingdetail',
			'key_from'       => 'game_id',
			'key_to'         => 'game_id',
			'cascade_save'   => false,
			'cascade_delete' => false,
		),
	);

	protected static $_has_one = array(
		'games' => array(
			'model_to'       => 'Model_Game',
			'key_from'       => 'game_id',
			'key_to'         => 'id',
			'cascade_save'   => false,
			'cascade_delete' => false,
		),
		'games_teams' => array(
			'model_to'       => 'Model_Games_Team',
			'key_from'       => 'game_id',
			'key_to'         => 'game_id',
			'cascade_save'   => false,
			'cascade_delete' => false,
		),
	);

	private static $_result_map = array(
		// 打席,打数,安打,二塁,三塁,本塁,三振,四球,死球,犠打,犠飛
		'11' => array(1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0), // 凡打
		'12' => array(1, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0), // 単打
		'13' => array(1, 1, 0, 1, 0, 0, 0, 0, 0, 0, 0), // 二塁打
		'14' => array(1, 1, 0, 0, 1, 0, 0, 0, 0, 0, 0), // 三塁打
		'15' => array(1, 1, 0, 0, 0, 1, 0, 0, 0, 0, 0), // 本塁打
		'16' => array(1, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0), // 犠打
		'17' => array(1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1), // 犠飛
		'18' => array(1, 1, 0, 0, 0, 0, 1, 0, 0, 0, 0), // 見逃し三振
		'19' => array(1, 1, 0, 0, 0, 0, 1, 0, 0, 0, 0), // 空振り三振
		'20' => array(1, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0), // 四球
		'21' => array(1, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0), // 死球
		'22' => array(1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0), // 打撃妨害
		'23' => array(1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0), // 守備妨害
	);

	public static function clean($where)
	{
		Common::db_clean(self::$_table_name, $where);
	}

	/**
	 * 成績の合計を配列で返す
	 *
	 * @param string game_id
	 * @param string team_id
	 *
	 * @return array
	 */
	public static function get_stats_total($game_id, $team_id)
	{
		$query = DB::select()->from(self::$_table_name);

		foreach (self::$_properties as $key => $column)
		{
			if (is_array($column))
			{
				$column = $key;
			}

			$query->select(DB::expr('SUM('.$column.') as '.$column));
		}

		$query->where('game_id', $game_id);
		$query->where('team_id', $team_id);

		$result = $query->execute()->as_array();

		return reset($result);
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
					->where('details.player_id', '=', $player_id)
					->order_by('details.bat_times')
				->related('details.batter_results')
				->get_one();

			$return[] = array(
				'game_id'            => $stats_player->games->id,
				'date'               => $stats_player->games->date,
				'opponent_team_id'   => $stats_player->games->games_teams->opponent_team_id,
				'opponent_team_name' => $stats_player->games->games_teams->opponent_team_name,
				'stats' => $stats,
			);
		}

		return $return;
	}

	/**
	 * 出場選手に従って打撃成績取得
	 *
	 * @param string game_id
	 * @param string team_id
	 * @param string player_id
	 *
	 * player_idは任意。指定するとその選手だけのデータを取得
	 * 指定が無い場合は出場した選手全員のデータを取得
	 *
	 * @return array
	 */
	public static function get_stats_by_playeds($game_id, $team_id, $player_id = null)
	{
		$query = Model_Stats_Player::get_query($game_id, $team_id, $player_id);

		// 交代も含めて表示順をそろえる
		$query->order_by('p.disp_order');	

		// join table
		$join_tables = array(
			self::$_table_name,
			'stats_fieldings',
		);

		foreach ($join_tables as $table)
		{
			$query->join($table, 'LEFT')
				->on($table.'.player_id', '=', 'p.player_id')
				->and_on($table.'.game_id', '=', 'p.game_id')
				->and_on($table.'.team_id', '=', 'p.team_id');
		}

		$result = $query->execute()->as_array();

		// add hittingdetails
		foreach ($result as $index => $res)
		{
			$query = DB::select()->from('stats_hittingdetails')
				->where('game_id', $res['game_id'])
				->where('team_id', $res['team_id'])
				->where('player_id', $res['player_id'])
				->order_by('bat_times');

			$result[$index]['details'] = $query->execute()->as_array();
		}

		return $result;
	}

	/**
	 * 成績取得
	 */
	public static function get_stats($game_id, $team_id)
	{
		$where = array(
			'game_id' => $game_id,
			'team_id' => $team_id,
		);

		return self::select_as_array(self::$_table_name, $where, 'player_id');
	}

	private static function _get_insert_props($stat)
	{
		return array(
			'TPA' => $stat['seiseki']['daseki'],
			'AB'  => $stat['seiseki']['dasuu'],
			'H'   => $stat['seiseki']['anda'],
			'2B'  => $stat['seiseki']['niruida'],
			'3B'  => $stat['seiseki']['sanruida'],
			'HR'  => $stat['seiseki']['honruida'],
			'SO'  => $stat['seiseki']['sanshin'],
			'BB'  => $stat['seiseki']['yontama'],
			'HBP' => $stat['seiseki']['shikyuu'],
			'SAC' => $stat['seiseki']['gida'],
			'SF'  => $stat['seiseki']['gihi'],
			'RBI' => $stat['seiseki']['daten'],
			'R'   => $stat['seiseki']['tokuten'],
			'SB'  => $stat['seiseki']['steal'],
		);
	}

	private static function _increment_stats(&$stats, $result_id)
	{
		if ($result_id and array_key_exists($result_id, self::$_result_map))
		{
			$map = self::$_result_map[$result_id];

			$stats['TPA'] += $map[0];
			$stats['AB'] += $map[1];
			$stats['H'] += $map[2];
			$stats['2B'] += $map[3];
			$stats['3B'] += $map[4];
			$stats['HR'] += $map[5];
			$stats['SO'] += $map[6];
			$stats['BB'] += $map[7];
			$stats['HBP'] += $map[8];
			$stats['SAC'] += $map[9];
			$stats['SF'] += $map[10];
		}
	}

	public static function regist($ids, $datas, $status = null)
	{
		Mydb::begin();

		try
		{
			foreach ($datas as $key => $data)
			{
				if ( ! $data) continue;

				// set value
				$player_id = $data['player_id'];
				$detail    = array_key_exists('detail', $data) ? $data['detail'] : null;
				$stats     = array_key_exists('stats', $data)  ? $data['stats']  : null;

				if ($detail)
				{
					// clean hitting detail stats
					// - 例えば4打席が予め登録されていて、修正された3打席分の成績がくると
					// - 4打席目が残ってしまうため、一度削除している
					Model_Stats_Hittingdetail::clean($ids + array('player_id' => $player_id));

					foreach ($detail as $bat_times => $d)
					{
						// regist detail
						Model_Stats_Hittingdetail::regist($ids, $player_id, $bat_times, $d);

						// 打席数などの数字を計算
						self::_increment_stats($stats, $d['result']);
					}
				}

				// insert stats_hittings
				$hit = self::query()->where($ids + array('player_id' => $player_id))->get_one();
				if ( ! $hit)
					$hit = self::forge($ids + array('player_id' => $player_id));

				$hit->set($stats);

				// status
				if ( ! is_null($status))
				{
					$hit->input_status = $status;
				}

				$hit->save();

				// fieldings
				Model_Stats_Fielding::regist($ids, $player_id, $stats);
			}

			Mydb::commit();
		}
		catch (Exception $e)
		{
			Mydb::rollback();
			throw new Exception($e->getMessage());
		}
	}

	public static function get_input_status($game_id, $player_id)
	{
		$s = self::query()->where(array(
			'game_id'   => $game_id,
			'player_id' => $player_id,
		))->get_one();

		return $s ? $s->input_status : '';
	}
}
