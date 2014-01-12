<?php

class Controller_Admin extends Controller_Base
{
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
