<?php

class Mydb
{
  private static $_already = false;

  public static function begin()
  {
    if (DB::in_transaction())
    {
      self::$_already = true;
      return false;
    }

    return DB::start_transaction();
  }

  public static function commit()
  {
    if (self::$_already)
    {
      self::$_already = false;
      return false;
    }

    if (DB::in_transaction())
      return DB::commit_transaction();

    return false;
  }

  public static function rollback()
  {
    if (self::$_already)
    {
      self::$_already = false;
      return false;
    }

    if (DB::in_transaction())
      return DB::rollback_transaction();

    return false;
  }
}
