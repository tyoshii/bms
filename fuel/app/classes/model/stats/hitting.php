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

  private static $_result_map = array(
                // 打席,打数,安打,二塁,三塁,本塁,三振,四球,死球,犠打,犠飛
    '11' => array( 1,   1,   0,   0,   0,   0,   0,   0,   0,   0,   0 ), // 凡打
    '12' => array( 1,   1,   1,   0,   0,   0,   0,   0,   0,   0,   0 ), // 単打
    '13' => array( 1,   1,   0,   1,   0,   0,   0,   0,   0,   0,   0 ), // 二塁打
    '14' => array( 1,   1,   0,   0,   1,   0,   0,   0,   0,   0,   0 ), // 三塁打
    '15' => array( 1,   1,   0,   0,   0,   1,   0,   0,   0,   0,   0 ), // 本塁打
    '16' => array( 1,   0,   0,   0,   0,   0,   0,   0,   0,   1,   0 ), // 犠打
    '17' => array( 1,   0,   0,   0,   0,   0,   0,   0,   0,   0,   1 ), // 犠飛
    '18' => array( 1,   1,   0,   0,   0,   0,   1,   0,   0,   0,   0 ), // 見逃し三振
    '19' => array( 1,   1,   0,   0,   0,   0,   1,   0,   0,   0,   0 ), // 空振り三振
    '20' => array( 1,   0,   0,   0,   0,   0,   0,   1,   0,   0,   0 ), // 四球
    '21' => array( 1,   0,   0,   0,   0,   0,   0,   0,   1,   0,   0 ), // 死球
    '22' => array( 1,   0,   0,   0,   0,   0,   0,   0,   0,   0,   0 ), // 打撃妨害
    '23' => array( 1,   0,   0,   0,   0,   0,   0,   0,   0,   0,   0 ), // 守備妨害
  );

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

  private static function _increment_stats(&$stats, $result_id)
  {
    if ( $result_id and array_key_exists($result_id, self::$_result_map) )
    {
      $map = self::$_result_map[$result_id];

      $stats['TPA'] += $map[0];
      $stats['AB']  += $map[1];
      $stats['H']   += $map[2];
      $stats['2B']  += $map[3];
      $stats['3B']  += $map[4];
      $stats['HR']  += $map[5];
      $stats['SO']  += $map[6];
      $stats['BB']  += $map[7];
      $stats['HBP'] += $map[8];
      $stats['SAC'] += $map[9];
      $stats['SF']  += $map[10];
    }
  }

  public static function regist($ids, $datas, $status)
  {
    Mydb::begin();

    try {

      // - TODO foreach は念のため感ある。
      // registは基本的には選手一人の成績登録なので
      // functionの引数変えたほうがいいかも
      foreach ( $datas as $player_id => $data )
      {
        if ( ! $data ) continue;

        // set value
        $detail = array_key_exists('detail', $data) ? $data['detail'] : null;
        $stats  = array_key_exists('stats',  $data) ? $data['stats']  : null;

        if ( $detail )
        {
          // clean hitting detail stats
          // - 例えば4打席が予め登録されていて、修正された3打席分の成績がくると
          // - 4打席目が残ってしまうため、一度削除している
          Model_Stats_Hittingdetail::clean($ids + array('player_id' => $player_id));

          foreach ( $detail as $bat_times => $d )
          {
            // regist detail
            Model_Stats_Hittingdetail::regist($ids, $player_id, $bat_times, $d);

            // 打席数などの数字を計算
            self::_increment_stats($stats, $d['result']);
          }
        }

        // insert stats_hittings
        $hit = self::query()->where($ids + array('player_id' => $player_id,))->get_one();
        if ( ! $hit )
          $hit = self::forge($ids + array('player_id' => $player_id));

        $hit->set($stats);
        $hit->status = $status;

        $hit->save();

        // fieldings
        Model_Stats_Fielding::regist($ids, $player_id, $stats);
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
