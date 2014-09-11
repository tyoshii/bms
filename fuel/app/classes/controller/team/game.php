<?php

class Controller_Team_Game extends Controller_Team
{
	public $_game;

	public function before()
	{
		parent::before();

		// game 情報
		if ( $game_id = $this->param('game_id') )
		{
			$this->$_game = Model_Game::find($game_id);
		}
		else
		{
			Session::set_flash('error', '存在しないURLです。');
			return Response::redirect('error/404');
		}
	}

	public function action_add()
	{
	}

	public function action_summary()
	{
	}

	public function action_edit()
	{
	}
}
