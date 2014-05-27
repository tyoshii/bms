<?php

class Controller_Admin extends Controller_Base
{
  public function before()
  {
    parent::before();

    if ( ! Auth::has_access('moderator.moderator') )
      Response::redirect(Uri::create('/'));
  }

  public function after($response)
  {
    $kind = Uri::segment(2);

    $response->body->active = array( $kind => 'active' );

    return $response;
  }

  public function action_index()
  {
    return Response::forge( View::forge('layout/admin.twig') );
  }

  public function action_user($id = null)
  {
    $view = View::forge('admin/user.twig');

    if ( Auth::has_access('admin.admin') )
    {
      $form = $this->_get_user_form($id);

      $view->set_safe('form', $form->build(Uri::current()));
    }

    return Response::forge($view);
  }

  public function post_user($id = null)
  {
    if ( ! Auth::has_access('admin.admin') )
      Response::redirect(Uri::current());

    $form = $this->_get_user_form($id);

    $val = $form->validation();
    if ( $val->run() )
    {
      if ( Input::post('submit') == '登録' )
      {
        if ( Model_User::regist() )
        {
          Session::set_flash('info', 'ユーザーを追加しました。');
          Response::redirect(Uri::create('admin/user'));
        }
      }
      else if ( Input::post('submit') == '更新' )
      {
        if ( Model_User::updates() )
        {
          Session::set_flash('info', 'ユーザー情報の更新に成功しました。');
          Response::redirect(Uri::create('admin/user'));
        }
      }
      else
      {
        if ( Model_User::disable() )
        {
          Session::set_flash('info', Input::post('username').'を無効にしました。');
          Response::redirect(Uri::create('admin/user'));
        }
      }
    }
    else // ! $val->run()
    {
      Session::set_flash('error', $val->show_errors());
    }

    $form->repopulate();

    // view set
    $view = View::forge('admin/user.twig');

    $view->set_safe('form', $form->build(Uri::current()));
    $view->users = Model_User::find('all');

    return Response::forge($view);
  }

  public function post_playerinfo($id)
  {
    $view = View::forge('admin/playerinfo.twig');
    
    $form = self::_get_playerinfo_form($id);
    $val = $form->validation();

    if ( $val->run() )
    {
      $props = array(
        'name'     => Input::post('name'),
        'number'   => Input::post('number'),
        'team'     => Input::post('team'),
        'username' => Input::post('username'),
      );

      if ( Model_Player::regist($props, Input::post('id')) )
      {
        Session::set_flash('info', '選手情報の更新に成功しました');
        Response::redirect(Uri::create('admin/player'));
      }
    }
    else
    {
      Session::set_flash('error', $val->show_errors());
    }

    $form->repopulate();

    // view set
    $view->set_safe('form', $form->build(Uri::current()));

    return Response::forge($view);
  }

  public function action_playerinfo($id)
  {
    $view = View::forge('admin/playerinfo.twig');

    $form = self::_get_playerinfo_form($id);
    $view->set_safe('form', $form->build(Uri::current()));

    return Response::forge($view);
  }

  public function action_player()
  {
    $view = View::forge('admin/player.twig');

    if ( Auth::has_access('admin.admin') )
    {
      $form = $this->_get_regist_player_form();

      $view->set_safe('form', $form->build(Uri::current()));
    }

    $view->players = Model_Player::get_players();

    return Response::forge( $view );
  }

  public function post_player()
  {
    // 無効
    if ( Input::post('id') )
    {
      if ( Model_Player::disable(Input::post('id')) )
      {
        Session::set_flash('info', '選手の無効化に成功しました');
      } 
 
      Response::redirect(Uri::current()); 
    }

    $form = $this->_get_regist_player_form();

    $val = $form->validation();
    if ( $val->run())
    {
      $props = array(
        'name'     => Input::post('name'),
        'number'   => Input::post('number'),
        'team'     => Input::post('team'),
        'username' => Input::post('username'),
      );

      if ( Model_Player::regist($props) )
      {
        Session::set_flash('info', '新しく選手を登録しました。');
        Response::redirect(Uri::current());
      }
    }
    else
    {
      Session::set_flash('error', $val->show_errors());
    }

    $form->repopulate();

    // view set
    $view = View::forge('admin/player.twig');

    $view->set_safe('form', $form->build(Uri::current()));
    $view->players = Model_Player::get_players();

    return Response::forge($view);
  }

  public function action_team()
  {
    $view = View::forge('admin/team.twig');
    $view->teams = Model_Team::get_teams();

    $form = $this->_get_team_form();
    $view->set_safe('form', $form->build(Uri::current()));

    return Response::forge($view);
  }

  public function post_team()
  {
    if ( ! Auth::has_access('admin.admin') )
      Response::redirect(Uri::current());

    // bann
    if ( Input::post('id') )
    {
      try {

        $team = Model_Team::find(Input::post('id'));
        $team->status = -1;
        $team->save();

        Session::set_flash('info', 'チームステータスを無効にしました。');
        Response::redirect(Uri::current());

      } catch ( Exception $e ) {
        throw new Exception($e->getMessage());
      }
    }

    $form = self::_get_team_form();

    $val  = $form->validation();
    if ( $val->run() )
    {
      $team = Model_Team::forge();
      $team->name = Input::post('name');
      $team->save();

      Response::redirect(Uri::current());
    }
    else
    {
      Session::set_flash('error', $val->show_errors());
      $form->repopulate();

      // view set
      $view = View::forge('admin/team.twig');
      $view->set_safe('form', $form->build(Uri::current()));
      $view->set_safe('teams', Model_Team::find('all'));

      return Response::forge($view);
    }
  }

  public function action_league()
  {
    $view = View::forge('admin/league.twig');
    $view->leagues = Model_League::find('all');

    $form = $this->_get_addleague_form();
    $view->set_safe('form', $form->build(Uri::current()));

    return Response::forge($view);
  }

  public function post_league()
  {
    $form = $this->_get_addleague_form();

    $val = $form->validation();
    if ( $val->run())
    {
      try {
        $league = Model_League::forge();
        $league->name   = Input::post('name');
        $league->save();

        Session::set_flash('info', '新規リーグを登録しました。');
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

    // view set
    $view = View::forge('admin/league.twig');

    $view->set_safe('form', $form->build(Uri::current()));
    $view->leagues = Model_League::find('all');

    return Response::forge($view);
  }

  public static function _get_playerinfo_form($id)
  {
    if ( ! Auth::has_access('admin.admin') )
      Response::redirect('/admin/player');

    $player = Model_Player::find($id);

    $form = self::_get_regist_player_form();

    // default value
    $form->field('name')->set_value($player->name);
    $form->field('number')->set_value($player->number);
    $form->field('team')->set_value($player->team);
    $form->field('username')->set_value($player->username);
    $form->field('submit')->set_value('更新');

    // id
    $form->add('id', 'プレイヤーID', array(
      'type' => 'hidden',
      'value' => $id,
    ))
      ->add_rule('required')
      ->add_rule('trim')
      ->add_rule('match_value', array($id))
      ->add_rule('valid_string', array('numeric'));

    return $form;
  }

  static private function _get_addleague_form()
  {
    $form = Fieldset::forge('league', array(
      'form_attributes' => array(
        'class' => 'form',
        'role'  => 'search',
      ),
    ));

    $form->add('name', '', array('class' => 'form-control', 'placeholder' => 'League Name'))
      ->add_rule('required')
      ->add_rule('max_length', 64);

    $form->add('submit', '', array('type' => 'submit', 'value' => 'Add League', 'class' => 'btn btn-success'));

    return $form;

  }

  static private function _get_team_form()
  {
    $form = Fieldset::forge('regist_team', array(
      'form_attributes' => array(
        'class' => 'form',
        'role'  => 'regist',
      ),
    ));

    $form->add('name', 'チーム名', array(
      'class' => 'form-control',
      'placeholder' => 'TeamName',
      'description' => '60文字以内',
    ))
      ->add_rule('required')
      ->add_rule('max_length', 60);

    $form->add('submit', '', array(
      'type' => 'submit',
      'value' => '登録',
      'class' => 'btn btn-success',
    ));

    return $form;
  }

  private function _get_user_form($id = null)
  {
    if ( $id )
    {
      $this->view->kind = 'updateuser';
      $form = self::_get_user_update_form($id);
    }
    else
    {
      $this->view->users = Model_User::find('all');
      $form = self::_get_user_regist_form();
    }

    // 必須項目のHTML変更
    $form->set_config('required_mark', '<span class="red">*</span>');

    return $form;
  }

  private static function _get_user_regist_form()
  {
    $form = Common_Form::forge('regist_user', array(
      'form_attributes' => array(
        'class' => 'form'
      )
    ));

    // 項目
    $form->username()
         ->password()
         ->confirm()
         ->email()
         ->name()
         ->group()
         ->submit('登録');

    return $form->form; 
  }

  static private function _get_user_update_form($id = '')
  {
    $form = Common_Form::forge('regist_user');

    // user info
    $info = Model_User::find($id) ?: Model_User::forge();

    // 項目
    $name = Model_Player::get_name_by_username($info->username);
    $form->username($info->username)
         ->email($info->email)
         ->name($name)
         ->group($info->group)
         ->submit('更新');

    $form = $form->form;

    // username / email は変更不可
    $form->field('username')->set_attribute(array('readonly' => 'readonly'));
    $form->field('email')->set_attribute(array('readonly' => 'readonly'));
    $form->field('name')->set_attribute(array('readonly' => 'readonly'));

    return $form;
  }

  static private function _get_regist_player_form()
  {
    $form = Common_Form::forge('regist_player');

    $form->name()
         ->number()
         ->team()
         ->submit('登録');

    $form = $form->form;

    // 紐付けユーザー
    $users = array(''=>'') + Model_User::get_username_list();

    $form->add_before('username', '紐づけるユーザー名', array(
      'type' => 'select',
      'options' => $users,
      'class' => 'form-control chosen-select',
    ), array(), 'submit')
      ->add_rule('in_array', array_keys($users));

    // required
    $form->set_config('required_mark', '<span class="red">*</span>');

    return $form;
  }
}
