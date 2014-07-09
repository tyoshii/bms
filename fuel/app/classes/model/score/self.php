<?php
class Model_Score_Self
{


  public static function getSelfScores()
  {
    $username = Auth::get_screen_name();
    $team_id = Model_Team::get_teams();

    $query = <<<__QUERY__
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
__QUERY__;

    $result = DB::query($query)->execute()->as_array();

    return $result;
  }
}
