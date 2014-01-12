<?php

class Controller_Top extends Controller_Base
{
	public function action_index()
	{
    $view = View::forge('top/index.twig');

    if ( $this->_login )
    {
      $view->set('login', 'ほげほげさんでログイン');
    }
    else
    {
      Auth::logout(); 
      $this->_login_form->repopulate();
      $view->set_safe('login', $this->_login_form->build(Uri::create('/')));
    }

		return Response::forge($view);
	}
}
