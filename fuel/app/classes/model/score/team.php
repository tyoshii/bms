<?php

class Model_Score_Team
{

	public static function get_team_score($team_id = null)
	{
		if (is_null($team_id))
		{
			$team_id = Model_Player::get_my_team_id();
		}

		$query = <<<__QUERY__
SELECT
		s.game_id,
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
		(SELECT sum(E) from stats_fieldings where team_id = $team_id) as E
FROM
		stats_hittings AS s

LEFT JOIN
		games as g
ON
		s.game_id = g.id

LEFT JOIN
		players AS p
ON
		s.player_id = p.id

LEFT JOIN
		teams AS  t
ON
		t.id = p.team_id

WHERE
		p.status != -1       AND
		s.team_id = $team_id AND
		g.game_status = 2	

GROUP BY
		s.team_id
;
__QUERY__;

		$result = DB::query($query)->execute()->as_array();
		$result = reset($result);

		self::give_stats($result);

		return $result;
	}

	/**
	 * 安打数合計や打率などを配列に付与
	 */
	public static function give_stats(&$stats)
	{
		if ( ! $stats)
		{
			return $stats = array();
		}

		// 成績追加
		// - total base : 塁打
		$stats['TB'] = $stats['H'] + 1 * $stats['2B'] + 2 * $stats['3B'] + 3 * $stats['HR'];

		// 合計
		// - total hit : 安打数
		$stats['total']['TH'] = $stats['H'] + $stats['2B'] + $stats['3B'] + $stats['HR'];
		// - total bb : 四死球数
		$stats['total']['TBB'] = $stats['BB'] + $stats['HBP'];
		// - total sf : 犠打犠飛合計
		$stats['total']['TSF'] = $stats['SAC'] + $stats['SF'];
		// - total on-base : 出塁した記録
		$stats['total']['TOB'] = $stats['total']['TH'] + $stats['total']['TBB'];
		// - total hitting appearance : 打撃機会
		$stats['total']['THA'] = $stats['AB'] + $stats['total']['TBB'] + $stats['SF'];

		// 率計算
		$stats['rate'] = array(
			'AVG' => '0.000', // 打率
			'OBP' => '0.000', // 出塁率
			'SLG' => '0.000', // 長打率
			'OPS' => '0.000',
			'SOR' => '0.000', // 三振率 SwingOutRate
		);

		if ($stats['AB'] !== 0 and $stats['AB'] !== '0')
		{
			$stats['rate']['AVG'] = sprintf('%.3f', $stats['total']['TH'] / $stats['AB']);
			$stats['rate']['OBP'] = sprintf('%.3f', ($stats['total']['TH'] + $stats['total']['TBB']) / $stats['total']['THA']);
			$stats['rate']['SLG'] = sprintf('%.3f', $stats['TB'] / $stats['AB']);
			$stats['rate']['OPS'] = sprintf('%.3f', $stats['rate']['OBP'] + $stats['rate']['SLG']);
			$stats['rate']['SOR'] = sprintf('%.3f', $stats['SO'] / $stats['AB']);
		};
	}

	public static function get_team_game_info($team_id = null)
	{
		if ( ! $team_id)
		{
			$team_id = Model_Player::get_my_team_id();
		}

		$query = <<<__QUERY__
SELECT
	g.id,
	gr.tsum,
	gr.bsum,
	gt.order,
	g.team_top,
	g.team_bottom,
	g.game_status,
	g.team_top_name,
	g.team_bottom_name,
	g.date
FROM
	games as g

LEFT JOIN
	games_runningscores as gr
ON
	g.id = gr.game_id

LEFT JOIN
	games_teams as gt
ON
	g.id = gt.game_id

WHERE
	gt.team_id = $team_id AND
	g.game_status = 2

ORDER BY
	g.date DESC
;
__QUERY__;

		$result = DB::query($query)->execute()->as_array();

		return $result;
	}

	// TODO: 引数のinfosはいらない
	public static function get_team_win_lose($team_id, $infos)
	{
		$infos = self::get_team_game_info($team_id);

		$ret = array(
			'games' => count($infos),
			'win'   => 0,
			'lose'  => 0,
			'draw'  => 0,
			'rate'  => array(
				'win'  => 0.000,
				'lose' => 0.000,
			),
		);

		// 勝敗
		foreach ($infos as $info)
		{
			if ($info['tsum'] > $info['bsum'])
			{
				$info['order'] == 'top' ? ++$ret['win'] : ++$ret['lose'];
			}
			elseif ($info['tsum'] < $info['bsum'])
			{
				$info['order'] == 'top' ? ++$ret['lose'] : ++$ret['win'];
			}
			else
			{
				++$ret['draw'];
			}
		}

		// 勝率計算
		if ($ret['games'] !== 0)
		{
			$ret['rate']['win'] = sprintf('%.3f', $ret['win'] / $ret['games']);
			$ret['rate']['lose'] = sprintf('%.3f', $ret['lose'] / $ret['games']);
		}

		return $ret;
	}
}
