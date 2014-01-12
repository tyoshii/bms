<?php

class Controller_Top extends Controller_Base
{
	public function action_index()
	{
    $view = View::forge('top/index.twig');

    if ( $this->_login )
    {
      $view->set('screen_name', Auth::Instance()->get_user_array()['screen_name']);
    }
    else
    {
      Auth::logout(); 
      $this->_login_form->repopulate();
      $view->set_safe('login_form', $this->_login_form->build(Uri::create('/')));
    }

		return Response::forge($view);
	}

  public function action_logout()
  {
    Auth::logout();
    Response::redirect(Uri::create('/'));
  }
}
