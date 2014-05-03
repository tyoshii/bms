<?php

class Model_Stats_Hitting extends \Orm\Model
{
	protected static $_properties = array(
		'id',
		'player_id',
		'game_id',
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
			'events' => array('before_insert'),
			'mysql_timestamp' => false,
		),
		'Orm\Observer_UpdatedAt' => array(
			'events' => array('before_update'),
			'mysql_timestamp' => false,
		),
	);
	protected static $_table_name = 'stats_hittings';

}
