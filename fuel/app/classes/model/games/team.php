<?php

class Model_Games_Team extends \Orm\Model
{
	protected static $_properties = array(
		'id',
		'game_id',
		'team_id',
		'order',
		'opponent_team_id',
		'input_status' => array(
      'default' => 'save',
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

	protected static $_table_name = 'games_teams';

}
