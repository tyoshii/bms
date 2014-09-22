<?php

namespace Fuel\Tasks;

class Json2mysql
{
	/**
	 * games_statsに入っているjsonフォーマットの情報をRDBMSへ
	 *
	 * games_stats.others => games / stats_award
	 */
	public function other()
	{
		// 対象game_idを取得
		$game_ids = \DB::select('id')->from('games')->execute()->as_array();

		// game_idsごとに処理
		foreach ($game_ids as $game_id)
		{
			$game_id = $game_id['id'];
			echo "execute game_id = {$game_id}\n";

			$others = \Model_Games_Stat::find_by_game_id($game_id);
			if ( ! $others) continue;
			if ( ! $others->others) continue;

			$team_id = $others->team_id;

			if ($stats = json_decode($others->others, true))
			{
				// updateしたロジックで既に動いている場合はスキップ
				if (array_key_exists('mvp', $stats)) continue;

				// award
				$awards = array(
					'mvp_player_id'        => $stats['mip2'],
					'second_mvp_player_id' => $stats['mip1'],
				);
				\Model_Stats_Award::regist($game_id, $team_id, $awards);

				// stadium/memo
				$game = \Model_Game::find($game_id);
				$game->stadium = $stats['place'];
				$game->memo = $stats['memo'];
				$game->save();

				echo "game_id={$game_id}のothersをRDBMSへコピーしました。\n";
			}
		}
	}

	/**
	 * games_statsに入っているjsonフォーマットの情報をRDBMSへ
	 *
	 * games_stats.players  => stats_players
	 * games_stats.pitchers => stats_pitchings
	 * games_stats.batters  => stats_hittings ( stats_hittingdetails )
	 */
	public function player_pitcher_batter($game_id = NULL)
	{
		// 対象game_idを取得
		$game_ids = '';
		if ($game_id)
		{
			$game_ids = array($game_id);
		}
		else
		{
			$game_ids = \DB::select('id')->from('games')->execute()->as_array();
		}

		foreach ($game_ids as $id)
		{
			// ミラー対象のデータを取得
			$results = \DB::select('game_id', 'team_id', 'players', 'pitchers', 'batters')
				->from('games_stats')
				->execute()->as_array();

			// 1つずつパースしてregist
			foreach ($results as $result)
			{
				$ids = array(
					'game_id' => $result['game_id'],
					'team_id' => $result['team_id'],
				);

				// players
				if ($players = json_decode($result['players'], true))
				{
					// データ整形
					foreach ($players as $key => $val)
					{
						$players[$key]['player_id'] = $val['member_id'];
					}

					// regist
					\Model_Stats_Player::regist_player($ids, $players);
				}

				// pitchers
				if ($stats = json_decode($result['pitchers'], true))
				{
					$require_keys = array(
						'result',
						'inning_int', 'inning_frac',
						'hianda', 'sanshin', 'shishikyuu',
						'earned_runs', 'runs',
					);

					// データ整形
					foreach ($stats as $player_id => $stat)
					{
						if ( ! $stat) continue;

						foreach ($require_keys as $key)
						{
							if (array_key_exists($key, $stat))
								$stats[$player_id][$key] = $stat[$key];
							else
								$stats[$player_id][$key] = 0;
						}
					}

					// regist
					\Model_Stats_Pitching::replace_all($ids, $stats);
				}

				// batters
				if ($stats = json_decode($result['batters'], true))
				{
					// regist
					\Model_Stats_Hitting::replace_all($ids, $stats);
				}
			}
		}

		echo "DONE !!";
	}
}
/* End of file tasks/json2mysql.php */
