<?php

class Model_Score_Self
{
	public static function getSelfScores($team_id = null)
	{
		if ( ! $team_id)
		{
			$team_id = Model_Player::get_my_team_id();
		}

		$query = <<<__QUERY__
SELECT
		s.player_id,
    p.number,
		t.name as team,
		p.name,
		count(s.id) as G,
		sum(s.TPA) as TPA,
		sum(s.AB)  as AB,
		sum(s.H)   as H,
		sum(s.2B)  as 2B,
		sum(s.3B)  as 3B,
		sum(s.HR)  as HR,
		sum(s.RBI) as RBI,
		sum(s.R)   as R,
		sum(s.SO)  as SO,
		sum(s.BB)  as BB,
		sum(s.HBP) as HBP,
		sum(s.SAC) as SAC,
		sum(s.SF)  as SF,
		sum(s.SB)  as SB,
		(SELECT sum(E) from stats_fieldings where player_id = s.player_id) as E
FROM
    stats_hittings AS s

LEFT JOIN
    players AS p
ON
    s.player_id = p.id

LEFT JOIN
    teams AS  t
ON
    t.id = p.team_id

WHERE
    p.team_id = $team_id AND p.status != -1

GROUP BY
    s.player_id

ORDER BY
		G DESC
;
__QUERY__;

		$result = DB::query($query)->execute()->as_array();

		foreach ($result as $index => $res)
		{
			Model_Score_Team::give_stats($res);
			$result[$index] = $res;
		}

		return $result;
	}
}
