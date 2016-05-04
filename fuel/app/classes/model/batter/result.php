<?php

class Model_Batter_Result extends \Orm\Model
{
    protected static $_properties = array(
        'id',
        'result',
        'order',
        'category_id',
        'category',
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
    protected static $_table_name = 'batter_results';

    protected static $_belongs_to = array(
        'batter_results' => array(
            'model_to' => 'Model_Stats_Hittingdetail',
            'key_from' => 'id',
            'key_to' => 'result_id',
            'cascade_save' => false,
            'cascade_delete' => false,
        ),
    );

    public static function get_all()
    {
        return DB::select()->from(self::$_table_name)
            ->order_by('category_id')
            ->execute()->as_array();
    }
}
