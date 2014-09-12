<?php

class Controller_Team_Game extends Controller_Team
{
	public $_game = array();

	public function before()
	{
		parent::before();

		// game 情報
		if ( $game_id = $this->param('game_id') )
		{
			if ( ! $this->_game = Model_Game::find($game_id) )
			{
				Session::set_flash('error', '試合情報が取得できませんでした。');
				return Response::redirect('team/'.self::$_team->url_path);
			}
		}

		// set global
		$this->set_global('game', $this->_game);
	}

	public function action_add()
	{
		$view = View::forge('team/game/add.twig');
		return Response::forge($view);
	}

	public function action_detail()
	{
		$view = View::forge('team/game/detail.twig');
		return Response::forge($view);
	}

	public function action_edit()
	{
		$view = View::forge('team/game/edit.twig');
		return Response::forge($view);
	}
}
