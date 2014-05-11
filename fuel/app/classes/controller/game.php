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

  public function action_list()
  {
    $view = View::forge('game/list.twig');

    $form = self::_get_addgame_form();
    $view->set_safe('form', $form->build(Uri::current()));

    $view->games = Model_Game::getGames();
    
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
      $my_team = Model_Player::getMyTeamId();

      $game_status = 0;
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
    $view->games = Model_Game::getGames();
    
    return Response::forge($view);
  }

  public function action_edit($game_id = null, $kind = '', $team_id = null)
  {
    // error check
    if ( ! is_int($game_id+0) || ! is_int($team_id+0) )
    {
      Session::set_flash('error', '試合一覧に戻されました');
      Response::redirect(Uri::create('/game'));
    }
    if ( ! in_array($kind, array('score', 'player','pitcher','batter','other')) )
    {
      Session::set_flash('error', '試合一覧に戻されました');
      Response::redirect(Uri::create('/game'));
    }

    $view = View::forge("game/{$kind}.twig");

    // team_idが空の時は、ログイン中ユーザーの所属チームIDを
    if ( ! $team_id )
      $team_id = Model_Player::getMyTeamId();

    // 所属選手
    $view->members = Model_Player::getMembers($team_id);

    // players
    $view->metum = Model_Stats_Player::getStarter($game_id, $team_id);

    switch ( $kind )
    {
      case 'score':
        $view->score = Model_Games_Runningscore::find($game_id, array(
          'related' => array('games'),
        ));
        break;

      case 'player':
        break;

      case 'pitcher':
        $view->stats_pitchings = Model_Stats_Player::getPitchingStats($game_id, $team_id);
        break;

      case 'batter':
        // 打席結果一覧
        $view->results = Model_Batter_Result::getAll();

        // 成績
        $view->hittings  = Model_Stat::getStats('stats_hittings', $game_id, 'player_id');
        $view->details   = Model_Stats_Hittingdetail::getStats($game_id); 
        $view->fieldings = Model_Stat::getStats('stats_fieldings', $game_id, 'player_id');
        break;

      case 'other':
        $stat = Model_Games_Stat::query()
                        ->where('game_id', $game_id)
                        ->where('team_id', $team_id)
                        ->get_one();

        $view->others = json_decode($stat->others);
        break;

      default:
        break;
    }

    // ID
    $view->game_id = $game_id;
    $view->team_id = $team_id;

    // 試合情報
    $game = Model_Game::find($game_id);

    // チーム名
    $view->team_top = Model_Team::find($game->team_top)->name;
    $view->team_bottom = Model_Team::find($game->team_bottom)->name;

    // 試合日
    $view->date = $game->date;

    return Response::forge($view);
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
