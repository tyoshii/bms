<?php

class Model_Stats_Pitching extends Model_Base
{
	protected static $_properties = array(
		'id',
    'status' => array(
      'default' => 0,
    ),
		'player_id',
		'game_id',
    'team_id',
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
			'events' => array('before_insert'),
			'mysql_timestamp' => false,
		),
		'Orm\Observer_UpdatedAt' => array(
			'events' => array('before_update'),
			'mysql_timestamp' => false,
		),
	);
	protected static $_table_name = 'stats_pitchings';

  public static function get_stats($where)
  {
    return self::select_as_array(self::$_table_name, $where, 'player_id');
  }

  private static function _get_insert_props($stat)
  {
    return array(
      'W'         => $stat['result'] == 'win'  ?  1 : 0,
      'L'         => $stat['result'] == 'lose' ?  1 : 0,
      'HLD'       => $stat['result'] == 'hold' ?  1 : 0,
      'SV'        => $stat['result'] == 'save' ?  1 : 0,
      'IP'        => $stat['IP'],
      'IP_frac'   => $stat['IP_frac'],
      'H'         => $stat['H'],
      'SO'        => $stat['SO'],
      'BB'        => $stat['BB'],
      'HB'        => $stat['HB'],
      'ER'        => $stat['ER'],
      'R'         => $stat['R'],
    );
  }

  public static function regist($ids, $stats, $status)
  {
    Mydb::begin();

    try {

      // regist new data
      foreach ( $stats as $player_id => $stat )
      {
        if ( ! $stat ) continue;

        # get model
        $pitch = self::query()->where($ids + array(
          'player_id' => $player_id,
        ))->get_one();

        if ( ! $pitch )
          $pitch = self::forge($ids + array(
            'player_id' => $player_id,
          ));
        
        # stats set => save
        $props = self::_get_insert_props($stat);

        $pitch->set($props);
        $pitch->status = $status;

        $pitch->save();
      }

      Mydb::commit();
    } catch ( Exception $e ) {
      Mydb::rollback();
      throw new Exception($e->getMessage());
    }
  }

  public static function replaceAll($ids, $stats, $status)
  {
    Mydb::begin();

    try {

      // clean data
      Common::db_clean(self::$_table_name, $ids);

      // regist new data
      foreach ( $stats as $player_id => $stat )
      {
        if ( ! $stat ) continue;

        $props = self::_get_insert_props($stat);
        $pitch = self::forge($ids + $props + array('player_id' => $player_id));
        $pitch->status = $status;

        $pitch->save();
      }

      Mydb::commit();
    } catch ( Exception $e ) {
      Mydb::rollback();
      throw new Exception($e->getMessage());
    } 
  }

  public static function get_status( $game_id, $player_id )
  {
    $s = self::query()->where(array(
      'game_id'   => $game_id,
      'player_id' => $player_id,
    ))->get_one();

    return $s ? $s->status : '0';
  }
}
