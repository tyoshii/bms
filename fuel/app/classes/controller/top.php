<?php

class Controller_Top extends Controller_Base
{
	public function action_index()
	{
		$view = Theme::instance()->view('top.twig');

		if (Auth::check())
		{
			// 所属チーム
			$view->teams = Model_Team::get_belong_team();
		}
		else
		{
			Auth::logout();
			$this->_login_form->repopulate();
			$view->set_safe('login_form', $this->_login_form->build(Uri::create('login')));
		}

		return Response::forge($view);
	}

	public function action_login()
	{
		if (Auth::check())
			return Response::redirect('/');

		if (Input::get('url'))
			Session::set('redirect_to', Input::get('url'));

		$view = View::forge('login.twig');
		$view->set_safe('form', $this->_login_form->build(Uri::current()));

		return Response::forge($view);
	}

	public function action_logout()
	{
		Auth::logout();
		return Response::redirect(Uri::create('/'));
	}

	public function action_404()
	{
		return Response::forge(View::forge('errors/404.twig'), 404);
	}
}
