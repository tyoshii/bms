<?php

class Controller_Base extends Controller
{

  protected $_login      = false;
  protected $_login_form = '';

  public function before()
  {
    $this->_login_form = self::_get_login_form();

    if ( Input::post() )
    {
      Auth::logout();
      if ($this->_login_form->validation()->run())
      {
        $auth = Auth::instance();
        if ( $auth->login(Input::post('username'), Input::post('password')) )
        {
          echo "login success";
          $this->_login = true;
        }
        else
        {
          echo "login failed";
          // Response::redirect('hoge/fuga');
        }
      }
    }
  }

  static private function _get_login_form ()
  {
    // login form
    $form = Fieldset::forge('login', array(
      'form_attributes' => array(
        'class' => 'navbar-form navbar-right',
        'role'  => 'search',
      ),
    ));

    $form->add('username', '', array('maxlength' => 8, 'class' => 'form-control', 'placeholder' => 'Account'))
      ->add_rule('required')
      ->add_rule('max_length', 8);

    $form->add('password', '', array('type' => 'password', 'class' => 'form-control', 'placeholder' => 'Password'))
      ->add_rule('required')
      ->add_rule('max_length', 8);

    $form->add('submit', '', array('type' => 'submit', 'value' => 'Sign In', 'class' => 'btn btn-success'));

    return $form;
  }
}