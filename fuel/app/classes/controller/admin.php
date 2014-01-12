<?php

class Controller_Admin extends Controller_Base
{
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
  }

  public function action_index()
  {
    $view = View::forge('admin/index.twig');

    $form = self::_get_team_form();

    $view->set_safe( 'form', $form->build(Uri::current()) );
    $view->list =  Model_Team::find('all');

    return Response::forge($view);
  }

  public function action_signup()
  {
    $view = View::forge('admin/signup.twig');
    $form = self::_get_signup_form();


    if ( Input::post() )
    {
      if ( $form->validation()->run())
      {
        try {
          Auth::create_user( Input::post('username'), Input::post('password'), Input::post('mail') );
          echo "success signup";
        }
        catch ( Exception $e )
        {
          echo "signup failed"; 
          echo $e->getMessage();
          $form->repopulate();
        }
      }
      else
      {
        echo "signup failed";
        $form->repopulate();
      }
    }
    
    $view->set_safe('form', $form->build(Uri::current()));

    return Response::forge( $view );
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

  static private function _get_signup_form()
  {
    $form = Fieldset::forge('signup', array(
      'form_attributes' => array(
        'class' => '',
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
