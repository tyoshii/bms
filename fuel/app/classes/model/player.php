<?php

class Model_Player extends \Orm\Model
{
	protected static $_properties = array(
		'id',
		'team',
		'name',
		'number',
    'username',
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
	protected static $_table_name = 'players';

  protected static $_belongs_to = array(
    'teams' => array(
      'model_to' => 'Model_Team',
      'key_from' => 'team',
      'key_to' => 'id',
      'cascade_save' => true,
      'cascade_delete' => false,
    ));

  public static function getMyTeamId()
  {
    if ( $res = self::find_by_username(Auth::get_screen_name()) )
      return $res->team;
      
    return null;
  }

  public static function getMembers($team_id)
  {
    return DB::select()->from(self::$_table_name)
                       ->where('team', $team_id)
                       ->execute()->as_array();
  }
}
