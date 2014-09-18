<?php

class Model_Games_Team extends \Orm\Model
{
	protected static $_properties = array(
		'id',
		'game_id',
		'team_id',
		'order' => array(
      'default' => 'top',
    ),
		'opponent_team_id' => array(
      'default' => 0,
    ),
		'opponent_team_name' => array(
      'default' => '',
    ),
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

  protected static $_belongs_to = array(
    'games' => array(
      'model_to'       => 'Model_Game',
      'key_from'       => 'game_id',
      'key_to'         => 'id',
      'cascade_save'   => false,
      'cascade_delete' => false,
    ),
	);

  /**
   * 新規ゲーム追加
   * @param array
   *   game_id
   *   team_id
   *   order
   *   opponent_team_id
   *   opponent_team_name
   */
  public static function regist($props)
  {
    extract($props);

    // validation
    if ( ! isset($game_id) )
    {
      Log::error('game_idが指定されていません');
      return false;
    }
    
    if ( ! isset($team_id) )
    {
      Log::error('team_idが指定されていません');
      return false;
    }

    // opponent
    if ( ! isset($opponent_team_id) and ! isset($opponent_team_name) )
    {
      Log::error('opponent_team_id / opponent_team_name はどちらかを指定してください');
      return false;
    }

    // team_id is available ?
    if ( ! Model_Team::find($team_id) )
    {
      Log::error('指定されたteam_idが存在しないidです。');
      return false;
    }

    // opponent_team_id is available ?
    if ( isset($opponent_team_id) and ! Model_Team::find($opponent_team_id) )
    {
      Log::error('指定されたopponent_team_idが存在しないidです');
      return false;
    }

    // default value (order)
    $props['order'] = isset($order) ? $order : 'top';

    // db insert
    $team = self::forge($props);
    $team->save();

    return true;
  }

}
