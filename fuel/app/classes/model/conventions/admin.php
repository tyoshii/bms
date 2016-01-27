<?php

class Model_Conventions_Admin extends \Orm\Model
{
    protected static $_properties = array(
        'id',
        'convention_id',
        'username',
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

    protected static $_table_name = 'conventions_admins';

    /**
     * add admin username
     * @param convention_id
     * @parma username
     * @return boolean
     */
    public static function add($id, $username)
    {
        if (static::check_auth($id, $username))
        {
            // already exists
            return true;
        }

        $conv_admin = static::forge(array(
            'convention_id' => $id,
            'username' => $username,
        ));

        $conv_admin->save();

        return true;
    }

    /**
     * check admin user
     * @param convention_id
     * @param username
     * @return boolean
     */
    public static function check_auth($id, $username)
    {
        $check = static::query()
            ->where('convention_id', $id)
            ->where('username', $username)
            ->get_one();

        return $check ? true : false;
    }
}
