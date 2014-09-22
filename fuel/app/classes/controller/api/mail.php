<?php

class Controller_Api_Mail extends Controller_Rest
{
	public function router($resource, $arguments)
	{
		// acl
		if ( ! Model_Player::has_team_admin(Input::post('team_id')))
		{
			Log::warning('権限の無い、不正アクセス');
			return Response::redirect('error/403');
		}

		parent::router($resource, $arguments);
	}

	public function post_remind()
	{
		// validate
		$val = Validation::forge();
		$val->add('game_id', 'game_id')->add_rule('required');
		$val->add('team_id', 'team_id')->add_rule('required');

		if ( ! $val->run())
		{
			Log::warning($val->show_errors());
			return Response::forge('不正なアクセスです。', 400);
		}

		// parameter
		$game_id = Input::post('game_id');
		$team_id = Input::post('team_id');

		// game status check
		if (Model_Game::get_game_status($game_id, $team_id) !== '1')
		{
			return Response::forge('成績入力中の試合のみリマインドできます', 400);
		}

		// remind
		Model_Game::remind_mail($game_id, $team_id);

		echo 'OK';
	}
}
