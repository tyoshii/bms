<?php

class Model_Stat
{
  public static function getStats($table, $where, $index_key = 'player_id')
  {
    $query = DB::select()->from($table);

    foreach ( $where as $key => $val )
    {
      $query->where($key, $val);
    }

    return $query->execute()->as_array($index_key);
  }
}
