<?php

class Controller_Game extends Controller_Base
{
  public function before()
  {
    parent::before();

    $action = Request::main()->action;
    if ( $action === 'edit' && ! Auth::check() )
    {
      Session::set('redirect_to', Uri::current(), '', Input::get());
      Response::redirect(Uri::create('/login'));
    }
  }

  public function action_add()
  {
    echo "新規ゲーム追加";
  }

  public function action_summary()
  {
    $game_id = $this->param('game_id');

    $view = View::forge('game/summary.twig');

    $info = Model_Game::find($game_id);
    if ( ! $info )
    {
      return Response::redirect('_404_');
    }

    $view->info  = $info;
    $view->score = Model_Games_Runningscore::find_by_game_id($game_id);

    // stats
    $view->player_top    = Model_Stats_Player::getStarter($game_id, $info['team_top']); 
    $view->player_bottom = Model_Stats_Player::getStarter($game_id, $info['team_bottom']); 

    $view->hitting_top    = Model_Stats_Hitting::get_stats($game_id, $info['team_top']);
    $view->hitting_bottom = Model_Stats_Hitting::get_stats($game_id, $info['team_bottom']);

    $view->pitching_top    = Model_Stats_Pitching::get_stats(array(
      'game_id' => $game_id,
      'team_id' => $info['team_top'],
    ));
    $view->pitching_bottom = Model_Stats_Pitching::get_stats(array(
      'game_id' => $game_id,
      'team_id' => $info['team_bottom'],
    ));

    // other
    $view->my_team_id = Model_Player::get_my_team_id();

    return Response::forge($view);
  }

  public function action_list()
  {
    $view = View::forge('game/list.twig');

    $form = self::_get_addgame_form();
    $view->set_safe('form', $form->build(Uri::current()));

    $view->games   = Model_Game::get_info();
    $view->team_id = Model_Player::get_my_team_id() ?: 0;
    
    return Response::forge($view);
  } 

  public function post_list()
  {
    $form = self::_get_addgame_form();

    $val = $form->validation();
    if ( $val->run() )
    {
      if ( self::_addgame_myvalidation() )
      {
        if ( Model_Game::createNewGame(Input::post()) )
        {
          Session::set_flash('info', '新規ゲームを追加しました');
          Response::redirect(Uri::current());
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
    $view->games = Model_Game::get_info();
    
    return Response::forge($view);
  }

  public function action_edit()
  {
    // get param
    $game_id = $this->param('game_id', null);
    $team_id = $this->param('team_id', null);
    $kind    = $this->param('kind', '');

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

    // view load
    $view = Theme::instance()->view("game/{$kind}.twig");

    // team_idが空の時は、ログイン中ユーザーの所属チームIDを
    if ( ! $team_id )
      $team_id = Model_Player::get_my_team_id();

    // 所属選手
    $view->players = Model_Player::get_players($team_id);

    // 出場選手
    // TODO: metumという変数は微妙だな・・・playeds ?
    $view->metum = Model_Stats_Player::getStarter($game_id, $team_id);

    // game_status
    $view->game_status = Model_Game::get_game_status($game_id, $team_id);

    switch ( $kind )
    {
      case 'score':

        list($view->scores, $view->tsum, $view->bsum)
          = Model_Games_Runningscore::get_score($game_id);
        
        break;

      case 'player':
        break;

      case 'pitcher':
        // ピッチャーだけにフィルター
        $view->metum = self::_filter_only_pitcher($view->metum);

        // 成績
        $view->stats = Model_Stats_Pitching::get_stats(array('game_id' => $game_id));
        break;

      case 'batter':
        // 打席結果一覧
        $view->results = Model_Batter_Result::getAll();

        // ログイン中ユーザのデータだけにフィルタ
        if ( ! Auth::has_access('admin.admin') )
          $view->metum = self::_filter_only_loginuser($view->metum);

        // 成績
        $where = array(
          'game_id' => $game_id,
          'team_id' => $team_id,
        );
        $view->hittings  = Model_Stats_Hitting::getStats($where);
        $view->details   = Model_Stats_Hittingdetail::getStats($where); 
        $view->fieldings = Model_Stats_Fielding::getStats($where);
        break;

      case 'other':
        // TODO: いつか消す
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
    $view->game_id   = $game_id;
    $view->team_id   = $team_id;
    $view->team_name = Model_Team::find($team_id)->name;

    // 試合情報
    // TODO: gameinfo としてまとめたい
    $game = Model_Game::find($game_id);
    $view->gameinfo = $game;

    // チーム名
    $view->team_top    = $game->team_top_name;
    $view->team_bottom = $game->team_bottom_name;

    // 試合日
    $view->date = $game->date;
        
    // 表彰
    // TODO: 試合概要だけにあればよいが、othersでも使っていたためここで
    $award = Model_Stats_Award::get_stats($game_id, $team_id);
    $view->mvp        = $award->mvp_player_id;
    $view->second_mvp = $award->second_mvp_player_id;


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

    // 試合実施日
    $form->add('date', '試合実施日', array(
      'class'            => 'form-control form-datepicker',
      'placeholder'      => '試合実施日',
      'value'        => date('Y-m-d'),
      'data-date-format' => 'yyyy-mm-dd',
    ))
      ->add_rule('required')
      ->add_rule('trim');

    // チーム選択
    $teams = array('' => '') + Model_Team::get_teams_key_value();

    $attrs = array(
      'type'    => 'select',
      'options' => $teams,
      'class'   => 'select2',
    );

    // - 先攻
    $form->add('top', '先攻', $attrs + array(
      'value'            => Model_Player::get_my_team_id(), // デフォルトで自分のチーム
      'data-placeholder' => 'チームを選択',
    ))
      ->add_rule('in_array', array_keys($teams));

    $form->add('top_name', '', array(
      'type' => 'text',
      'class' => 'form_control',
      'placeholder' => 'or 直接入力',
    ))
      ->add_rule('max_length', 100);

    // - 後攻
    $form->add('bottom', '後攻', $attrs + array(
      'data-placeholder' => 'チームを選択',
    ))
      ->add_rule('in_array', array_keys($teams));

    $form->add('bottom_name', '', array(
      'type' => 'text',
      'class' => 'form_control',
      'placeholder' => 'or 直接入力',
    ))
      ->add_rule('max_length', 100);

    // 先行後攻の入れ替え
/*
    $form->add_before('change', '', array(
      'type'    => 'button',
      'class'   => 'btn btn-success btn-xs',
      'value'   => "<span class='glyphicon glyphicon-sort'></span>",
      'onClick' => 'change_topbottom();',
    ), array(), 'bottom');
*/

    // submit
    $form->add('addgame', '', array(
      'type'  => 'submit',
      'value' => '追加',
      'class' => 'btn btn-success',
    ));

    return $form;
  }
  
  // - TODO validationクラスへ独自validationを追加するのが本当は綺麗
  private static function _addgame_myvalidation()
  {
    // 入力チェック
    if ( ( ! Input::post('top')    && ! Input::post('top_name')    ) ||
         ( ! Input::post('bottom') && ! Input::post('bottom_name') ) )
    {
      Session::set_flash('error', 'リストからチームを選択するか直接入力してください。');
      return false;
    }

    // 自分の試合かどうか
    if ( ! Auth::has_access('admin.admin') )
    {
      $team_id = Model_Player::get_my_team_id();

      if ( Input::post('top') != $team_id && Input::post('bottom') != $team_id )
      {
        Session::set_flash('error', '自チームの試合のみ登録できます。');
        return false;
      }
    }

    return true;
  }

  private static function _filter_only_loginuser($players)
  {
    $myid = Model_Player::get_my_player_id(); 

    $res = array();
    foreach ( $players as $player )
    {
      if ( $player['player_id'] === $myid )
        $res[] = $player;
    }

    return $res;
  }

  private static function _filter_only_pitcher($players)
  {
    $myid = Model_Player::get_my_player_id(); 

    $res = array();
    foreach ( $players as $index => $player )
    {
      // 権限を持っていない場合は自分の成績のみupdate可能
      if ( ! Auth::has_access('admin.admin') and $player['player_id'] !== $myid )
      {
        continue;
      }

      if ( array_search(1, $player['position']) !== false )
      {
        $res[$index] = $player;
      }
    }

    return $res;
  }
}
