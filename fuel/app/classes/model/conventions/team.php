<?php

class Model_Conventions_Team extends \Orm\Model
{
	protected static $_properties = array(
		'id',
		'convention_id',
		'team_id',
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

	protected static $_table_name = 'conventions_teams';

	/**
	 * get team data
	 * @param string convention_id
	 * @return array
	 */
	public static function get_teams($convention_id)
	{
		$teams = Model_Team::get_teams();

		$entried = static::query()->where('convention_id', $convention_id)->get();
		foreach ($entried as $team)
		{
			if (array_key_exists($team->team_id, $teams))
			{
				$teams[$team->team_id]['entried'] = true;
			}
		}

		return $teams;
	}

	/**
	 * add team to convention
	 * @param convention_id
	 * @param team_id
	 * @return boolean
	 */
	public static function add($convention_id, $team_id)
	{
		static::forge(array(
			'convention_id' => $convention_id,
			'team_id' => $team_id,
		))->save();

		return true;
	}

	/**
	 * remove team from convention
	 * @param convention_id
	 * @param team_id
	 * @return boolean
	 */
	public static function remove($convention_id, $team_id)
	{
		$teams = static::query()
			->where('convention_id', $convention_id)
			->where('team_id', $team_id)
			->get();

		foreach ($teams ?: array() as $team)
		{
			$team->delete();
		}

		return true;
	}
}
