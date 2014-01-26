<?php

class Controller_Game extends Controller_Base
{
  public function before()
  {
    parent::before();

    if ( ! Auth::check() )
    {
      Session::set('redirect_to', Uri::current());
      Response::redirect(Uri::create('/login'));
    }
  }

  public function action_score()
  {
    $id = Input::param('id');
    if ( ! $id )
    {
      Response::redirect(Uri::create('/game/list'));
    }

    $score = Model_Score::find(Input::param('id'), array(
      'related' => array('games'),
    ));

    if ( Input::post() )
    {      
      $form = Fieldset::forge('score');
      $form->add_model($score);

      $val = $form->validation();
      if ( $val->run() )
      {
        $fields = $val->validated();
        unset($fields['submit']);

        $score = Model_Score::find($id);
        $score->set($fields);
        $score->save(); 

        Response::redirect(Uri::create('/game/list'));
      }
      else {
        Session::set_flash('error', $val->show_errors());
      }
    }

    $view = View::forge('game/score.twig');
    $view->score = $score;
    $view->team_top    = Model_Team::find($score->games->team_top)->name;
    $view->team_bottom = Model_Team::find($score->games->team_bottom)->name;

    return Response::forge($view);
  }

  public function action_list()
  {
    $form = self::_get_addgame_form();

    $view = View::forge('game/list.twig');
    $view->set_safe('form', $form->build(Uri::current()));
    $games = Model_Game::getOwnGames();

    $view->games = $games;
    
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

      if ( $game_status === 0 && ! Auth::has_access('admin.admin') )
      {
        Session::set_flash('error', '自分のチームを選択してください');
      }
      else
      {
        try {
          $game = Model_Game::forge();
          $game->date        = Input::post('date');
          $game->team_top    = $top;
          $game->team_bottom = $bottom;
          $game->game_status = $game_status;
          $game->save();

          Model_Score::createNewGame($game->id);
 
          Session::set_flash('info', '新規ゲームを追加しました');
          Response::redirect(Uri::current());
        }
        catch ( Exception $e )
        {
          Session::set_flash('error', $e->getMessage());
        }
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
    $json = array(
      array(
        'order' => 1,
        'member_id' => 32,
        'position' => array(1,2,3,4,5,'R',5),
      ),
      array(),
      array(),
      array(),
      array(),
      array(),
      array(),
    );
    // ゲームデータ表示
    // 権限のあるチームのみ表示

    // 複数権限もっている場合はタブで両方表示

    $view = View::forge('game/edit.twig');

    $view->members = Model_Member::find('all', array(
      'where' => array(
        array('team', Input::get('team_id')),
      ),
    ));

    $stamens = Model_Game::find('all', array(
      'select' => array('starting_member'),
      'where' => array(
		    array('id', Input::get('game_id')),
      ),
    ));

    // stamens to json

    $view->stamens = $stamens;

    return Response::forge($view);
	}

  public function post_edit()
  {

  }

	public function post_status()
	{
    $game = Model_Game::find(Input::post('id'));
    $game->game_status = Input::post('status');
    $game->save();

    return "OK";
	}

  static private function _get_deletegame_form($id)
  {
    $form = Fieldset::forge('deletegame', array(
      'form_attributes' => array(
        'class' => 'form',
        'role'  => 'search',
      ),
    ));

    $form->add('id', '', array('type' => 'hidden', 'value' => $id))
      ->add_rule('required');

    $form->add('confirm', '', array('type' => 'hidden', 'value' => '1'))
      ->add_rule('required');

    $form->add('submit', '', array('type' => 'submit', 'value' => '無効', 'class' => 'btn btn-warning'));

    return $form;
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
