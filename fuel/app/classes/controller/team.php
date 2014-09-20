<?php

class Controller_Team extends Controller_Base
{
  public $_team = array();
  public $_player = array();
  public $_team_admin = false;

  public function before()
  {
    parent::before();

    // team 情報
    if ($url_path = $this->param('url_path'))
    {
      if ( ! $this->_team = Model_Team::find_by_url_path($url_path))
      {
        Session::set_flash('error', '正しいチーム情報が取得できませんでした。');
        return Response::redirect('error/404');
      }
    }

    if ($this->_team)
    {
      // チーム管理者権限があるかどうか
      if (Model_Player::has_team_admin($this->_team->id))
      {
        $this->_team_admin = true;
      }

      // チームページへのURL
      $this->_team->href = '/team/' . $this->_team->url_path;
    }

    // ログイン中ユーザーの選手情報
    if (Auth::check() and $this->_team)
    {
      $this->_player = Model_Player::query()->where(array(
          array('team_id', $this->_team->id),
          array('username', Auth::get('username')),
      ))->get_one();
    }

    // set_global
    $this->set_global('team', $this->_team);
    $this->set_global('team_admin', $this->_team_admin);
    $this->set_global('player', $this->_player);
  }

  /**
   * チームページトップ
   */
  public function action_index()
  {
    $view = View::forge('team/index.twig');

    // set view
    $view->games = Model_Game::get_info_by_team_id($this->_team->id);

    return Response::forge($view);
  }

  /**
   * チーム検索画面
   */
  public function action_search()
  {
    $view = View::forge('team/search.twig');

    $query = Model_Team::query()->order_by('created_at');

    if ($q = Input::get('query'))
    {
      $query->where('name', 'LIKE', '%' . $q . '%');
    }

    $view->teams = $query->get();

    return Response::forge($view);
  }

  /**
   * チーム、新規登録
   */
  public function action_regist()
  {
    $view = View::forge('team/regist.twig');

    // form
    $form = self::_regist_form();
    $form->repopulate();

    if (Input::post())
    {
      $val = $form->validation();

      if ($val->run())
      {
        if (Model_Team::regist(Input::post()))
        {
          Session::set_flash('info', '新しくチームを作成しました。');
          return Response::redirect(Uri::create('/team/' . Input::post('url_path')));
        }
      } else
      {
        Session::set_flash('error', $val->show_errors());
      }
    }

    $view->set_safe('form', $form->build(Uri::current()));

    return Response::forge($view);
  }

  /**
   * 新規チーム登録フォーム
   */
  private static function _regist_form()
  {
    $config = array('form_attribute' => array('class' => 'form'));
    $form = Fieldset::forge('team_regist', $config);

    $form->add_model(Model_Team::forge());

    // placeholder 追加
    $form->field('url_path')->set_attribute('placeholder', Uri::base(false) . 'team/XXXX');

    // submit
    $form->add('regist', '', array(
        'type'  => 'submit',
        'value' => '新規チーム登録',
        'class' => 'btn btn-success',
    ));

    return $form;
  }

  /**
   * 選手一覧/個人
   */
  public function action_player()
  {

    if ($player_id = $this->param('player_id'))
    {
      // 個人
      $view = View::forge('team/player/personal.twig');
      if ( ! $view->player = Model_Player::find($player_id))
      {
        Session::get_error('選手情報が取得できませんでした');
        return Response::redirect('team/' . $this->_team->url_path);
      }
    } else
    {
      // 選手一覧
      $view = View::forge('team/player/list.twig');
      $view->players = Model_Player::get_players($this->_team->id);
    }

    return Response::forge($view);
  }

  /**
   * 成績
   */
  public function action_stats()
  {
    $view = View::forge('team/stats.twig');

    $view->result = Model_Score_Team::getTeamWinLose($this->_team->id, array());
    $view->stats = array(
        'teams' => Model_Score_Team::getTeamScore($this->_team->id),
        'selfs' => Model_Score_Self::getSelfScores($this->_team->id),
    );

    return Response::forge($view);
  }

  /**
   * このチームに入る、っていうオファー
   */
  public function action_offer()
  {
    $view = View::forge('team/offer.twig');
    return Response::forge($view);
  }
}
