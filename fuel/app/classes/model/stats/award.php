<?php

class Model_Stats_Award extends Model_Base
{
    protected static $_properties = array(
        'id',
        'game_id',
        'team_id',
        'mvp_player_id'        => array('default' => 0),
        'second_mvp_player_id' => array('default' => 0),
        'created_at',
        'updated_at',
    );

    protected static $_observers = array(
        'Orm\Observer_CreatedAt' => array(
            'events'          => array('before_insert'),
            'mysql_timestamp' => false,
        ),
        'Orm\Observer_UpdatedAt' => array(
            'events'          => array('before_update'),
            'mysql_timestamp' => false,
        ),
    );

    protected static $_table_name = 'stats_awards';

    public static function get_stats($game_id, $team_id)
    {
        $query = <<<__SQL__
SELECT
	*,
	(SELECT name FROM players WHERE id = stats_awards.mvp_player_id)
		as mvp_player_name,
	(SELECT name FROM players WHERE id = stats_awards.second_mvp_player_id)
		as second_mvp_player_name
FROM
	stats_awards
WHERE
	game_id = $game_id AND
	team_id = $team_id
__SQL__;

        $result = DB::query($query)->execute()->as_array();
        return reset($result);
    }

    public static function regist($game_id, $team_id, $stats)
    {
        $props = array(
            'game_id' => $game_id,
            'team_id' => $team_id,
        );
        $award = self::get_one_or_forge($props);

        $award->set($stats);
        $award->save();

        return $award;
    }
}
