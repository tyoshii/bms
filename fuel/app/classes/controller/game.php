<?php

class Controller_Game extends Controller_Base
{
  public function before()
  {
    parent::before();

    if ( ! Auth::check() )
    {
      Session::set('redirect_to', Uri::current(), '', Input::get());
      Response::redirect(Uri::create('/login'));
    }
  }

  public function action_score($game_id)
  {
    if ( ! $game_id )
    {
      Response::redirect(Uri::create('/game/list'));
    }

    $score = Model_Games_Runningscore::find($game_id, array(
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

        $score = Model_Games_Runningscore::find($game_id);
        $score->set($fields);
        $score->save(); 

        Response::redirect(Uri::create('/game'));
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
      if ( Auth::has_access('admin.admin') )
      {
        // 管理者登録の試合
        $game_status = 3;
      }

      if ( $game_status === 0 && ! Auth::has_access('admin.admin') )
      {
        Session::set_flash('error', '自分のチームを選択してください');
      }
      else
      {
        try {
          Model_Game::createNewGame($top, $bottom, $game_status);

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

  public function action_edit($game_id = null, $team_id = null, $kind = '')
  {
    // error check
    if ( ! is_int($game_id+0) or ! is_int($team_id+0) )
    {
      Session::set_flash('error', '試合一覧に戻されました');
      Response::redirect(Uri::create('/game/list'));
    }
    if ( ! in_array($kind, array('player', 'pitcher', 'batter')) )
    {
      Session::set_flash('error', '試合一覧に戻されました');
      Response::redirect(Uri::create('/game/list'));
    }

    $view = View::forge("game/{$kind}.twig");

    // 所属選手
    $view->members = Model_Player::find('all', array(
      'where' => array(
        array('team', $team_id),
      ),
    ));

    // players
    $stat = Model_Games_Stat::query()
                        ->where('game_id', $game_id)
                        ->where('team_id', $team_id)
                        ->get_one();

    $view->players = json_decode($stat->players);

    switch ( $kind )
    {
      case 'player':
        break;

      case 'pitcher':
        $view->pitchers = json_decode($stat->pitchers);
        break;

      case 'batter':
        $view->batters = json_decode($stat->batters);
        $view->results = Model_Batter_Result::find('all');
        break;

      default:
        break;
    }

    $view->game_id = $game_id;
    $view->team_id = $team_id;

    $game = Model_Game::find($game_id);

    // チーム名
    $view->team_top = Model_Team::find($game->team_top)->name;
    $view->team_bottom = Model_Team::find($game->team_bottom)->name;

    // 試合日
    $view->date = $game->date;

    return Response::forge($view);
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
