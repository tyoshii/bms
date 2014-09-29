<?php

class Model_Score_Self
{
	/**
	 * 個人成績取得
	 *
	 * @param team_id
	 * @param is_regulation 規定打席を考慮するかどうか
	 */
	public static function get_self_scores($team_id = null, $is_regulation = true)
	{
		if (is_null($team_id))
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
		p.team_id = $team_id AND
		p.status != -1

GROUP BY
		s.player_id

ORDER BY
		G DESC
;
__QUERY__;

		$result = DB::query($query)->execute()->as_array();

		// 規定打席
		$total_games = count(Model_Games_Team::query()->where('team_id', $team_id)->get());
		$regulation_at_bats = Model_Team::find($team_id)->regulation_at_bats;

		foreach ($result as $index => $res)
		{
			// 規定打席以下のデータを削除
			if ($is_regulation)
			{
				if ($res['TPA'] < ($total_games * $regulation_at_bats))
				{
					unset($result[$index]);
					continue;
				}
			}

			// 安打数合計や打率など
			Model_Score_Team::give_stats($res);
			$result[$index] = $res;
		}

		return $result;
	}
}
