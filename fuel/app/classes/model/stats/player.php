<?php

class Model_Stats_Player extends \Orm\Model
{
	protected static $_properties = array(
		'id',
		'game_id',
		'team_id',
		'player_id',
		'order',
		'position',
		'disp_order',
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
	protected static $_table_name = 'stats_players';

	protected static $_has_one = array(
		'games' => array(
			'model_to'       => 'Model_Game',
			'key_from'       => 'game_id',
			'key_to'         => 'id',
			'cascade_save'   => false,
			'cascade_delete' => false,
		),
	);

	protected static $_belongs_to = array(
		'games' => array(
			'model_to'       => 'Model_Game',
			'key_from'       => 'game_id',
			'key_to'         => 'id',
			'cascade_save'   => false,
			'cascade_delete' => false,
		),
	);

	public static function get_query($game_id, $team_id, $player_id = null)
	{
		$query = DB::select('*', 'p.player_id')->from(array('stats_players', 'p'));

		$query->where('p.game_id', $game_id);
		$query->where('p.team_id', $team_id);

		if ($player_id)
		{
			$query->where('p.player_id', $player_id);
		}

		// players
		$query->join(array('players', 'pl'), 'LEFT OUTER')
			->on('p.player_id', '=', 'pl.id')
			->and_on('p.team_id', '=', 'pl.team_id');

		return $query;
	}

	/**
	 * get participate players
	 *
	 * @param string game_id
	 * @param string team_id
	 *
	 * @return array
	 */
	public static function get_participate_players($game_id, $team_id)
	{
		$query = self::get_query($game_id, $team_id);

		// 交代も含めて表示順をそろえる
		$query->order_by('p.disp_order');

		$result = $query->execute()->as_array();

		foreach ($result as $index => $res)
		{
			if ($res['position'] == '')
			{
				$result[$index]['position'][] = '';
			}
			else
			{
				// テンプレート表示の際に、次のselectボックスを出すために、
				// 配列の最後にから配列を入れる。
				$temp = explode(',', $res['position']);
				$temp[] = '';
				$result[$index]['position'] = $temp;
			}
		}

		return $result;
	}

	public static function create_new_game($game_id, $team_id)
	{
		if ( ! $team_id) return false;

		$ids = array(
			'game_id' => $game_id,
			'team_id' => $team_id,
		);

		$default = array();
		for ($i = 1; $i < 10; $i++)
		{
			$default[] = array(
				'player_id' => 0,
				'order'     => $i,
			);
		}

		self::regist($ids, $default);
	}

	public static function regist($ids, $players)
	{
		Mydb::begin();

		try
		{
			// clean data
			Common::db_clean(self::$_table_name, $ids);

			// regist new data
			foreach ($players as $disp_order => $player)
			{
				if ( ! $player) continue;

				$player = self::forge($ids + array(
					'disp_order' => $disp_order,
					'player_id'  => $player['player_id'],
					'order'      => $player['order'] ? : 0,
					'position'   => array_key_exists('position', $player) ? implode(',', $player['position']) : '',
				));

				$player->save();
			}

			Mydb::commit();
		}
		catch (Exception $e)
		{
			Mydb::rollback();
			throw new Exception($e->getMessage());
		}
	}

	/**
	 * 出場した試合のデータ
	 *
	 * @param string player_id
	 * @return array object(Model_Stats_Player)
	 */
	public static function get_played_games($player_id)
	{
		$team_id = Model_Player::find($player_id)->team_id;

		$return = static::query()
				->where('player_id', $player_id)
				->where('team_id', $team_id)
			->related('games')
				->where('games.game_status', '!=', '-1')
				->order_by('games.date', 'DESC')
			->related('games.games_team')
				->where('games.games_team.team_id', '=', $team_id)
			->get();

		return $return;
	}
}
