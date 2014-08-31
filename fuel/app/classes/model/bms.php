<?php

class Model_Bms extends \Orm\Model
{
  public static function _get_one_or_forge(array $props = array())
  {
    return self::query()->where($props)->get_one() ?: self::forge($props);
  }
}
