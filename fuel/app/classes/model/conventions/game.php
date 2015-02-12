<?php

class Model_Conventions_Game extends \Orm\Model
{
	protected static $_properties = array(
		'id',
		'convention_id',
		'game_id',
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

	protected static $_table_name = 'conventions_games';

  protected static $_has_one = array(
    'game' => array(
      'key_from'       => 'game_id',
      'model_to'       => 'Model_Game',
      'key_to'         => 'id',
      'cascade_save'   => false,
      'cascade_delete' => false,
    ),
    'games_runningscore' => array(
      'key_from'       => 'game_id',
      'model_to'       => 'Model_Games_Runningscore',
      'key_to'         => 'game_id',
      'cascade_save'   => false,
      'cascade_delete' => false,
    ),
  );

	/**
	 * regist new convention game
	 *
	 * @param string convention_id
	 *
	 * @return mix Model_Conventions_Game / false
	 */
	public static function regist($convention_id)
	{
		try
		{
			Mydb::begin();

			$game = Model_Game::regist();

			$convention_game = static::forge(array(
				'convention_id' => $convention_id,
				'game_id' => $game->id,
			));
			$convention_game->save();

			Mydb::commit();
			return $convention_game;
		}
		catch (Exception $e)
		{
			Mydb::rollback();
			Log::error('大会の試合追加に失敗：'.$e->getMessage());
			return false;
		}
	}

	/**
	 * get all convention games
	 *
	 * @param string convention_id
	 *
	 * @return array
	 *  - game_id
	 *	- date
	 * 	- top
	 *  - bottom
	 */
	public static function get_all_games($id)
	{
		$convention_games = static::query()->where('convention_id', $id)->get();

		$results = array();

		foreach ($convention_games as $convention_game)
		{
			$result['id']    = $convention_game->game_id;
			$result['date']  = $convention_game->game->date;
			$result['score'] = sprintf('%d - %d',
				$convention_game->games_runningscore->tsum,
				$convention_game->games_runningscore->bsum
			);

			foreach ($convention_game->game->games_teams as $team)
			{
				if ($team->order === 'top')
				{
					$result['bottom'] = $team->opponent_team_name;
				}
				else
				{
					$result['top'] = $team->opponent_team_name;
				}
			}

			$results[] = $result;
		}
	
		return $results;	
	}
}
