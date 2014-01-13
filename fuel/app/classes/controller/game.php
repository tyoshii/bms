<?php

class Controller_Game extends Controller_Base
{
  public function action_list()
  {
    $form = self::_get_addgame_form();

    $view = View::forge('game/list.twig');
    $view->set_safe('form', $form->build(Uri::current()));
    $view->games = Model_Game::getOwnGames();
    
    return Response::forge($view);
  } 

  public function post_list()
  {
    $form = self::_get_addgame_form();

    $val = $form->validation();
    if ($val->run())
    {
      $top     = Input::post('top');
      $bottom  = Input::post('bottom');
      $my_team = Model_User::getMyTeamId();

      $game_status = 0;
      if ( $top === $my_team AND $bottom === $my_team )
      {
        // 紅白戦
        $game_status = 2;
      }
      if ( $top === $my_team OR $bottom === $my_team )
      {
        // 自分のチームの試合
        $game_status = 1;
      }

      try {
        $game = Model_Game::forge();
        $game->date        = Input::post('date');
        $game->team_top    = $top;
        $game->team_bottom = $bottom;
        $game->game_status = $game_status;
        
        $game->save();

        Session::set_flash('info', '新規ゲームを追加しました');
        Response::redirect(Uri::current());
      }
      catch ( Exception $e )
      {
        Session::set_flash('error', $e->getMessage());
      }
    }
    else
    {
      Session::set_flash('error', $val->show_errors());
    }

    $form->repopulate();

    $view = View::forge('game/list.twig');
    $view->set_safe('form', $form->build(Uri::current()));
    $view->games = Model_Game::getOwnGames();
    
    return Response::forge($view);
  }

	public function action_edit()
	{
    // ゲームデータ表示
    // 権限のあるチームのみ表示

    // 複数権限もっている場合はタブで両方表示

    $view = View::forge('game/edit.twig');

    return Response::forge($view);
	}

  public function post_edit()
  {

  }

	public function action_delete()
	{
    // Fieldset::forge

    return Response::forge( View::forge('game/delete.twig') );
	}

  public function post_delete()
  {
    // validation

    // delete

    Response::redirect(Uri::create('/game/list'));
  }

  static private function _get_addgame_form()
  {
    $form = Fieldset::forge('addgame', array(
      'form_attributes' => array(
        'class' => 'form',
        'role'  => 'search',
      ),
    ));

    $form->add('date', '', array('class' => 'form-control form-datepicker', 'placeholder' => '試合実施日', 'data-date-format' => 'yyyy-mm-dd'))
      ->add_rule('required')
      ->add_rule('trim');

    // option - チーム選択
    $default = array( '' => '' );
    $teams = Model_Team::getTeams();

    $form->add('top', '', array('options' => $default+$teams, 'type' => 'select', 'class' => 'form-control chosen-select', 'data-placeholder' => '先攻'))
      ->add_rule('in_array', array_keys($teams));

    $form->add('bottom', '', array('options' => $default+$teams, 'type' => 'select', 'class' => 'form-control chosen-select', 'data-placeholder' => '後攻'))
      ->add_rule('in_array', array_keys($teams));

    $form->add('addgame', '', array('type' => 'submit', 'value' => '追加', 'class' => 'btn btn-success'));

    return $form;
  }
}
