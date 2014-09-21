<?php

class Controller_Score extends Controller_Base
{
	public function before()
	{
		parent::before();
	}

	public function action_record_team()
	{
		// 所属チームがなければエラーページ
		if (is_null(Model_Player::get_my_team_id()))
		{
			return Response::forge(View::forge('score/not_belong_team.twig'));
		}

		$view = View::forge('score/record_team.twig');

		$view->stat = Model_Score_Team::getTeamScore();
		$view->team_id = Model_Player::get_my_team_id();
		$view->team_name = Model_Player::get_my_team_name();

		$view->game_infos = Model_Score_Team::getTeamGameInfo();
		$view->game_result = Model_Score_Team::getTeamWinLose($view->team_id, $view->game_infos);

		return Response::forge($view);
	}

	public function action_record_self()
	{
		$view = View::forge('score/record_self.twig');

		$view->stats = Model_Score_Self::getSelfScores();
		$view->dispname = Common::get_dispname();

		return Response::forge($view);
	}
}
