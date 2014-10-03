<?php

class Model_Stats_Pitching extends Model_Base
{
	protected static $_properties = array(
		'id',
		'status' => array('default' => 0),
		'input_status' => array('default' => 'save'),
		'player_id',
		'game_id',
		'team_id',
		'order' => array('default' => 0),
		'W',
		'L',
		'HLD',
		'SV',
		'IP',
		'IP_frac',
		'H',
		'SO',
		'BB',
		'HB',
		'ER',
		'R',
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
	protected static $_table_name = 'stats_pitchings';

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

	/**
	 * 出場選手に従って投手成績取得
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

		// join
		$query->join(self::$_table_name, 'LEFT')
			->on(self::$_table_name.'.player_id', '=', 'p.player_id')
			->and_on(self::$_table_name.'.game_id', '=', 'p.game_id')
			->and_on(self::$_table_name.'.team_id', '=', 'p.team_id');

		// positionに1が含まれている場合のみ取得
		$query->where('p.position', 'LIKE', '%1%');

		// 登板順に
		$query->order_by(self::$_table_name.'.order');

		$result = $query->execute()->as_array();

		return $result;
	}

	/**
	 * 投手成績を取得
	 * @parma string game_id
	 * @parma string team_id
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
			'W'       => $stat['result'] === 'win'  ? 1 : 0,
			'L'       => $stat['result'] === 'lose' ? 1 : 0,
			'HLD'     => $stat['result'] === 'hold' ? 1 : 0,
			'SV'      => $stat['result'] === 'save' ? 1 : 0,
			'IP'      => $stat['IP'],
			'IP_frac' => $stat['IP_frac'],
			'H'       => $stat['H'],
			'SO'      => $stat['SO'],
			'BB'      => $stat['BB'],
			'HB'      => $stat['HB'],
			'ER'      => $stat['ER'],
			'R'       => $stat['R'],
		);
	}

	public static function regist($ids, $stats, $status = null)
	{
		Mydb::begin();

		try
		{
			// regist new data
			foreach ($stats as $order => $stat)
			{
				if ( ! $stat) continue;

				// get model
				$pitch = self::get_one_or_forge($ids + array('player_id' => $stat['player_id']));

				// stats set => save
				$props = self::_get_insert_props($stat);
				$pitch->set($props);

				// other
				$pitch->order  = $order;
				if ( ! is_null($status))
				{
					$pitch->input_status = $status;
				}

				$pitch->save();
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
