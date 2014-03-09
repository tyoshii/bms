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

  public function get_user()
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

  public function post_memberinfo($id)
  {
    $this->view->kind = 'memberinfo';
    
    $form = self::_get_memberinfo_form($id);
    $val = $form->validation();

    if ( $val->run() )
    {
      // memberテーブル更新
      $member = Model_Player::find(Input::post('id'));
      $member->team = Input::post('team');
      $member->name = Input::post('name');
      $member->number = Input::post('number');
      $member->save();

      // アカウントの紐付け
      // Todo

      Session::set_flash('info', '選手情報の更新に成功しました');
      Response::redirect(Uri::current());
    }
    else
    {
      Session::set_flash('error', $val->show_errors());
    }

    $form->repopulate();
    $this->view->set_safe('form', $form->build(Uri::current()));

    return Response::forge($this->view);
  }

  public function action_memberinfo($id)
  {
    $this->view->kind = 'memberinfo';

    $form = self::_get_memberinfo_form($id);
    $this->view->set_safe('form', $form->build(Uri::current()));

    return Response::forge($this->view);
  }

  public function get_member()
  {
    $form = $this->_get_addmember_form();

    $this->view->set_safe('form', $form->build(Uri::current()));
    $this->view->members = Model_Player::find('all', array(
      'related' => array('teams'),
      'order_by' => 'number',
    ));

    return Response::forge( $this->view );
  }

  public function post_member()
  {
    $form = $this->_get_addmember_form();

    $val = $form->validation();
    if ( $val->run())
    {
      try {
        $member = Model_Player::forge();
        $member->name   = Input::post('name');
        $member->team   = Input::post('team');
        $member->number = Input::post('number');
        $member->save();

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

  public function get_team()
  {
    $form = $this->_get_team_form();

    $this->view->set_safe('form', $form->build(Uri::current()));
    $this->view->teams = Model_Team::find('all');

    return Response::forge($this->view);
  }

  public function post_team()
  {
    $form = self::_get_team_form();

    if ( $form->validation()->run() )
    {
      $team = Model_Team::forge();
      $team->name = Input::post('name');
      $team->save();

      Response::redirect(Uri::current());
    }
    else
    {
      $form->repopurate();

      $this->view->set_safe('form', $form->build(Uri::current()));
      $this->view->set_safe('teams', Model_Team::find('all'));

      return Response::forge($this->view);
    }
  }

  public function get_league()
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
        $member = Model_League::forge();
        $member->name   = Input::post('name');
        $member->save();

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

  public static function _get_memberinfo_form($id)
  {
    $form = Fieldset::forge('memberinfo', array(
      'form_attributes' => array(
        'class' => 'form',
      ),
    ));

    // 登録情報
    $member = Model_Player::find($id);

    // id
    $form->add('id', '', array(
      'type' => 'hidden',
      'value' => $id,
    ))
      ->add_rule('required')
      ->add_rule('trim')
      ->add_rule('match_value', array($id))
      ->add_rule('valid_string', array('numeric'));

    // team
    Common::add_team_select($form, $member->team);

    // name
    $form->add('name', '選手名', array(
      'type' => 'text',
      'value' => $member->name,
      'class' => 'form-control',
    ))
      ->add_rule('required')
      ->add_rule('trim');

    // number
    $form->add('number', '背番号', array(
      'type' => 'number',
      'value' => $member->number,
      'class' => 'form-control',
      'mim' => 0,
    ))
      ->add_rule('required')
      ->add_rule('trim')
      ->add_rule('valid_string', array('numeric'));

    // 紐付けユーザー
    $users = array();
    foreach ( Model_User::query()->select('username')->get() as $user )
    {
      if ( $user->username !== 'admin' )
        $users[$user->username] = $user->username;
    }

    $form->add('account', 'アカウント', array(
      'type' => 'select',
      'options' => array(''=>'') + $users,
      'value'   => Model_User::getMyTeamId(),
      'class' => 'form-control chosen-select',
      'data-placeholder' => '紐付けアカウント',
    ))
      ->add_rule('in_array', array_keys(array(''=>'') + $users));

    // submit
    $form->add('submit', '', array(
      'type' => 'submit',
      'value' => '更新',
      'class' => 'btn btn-success'));

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
    $form = Fieldset::forge('team', array(
      'form_attributes' => array(
        'class' => 'form',
        'role'  => 'search',
      ),
    ));

    $form->add('name', '', array('class' => 'form-control', 'placeholder' => 'TeamName'))
      ->add_rule('required')
      ->add_rule('max_length', 64);

    $leagues = Model_League::find(':all');

    if ( $leagues )
    {
      $form->add('league', '', array('class' => 'form-control', 'options' => $leagues, 'type' => 'select'))
        ->add_rule('in_array', $leagues);
    }

    $form->add('submit', '', array('type' => 'submit', 'value' => 'Add Team', 'class' => 'btn btn-success'));

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

  static private function _get_addmember_form()
  {
    $form = Fieldset::forge('adduser', array(
      'form_attributes' => array(
        'class' => 'form',
        'role'  => 'search',
      ),
    ));

    $form->add('name', '', array('class' => 'form-control', 'placeholder' => 'Name'))
      ->add_rule('required')
      ->add_rule('trim');

    $form->add('number', '', array('class' => 'form-control', 'placeholder' => 'number'))
      ->add_rule('required')
      ->add_rule('trim')
      ->add_rule('valid_string', array('numeric'))
      ->add_rule('max_length', 8);

    // option - チーム選択
    $default = array( '' => '' );
    $teams = Model_Team::getTeams();

    $form->add('team', '', array('options' => $default+$teams, 'type' => 'select', 'class' => 'form-control chosen-select', 'data-placeholder' => 'Select Team'))
      ->add_rule('in_array', array_keys($teams));

    $form->add('submit', '', array('type' => 'submit', 'value' => '登録', 'class' => 'btn btn-success'));

    return $form;
  }
}
