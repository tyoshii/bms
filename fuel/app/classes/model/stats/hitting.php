<?php

class Model_Stats_Hitting extends \Orm\Model
{
	protected static $_properties = array(
		'id',
    'status' => array(
      'default' => 0,
    ),
		'player_id',
		'game_id',
		'team_id',
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

  public static function clean($where)
  {
    Common::db_clean(self::$_table_name, $where);
  }

  public static function get_stats($game_id, $team_id)
  {
    return DB::select()->from(self::$_table_name)
            ->where('game_id', $game_id)
            ->where('team_id', $team_id)
            ->execute()->as_array('player_id'); 
  }

  private static function _get_insert_props($stat)
  {
    return array(
          'TPA' => $stat['seiseki']['daseki'],
          'AB'  => $stat['seiseki']['dasuu'],
          'H'   => $stat['seiseki']['anda'],
          '2B'  => $stat['seiseki']['niruida'],
          '3B'  => $stat['seiseki']['sanruida'],
          'HR'  => $stat['seiseki']['honruida'],
          'SO'  => $stat['seiseki']['sanshin'],
          'BB'  => $stat['seiseki']['yontama'],
          'HBP' => $stat['seiseki']['shikyuu'],
          'SAC' => $stat['seiseki']['gida'],
          'SF'  => $stat['seiseki']['gihi'],
          'RBI' => $stat['seiseki']['daten'],
          'R'   => $stat['seiseki']['tokuten'],
          'SB'  => $stat['seiseki']['steal'],
    );
  }

  public static function regist($ids, $stats, $status)
  {
    Mydb::begin();

    try {

      // - TODO foreach は念のため感ある。
      // registは基本的には選手一人の成績登録なので
      // functionの引数変えたほうがいいかも
      foreach ( $stats as $player_id => $stat )
      {
        if ( ! $stat ) continue;

        // get model
        $hit = self::query()->where($ids + array(
          'player_id' => $player_id,
        ))->get_one();
        if ( ! $hit )
          $hit = self::forge($ids + array('player_id' => $player_id));

        // set props => save
        $props = self::_get_insert_props($stat);
        $hit->set($props);
        $hit->status = $status;

        $hit->save();

        // hittingdetails
        if ( $stat['detail'] )
        {
          foreach ( $stat['detail'] as $bat_times => $data )
          {
            $detail = Model_Stats_Hittingdetail::query()->where($ids + array(
              'player_id' => $player_id,
              'bat_times' => $bat_times + 1,
            ))->get_one();
            if ( ! $detail )
              $detail = Model_Stats_Hittingdetail::forge($ids + array(
                'player_id' => $player_id,
                'bat_times' => $bat_times + 1,
              ));

            $detail->set(array(
              'direction' => $data['direction'],
              'kind'      => $data['kind'],
              'result_id' => $data['result'],
            ));
            $detail->save();
          }
        }

        // fieldings
        $field = Model_Stats_Fielding::query()->where($ids + array(
          'player_id' => $player_id,
        ))->get_one();
        if ( ! $field )
          $field = Model_Stats_Fielding::forge($ids + array(
            'player_id' => $player_id,
          ));

        $field->set(array(
          'E' => $stat['seiseki']['error'] ?: 0,
        ));

        $field->save();
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

      // clean database
      Common::db_clean(self::$_table_name, $ids);
      Model_Stats_Hittingdetail::clean($ids);
      Model_Stats_Fielding::clean($ids);

      // regist new data
      foreach ( $stats as $player_id => $stat )
      {
        if ( ! $stat ) continue;

        // hittings
        $props = self::_get_insert_props($stat);
        $hit = self::forge($ids + $props + array('player_id' => $player_id));
        $hit->status = $status;

        $hit->save();

        // hittingdetails
        if ( $stat['detail'] )
        {
          foreach ( $stat['detail'] as $bat_times => $data )
          {
            $detail = Model_Stats_Hittingdetail::forge($ids + array(
              'player_id' => $player_id,
              'bat_times' => $bat_times + 1,
              'direction' => $data['direction'],
              'kind'      => $data['kind'],
              'result_id' => $data['result'],
            ));
            $detail->save();
          }
        }

        // fieldings
        $field = Model_Stats_Fielding::forge($ids + array(
          'player_id' => $player_id,
          'E'         => $stat['seiseki']['error'] ?: 0,
        ));
        $field->save();
      }

      Mydb::commit();
    } catch ( Exception $e ) {
      Mydb::rollback();
      throw new Exception();
    }
  }

  public static function getStats($where)
  {
    return Model_Stat::getStats(self::$_table_name, $where);
  }
}
