<?php
class Model_Score_Self extends \Orm\Model
{

	protected static $_properties = array(
		'id',
		'date',
		'team_top',
		'team_bottom',
		'game_status',
		'players',
		'pitchers',
		'batters',
		'created_at',
		'updated_at',
	);

  public static function getSelfScores()
  {
    // userIDからユーザ名(uniq)を取得
    // select username from users where id = <ID>;
    
    $username = Auth::get_screen_name();
    $team_id = Model_Team::get_teams();

    $query = <<<___QUERY___
SELECT
    p.number, t.name as team, p.name, sum(s.TPA) as TPA, count(s.id) as G,sum(s.AB) as AB, sum(s.H) as H, sum(s.2B) as 2B, sum(s.3B) as 3B, sum(s.HR) as HR,sum(s.RBI) as RBI,sum(s.R) as R,sum(s.SO) as SO,sum(s.BB) as BB,sum(s.HBP) as HBP,sum(s.SAC) as SAC,sum(s.SF) as SF,sum(s.SB) as SB,(SELECT sum(E) from stats_fieldings where player_id = s.player_id) as E
FROM
    stats_hittings AS s
LEFT JOIN
    players AS p
ON
    s.player_id = p.id
LEFT JOIN
    teams AS  t
ON
    t.id = p.team
WHERE
    p.status != -1
GROUP BY
    s.player_id
;
___QUERY___;

    $result = DB::query($query)->execute()->as_array();

    return $result;
  }
}
