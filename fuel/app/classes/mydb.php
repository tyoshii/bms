<?php

class Mydb
{
    private static $_callnum = 0;
    private static function _increment()
    {
        static::$_callnum++;
    }
    private static function _decrement()
    {
        static::$_callnum--;

        if (static::$_callnum < 0) {
            static::$_callnum = 0;
        }
    }

    public static function begin()
    {
        static::_increment();

        if (DB::in_transaction()) {
            return false;
        }

        return DB::start_transaction();
    }

    public static function commit()
    {
        static::_decrement();

        if (DB::in_transaction() && static::$_callnum === 0) {
            return DB::commit_transaction();
        }

        return false;
    }

    public static function rollback()
    {
        static::_decrement();

        if (DB::in_transaction() && static::$_callnum === 0) {
            return DB::rollback_transaction();
        }

        return false;
    }
}
