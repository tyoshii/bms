<?php

class Model_Team extends \Orm\Model
{
	protected static $_properties = array(
		'id',
		'name',
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
	protected static $_table_name = 'teams';

  public static function getTeams()
  {
    $res = self::find('all', array('select' => array('id', 'name')));
  
    $return = array();
    foreach ( $res as $row )
    {
      $return[$row['id']] = $row['name'];
    }

    return $return;
  }
}
