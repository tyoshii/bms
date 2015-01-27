<?php

class Controller_Convention_Game extends Controller_Base
{
	public $view = '';

	public function before()
	{
		parent::before();

		// view set
		$action = Request::main()->action;
		$this->view = View::forge('convention/game/'.$action.'.twig');
		
		// debug
		echo $this->param('convention_id');
		echo $this->param('game_id');
	}

	/**
	 * display convention game list
	 */
	public function action_index()
	{
		return Response::forge($this->view);
	}
	
	/**
	 * display convention game detail
	 */
	public function action_detail()
	{
		return Response::forge($this->view);
	}

	/**
	 * add convention game
	 */
	public function action_add()
	{
		return Response::forge($this->view);
	}

	public function post_add()
	{
		return Response::foreg($this->view);
	}
	
	/**
	 * update convention game
	 */
	public function action_update()
	{
		return Response::forge($this->view);
	}

	public function post_update()
	{
		return Response::forge($this->view);
	}
}
