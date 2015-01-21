<?php

class Controller_Convention extends Controller_Base
{
	public $view = '';

	public function before()
	{
		parent::before();

		// view set
		$action = Request::main()->action;
		$this->view = View::forge('convention/'.$action.'.twig');

		// debug
		echo $this->param('convention_id');
	}

	/**
	 * display convention list
	 */
	public function action_index()
	{
		return Response::forge($this->view);
	}
	
	/**
	 * add convention
	 */
	public function action_add()
	{
		return Response::forge($this->view);
	}

	public function post_add()
	{
		return Response::forge($this->view);
	}

	/**
	 * convention detail
	 */
	public function action_detail()
	{
		return Response::forge($this->view);
	}
	
	/**
	 * convention update
	 */
	public function action_update()
	{
		return Response::forge($this->view);
	}
	public function post_update()
	{
		return Response::forge($this->view);
	}

	/**
	 * convention stats
	 */
	public function action_stats()
	{
		return Response::forge($this->view);
	}
	
	/**
	 * convention games list
	 */
	public function action_games()
	{
		return Response::forge($this->view);
	}
}
