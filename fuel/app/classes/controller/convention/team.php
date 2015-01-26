<?php

class Controller_Convention_Team extends Controller_Base
{
	public $view = '';

	public function before()
	{
		parent::before();

		// view set
		$action = Request::main()->action;
		$this->view = View::forge('convention/team/'.$action.'.twig');
	}

	/**
	 * display convention team list
	 */
	public function action_index()
	{
		return Response::forge($this->view);
	}
	
	/**
	 * add convention team
	 */
	public function action_add()
	{
		return Response::forge($this->view);
	}

	public function post_add()
	{
		return Response::foreg($this->view);
	}
}
