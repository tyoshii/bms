<?php

class Model_Stat
{
  public static function getStats($table, $game_id, $index_key = null)
  {
    return DB::select()->from($table)
                       ->where('game_id', $game_id)
                       ->execute()
                       ->as_array($index_key);
  }
}
