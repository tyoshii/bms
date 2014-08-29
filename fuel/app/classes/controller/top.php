<?php

class Controller_Top extends Controller_Base
{
	public function action_index()
	{
    $view = Theme::instance()->view('top.twig');

    if ( Auth::check() )
    {
      if ( $player = Model_Player::find_by_username(Auth::get_screen_name()) )
      {
        // アラート
        $view->alert_games = Model_Game::get_incomplete_gameids($player->id);
        // 最近の試合
        $view->games = Model_Game::getGamesOnlyMyTeam();
      }
      else
      {
        $view->no_belong_team = true;
      }
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
    if ( Auth::check() )
      return Response::redirect('/');

    if ( Input::get('url') )
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
}
