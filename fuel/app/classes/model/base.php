<?php

class Model_Base extends \Orm\Model
{
    public static function get_one_or_forge(array $props = array())
    {
        return self::query()->where($props)->get_one() ?: self::forge($props);
    }

    public static function select_as_array($table, $where, $index_key)
    {
        $query = DB::select()->from($table);

        foreach ($where as $key => $val) {
            $query->where($key, $val);
        }

        return $query->execute()->as_array($index_key);
    }
}
