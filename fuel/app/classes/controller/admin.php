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

  public function action_user()
  {
    $form = self::_get_adduser_form();

    $this->view->set_safe('form', $form->build(Uri::current()));
    $this->view->users = Model_User::find('all');

    return Response::forge( $this->view );
  }

  public function post_user()
  {
    $form = self::_get_adduser_form();

    $val = $form->validation();
    if ( $val->run())
    {
      try {
        Auth::create_user(
          Input::post('username'),
          Input::post('password'),
          Input::post('mail'),
          Input::post('group')
        );

        Session::set_flash('info', 'ユーザーを追加しました。');
        Response::redirect(Uri::create('admin/user'));
      }
      catch ( Exception $e )
      {
        Session::set_flash('error', $e->getMessage());
      }
    }
    elseif(Input::post("username")) {
      $uname = Input::post('username');
      $current_user = Auth::get("username");
      try {
        if ($uname === $current_user) {
            Session::set_flash('error', '自分自身のアカウントは無効にできません');
            Response::redirect(Uri::create('admin/user'));
        }
        Auth::update_user(
            array(
                'group' => -1,    // ユーザーを無効化
            ),
            $uname
        );
        Session::set_flash('info', $uname .' を無効にしました。');
        Response::redirect(Uri::create('admin/user'));
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

  static private function _get_adduser_form()
  {
    $form = Fieldset::forge('adduser', array(
      'form_attributes' => array(
        'class' => 'form',
        'role'  => 'search',
      ),
    ));

    $form->add('mail', '', array('class' => 'form-control', 'placeholder' => 'Mail'))
      ->add_rule('required')
      ->add_rule('trim')
      ->add_rule('valid_email');

    $form->add('username', '', array('class' => 'form-control', 'placeholder' => 'Account'))
      ->add_rule('required')
      ->add_rule('max_length', 8);

    $form->add('password', '', array('type' => 'password', 'class' => 'form-control', 'placeholder' => 'Password'))
      ->add_rule('required');

    $groups = Config::get("simpleauth.groups");
    $roles = array();
    foreach ($groups as $k => $v) {
        if ($k > 0) {
            $roles[$k] = $v["name"];
        }
    }
    $role_ops = $roles;
    $form->add('group', '',
        array('class' => 'form-control', 'placeholder' => '権限グループ',
              'type' => 'select', 'options'=>$role_ops, 'value' =>'true',
      ))
      ->add_rule('required');

    $form->add('submit', '', array('type' => 'submit', 'value' => 'Sign Up', 'class' => 'btn btn-success'));

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
