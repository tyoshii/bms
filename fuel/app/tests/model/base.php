<?php

abstract class Test_Model_Base extends Test_Base
{
  public function assertSchema($class_name = null)
  {
    // class_nameの自動取得
    if (is_null($class_name))
    {
      $class_name = str_replace('Test_', '', get_class($this));
    }

    // _propertiesの取得
    $table = self::get_property($class_name, '_table_name');
    $props = self::get_property($class_name, '_properties');
    $props = self::_trim_props($props);

    // DBからカラムを取得
    $columns = self::_get_columns_from_db($table);

    // sort
    sort($props);
    sort($columns);

    // assert
    $this->assertSame($props, $columns);
  }

  private static function _trim_props($props)
  {
    $return = array();
    foreach ($props as $key => $val)
    {
      if (is_array($val))
      {
        $return[] = $key;
      } else
      {
        $return[] = $val;
      }
    }

    return $return;
  }

  private static function _get_columns_from_db($table)
  {
    $result = \Database_Connection::instance()
        ->query(\DB::SELECT, "desc {$table}", false)
        ->as_array();

    $return = array();
    foreach ($result as $res)
      $return[] = $res['Field'];

    return $return;
  }
}
