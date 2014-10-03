<?php

class Model_Stats_Common extends Model_Base
{
	/**
	 * 成績入力の完了していない試合をreturnする
	 *
	 * @param string player_id
	 */
	public static function get_stats_alerts($team_id, $player_id)
	{
		$return = array();

		// hitting
		$hittings = Model_Stats_Hitting::query()
			->related('games')
			->related('games_teams')
			->where(array(
				array('player_id', $player_id),
				array('input_status', 'save'),
				array('games.game_status', '=', '1'),
			))->get();

		foreach ($hittings as $hitting)
		{
			$return['hittings'][] = array(
				'game_id' => $hitting->game_id,
				'date' => $hitting->games->date,
				'opponent_team_name' => $hitting->games_teams->opponent_team_name,
			);
		}

		// pitching
		$pitchings = Model_Stats_Pitching::query()
			->related('games')
			->related('games_teams')
			->where(array(
				array('player_id', $player_id),
				array('input_status', 'save'),
				array('games.game_status', '1'),
			))->get();

		foreach ($pitchings as $pitching)
		{
			$return['pitchings'][] = array(
				'game_id' => $pitching->game_id,
				'date' => $pitching->games->date,
				'opponent_team_name' => $pitching->games_teams->opponent_team_name,
			);
		}

		// team_admin
		if (Model_Player::has_team_admin($team_id))
		{
			$games = Model_Game::query()->related('games_teams')->where(array(
				array('game_status', '1'),
				array('games_teams.team_id', $team_id),
			))->get();

			foreach ($games as $game)
			{
				$return['games'][] = array(
					'game_id' => $game->id,
					'date' => $game->date,
					'opponent_team_name' => $game->games_teams->opponent_team_name,
				);
			}
		}

		return $return;
	}
}
