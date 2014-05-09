<?php

class Model_Stats_Pitching extends \Orm\Model
{
	protected static $_properties = array(
		'id',
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

  public static function registStats($ids, $stats)
  {
    DB::start_transaction();

    try {

      // clean data
      DB::delete(self::$_table_name)
        ->where($ids)
        ->execute();

      // regist new data
      foreach ( $stats as $player_id => $stat )
      {
        if ( ! $stat ) continue;

        $pitch = self::forge($ids + array(
          'player_id' => $player_id,
          'W'         => $stat['result'] == 'win'  ?  1 : 0,
          'L'         => $stat['result'] == 'lose' ?  1 : 0,
          'HLD'       => $stat['result'] == 'hold' ?  1 : 0,
          'SV'        => $stat['result'] == 'save' ?  1 : 0,
          'IP'        => $stat['inning_int'],
          'IP_frac'   => $stat['inning_frac'],
          'H'         => $stat['hianda'],
          'SO'        => $stat['sanshin'],
          'BB'        => $stat['shishikyuu'],
          'HB'        => 0,
          'ER'        => $stat['earned_runs'],
          'R'         => $stat['runs']          
        ));

        $pitch->save();
      }

      DB::commit_transaction();
    } catch ( Exception $e ) {
      DB::rollback_transaction();
      throw new Exception();
    } 
  }
}
