<?php

class Model_Stats_Hittingdetail extends \Orm\Model
{
  protected static $_properties = array(
      'id',
      'player_id',
      'game_id',
      'team_id',
      'bat_times',
      'direction',
      'kind',
      'result_id',
      'created_at',
      'updated_at',
  );

  protected static $_observers = array(
      'Orm\Observer_CreatedAt' => array(
          'events'          => array('before_insert'),
          'mysql_timestamp' => false,
      ),
      'Orm\Observer_UpdatedAt' => array(
          'events'          => array('before_update'),
          'mysql_timestamp' => false,
      ),
  );
  protected static $_table_name = 'stats_hittingdetails';

  public static function clean($where)
  {
    Common::db_clean(self::$_table_name, $where);
  }

  public static function getStats($where)
  {
    // データ取得
    $query = DB::select()->from(self::$_table_name);
    foreach ($where as $key => $val)
    {
      $query->where($key, $val);
    }
    $query->order_by('bat_times');

    $result = $query->execute()->as_array();

    // データ整形
    $stats = array();
    foreach ($result as $res)
    {
      $key = $res['player_id'];

      if (!array_key_exists($key, $stats))
        $stats[$key] = array();

      array_push($stats[$key], $res);
    }

    return $stats;
  }

  public static function regist($ids, $player_id, $bat_times, $stat)
  {
    $props = $ids + array(
            'player_id' => $player_id,
            'bat_times' => $bat_times,
            'direction' => $stat['direction'],
            'kind'      => $stat['kind'],
            'result_id' => $stat['result'],
        );

    self::forge($props)->save();
  }

  public static function replaceAll($ids, $player_id, $stats)
  {
    // clean player stats
    // - 例えば4打席が予め登録されていて、修正された3打席分の成績がくると
    // - 4打席目が残ってしまうため、一度削除している
    self::clean($ids + array('player_id' => $player_id));

    // insert
    foreach ($stats as $bat_times => $stat)
    {
      self::regist($ids, $player_id, $bat_times, $stat);
    }
  }
}
