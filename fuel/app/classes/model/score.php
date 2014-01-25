<?php

class Model_Score extends \Orm\Model
{
	protected static $_properties = array(
		'id',
		't1',
		't2',
		't3',
		't4',
		't5',
		't6',
		't7',
		't8',
		't9',
		't10',
		't11',
		't12',
		't13',
		't14',
		't15',
		't16',
		't17',
		't18',
		'tsum',
		'b1',
		'b2',
		'b3',
		'b4',
		'b5',
		'b6',
		'b7',
		'b8',
		'b9',
		'b10',
		'b11',
		'b12',
		'b13',
		'b14',
		'b15',
		'b16',
		'b17',
		'b18',
		'bsum',
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
	protected static $_table_name = 'scores';

}
