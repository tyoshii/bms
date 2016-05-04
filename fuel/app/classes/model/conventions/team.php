<?php

class Model_Conventions_Team extends \Orm\Model
{
    protected static $_properties = array(
        'id',
        'convention_id',
        'team_id',
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

    protected static $_table_name = 'conventions_teams';

    protected static $_has_one = array(
        'team' => array(
            'key_from' => 'team_id',
            'model_to' => 'Model_Team',
            'key_to' => 'id',
            'cascade_save' => false,
            'cascade_delete' => false,
    ),
    );

    /**
     * get team data.
     * 
     * Model_Team::get_teamsのラッパー
     * 大会IDが引数で指定されると、entriedキーが付与される
     *
     * @param string convention_id
     *
     * @return array
     */
    public static function get_teams($convention_id = null)
    {
        $teams = Model_Team::get_teams();

        if (is_null($convention_id)) {
            return $teams;
        }

        $entried = static::query()->where('convention_id', $convention_id)->get();
        foreach ($entried as $team) {
            if (array_key_exists($team->team_id, $teams)) {
                $teams[$team->team_id]['entried'] = true;
            }
        }

        return $teams;
    }

    /**
     * get entried team data.
     *
     * @param convention_id
     *
     * @return array
     */
    public static function get_entried_teams($convention_id)
    {
        return static::query()
            ->related('team')
            ->where('convention_id', $convention_id)
            ->get();
    }

    /**
     * add team to convention.
     *
     * @param convention_id
     * @param team_id
     *
     * @return bool
     */
    public static function add($convention_id, $team_id)
    {
        static::forge(array(
            'convention_id' => $convention_id,
            'team_id' => $team_id,
        ))->save();

        return true;
    }

    /**
     * remove team from convention.
     *
     * @param convention_id
     * @param team_id
     *
     * @return bool
     */
    public static function remove($convention_id, $team_id)
    {
        $teams = static::query()
            ->where('convention_id', $convention_id)
            ->where('team_id', $team_id)
            ->get();

        foreach ($teams ?: array() as $team) {
            $team->delete();
        }

        return true;
    }
}
