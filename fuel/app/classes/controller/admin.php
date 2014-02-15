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
          Input::post('group'),
          array( 'team' => Input::post('team') )
        );

        Session::set_flash('info', 'ユーザーを追加しました。');
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

  public function get_member()
  {
    $form = $this->_get_addmember_form();

    $this->view->set_safe('form', $form->build(Uri::current()));
    $this->view->members = Model_Member::find('all', array(
      'related' => array('teams'),
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
        $member = Model_Member::forge();
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

    $role_ops = array(1 =>'normal', 50 => 'team', 100=>'admin');
    $form->add('group', '',
        array('class' => 'form-control', 'placeholder' => '権限グループ',
              'type' => 'select', 'options'=>$role_ops, 'value' =>'true',
      ))
      ->add_rule('required');

    // option - チーム選択
    $default = array( '' => '' );
    $teams = Model_Team::getTeams();

    $form->add('team', '', array('options' => $default+$teams, 'type' => 'select', 'class' => 'form-control chosen-select', 'data-placeholder' => '担当チーム'))
      ->add_rule('in_array', array_keys($teams));

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
