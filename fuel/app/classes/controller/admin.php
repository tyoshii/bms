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
    $this->view->list = Model_User::find('all');

    return Response::forge( $this->view );
  }

  public function post_user()
  {
    $form = self::_get_adduser_form();

    if ( $form->validation()->run())
    {
      try {
        Auth::create_user(
          Input::post('username'),
          Input::post('password'),
          Input::post('mail')
        );

        Response::redirect(Uri::create('admin/user'));
      }
      catch ( Exception $e )
      {
        echo $e->getMessage();
      }
    }
    else
    {
      echo "signup failed";
    }

    $form->repopulate();
    
    $this->view->set_safe('form', $form->build(Uri::current()));
    $this->view->list = Model_User::find('all');

    return Response::forge($this->view);
  }

  public function get_team()
  {
    $form = $this->_get_team_form();

    $this->view->set_safe('form', $form->build(Uri::current()));
    $this->view->set_safe('teams', Model_Team::find('all'));

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

  static private function _get_league_form()
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
      ->add_rule('required');

    $form->add('username', '', array('class' => 'form-control', 'placeholder' => 'Account'))
      ->add_rule('required')
      ->add_rule('max_length', 8);

    $form->add('password', '', array('type' => 'password', 'class' => 'form-control', 'placeholder' => 'Password'))
      ->add_rule('required');

    $form->add('submit', '', array('type' => 'submit', 'value' => 'Sign Up', 'class' => 'btn btn-success'));

    return $form;
  }
}
