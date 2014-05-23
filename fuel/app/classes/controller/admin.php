<?php

class Controller_Admin extends Controller_Base
{
  public function before()
  {
    parent::before();

    if ( ! Auth::has_access('admin.admin') )
      Response::redirect(Uri::create('/'));

    $kind = Uri::segment(2);

    $this->view = View::forge('admin.twig');
    $this->view->kind = $kind;
    $this->view->active = array( $kind => 'active' );
  }

  public function action_index()
  {
    return Response::forge( View::forge('admin.twig') );
  }

  public function action_user($id = null)
  {
    $form = $this->_get_user_form($id);

    // view set
    $this->view->set_safe('form', $form->build(Uri::current()));

    return Response::forge( $this->view );
  }

  public function post_user($id = null)
  {
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

    $this->view->set_safe('form', $form->build(Uri::current()));
    $this->view->users = Model_User::find('all');

    return Response::forge($this->view);
  }

  public function post_playerinfo($id)
  {
    $this->view->kind = 'playerinfo';
    
    $form = self::_get_playerinfo_form($id);
    $val = $form->validation();

    if ( $val->run() )
    {
      // playerテーブル更新
      $player = Model_Player::find(Input::post('id'));
      $player->team     = Input::post('team');
      $player->name     = Input::post('name');
      $player->number   = Input::post('number');
      $player->username = Input::post('username');
      $player->save();

      Session::set_flash('info', '選手情報の更新に成功しました');
      Response::redirect(Uri::create('admin/player'));
    }
    else
    {
      Session::set_flash('error', $val->show_errors());
    }

    $form->repopulate();
    $this->view->set_safe('form', $form->build(Uri::current()));

    return Response::forge($this->view);
  }

  public function action_playerinfo($id)
  {
    $this->view->kind = 'playerinfo';

    $form = self::_get_playerinfo_form($id);
    $this->view->set_safe('form', $form->build(Uri::current()));

    return Response::forge($this->view);
  }

  public function action_player()
  {
    $form = $this->_get_regist_player_form();

    $this->view->set_safe('form', $form->build(Uri::current()));
    $this->view->players = Model_Player::find('all', array(
      'related' => array('teams'),
      'order_by' => 'id',
    ));

    return Response::forge( $this->view );
  }

  public function post_player()
  {
    $form = $this->_get_regist_player_form();

    $val = $form->validation();
    if ( $val->run())
    {
      try {
        $player = Model_Player::forge();
        $player->name     = Input::post('name');
        $player->team     = Input::post('team');
        $player->number   = Input::post('number');
        $player->username = '';
        $player->save();

        Session::set_flash('info', '選手を登録しました。');
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

    $this->view->set_safe('form', $form->build(Uri::current()));
    $this->view->users = Model_User::find('all');

    return Response::forge($this->view);

  }

  public function action_team()
  {
    $form = $this->_get_team_form();

    $this->view->set_safe('form', $form->build(Uri::current()));
    $this->view->teams = Model_Team::find('all');

    return Response::forge($this->view);
  }

  public function post_team()
  {
    // delete
    if ( Input::post('id') )
    {
      try {

        $team = Model_Team::find(Input::post('id'));
        $team->delete();

        Session::set_flash('info', 'チームを削除しました。');
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

      $this->view->set_safe('form', $form->build(Uri::current()));
      $this->view->set_safe('teams', Model_Team::find('all'));

      return Response::forge($this->view);
    }
  }

  public function action_league()
  {
    $form = $this->_get_addleague_form();

    $this->view->set_safe('form', $form->build(Uri::current()));
    $this->view->leagues = Model_League::find('all');

    return Response::forge($this->view);
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

    $this->view->set_safe('form', $form->build(Uri::current()));
    $this->view->leagues = Model_League::find('all');

    return Response::forge($this->view);
  }

  public static function _get_playerinfo_form($id)
  {
    $self = Model_Player::find($id);

    $form = self::_get_regist_player_form();

    // default value
    $form->field('name')->set_value($self->name);
    $form->field('number')->set_value($self->number);
    $form->field('team')->set_value($self->team);
    $form->field('username')->set_value($self->username);
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
    $form = Fieldset::forge('regist_player', array(
      'form_attributes' => array(
        'class' => 'form',
        'role'  => 'regist',
      ),
    ));

    $form->add('name', '選手名', array(
      'class' => 'form-control',
      'placeholder' => 'Name',
      'description' => '60文字以内',
    ))
      ->add_rule('required')
      ->add_rule('max_length', 60)
      ->add_rule('trim');

    $form->add('number', '背番号', array(
      'class' => 'form-control',
      'placeholder' => 'number',
      'description' => '数字のみ / 3桁まで',
      'min' => 0,
    ))
      ->add_rule('required')
      ->add_rule('trim')
      ->add_rule('valid_string', array('numeric'))
      ->add_rule('max_length', 3);

    // option - チーム選択
    $default = array( '' => '' );
    $teams = Model_Team::getTeams();

    $form->add('team', '所属チーム', array(
      'options' => $default+$teams,
      'type' => 'select',
      'class' => 'form-control chosen-select',
      'data-placeholder' => 'Select Team',
    ))
      ->add_rule('required')
      ->add_rule('in_array', array_keys($teams));
    
    // 紐付けユーザー
    $users = array(''=>'') + Model_User::get_noregist_player_user();

    $form->add('username', '紐づけられているユーザー', array(
      'type' => 'select',
      'options' => $users,
      'class' => 'form-control chosen-select',
      'data-placeholder' => '紐付けユーザー',
    ))
      ->add_rule('in_array', array_keys($users));

    $form->add('submit', '', array(
      'type' => 'submit',
      'value' => '登録',
      'class' => 'btn btn-success',
    ));

    return $form;
  }
}
