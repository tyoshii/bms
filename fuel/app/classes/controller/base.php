<?php

class Controller_Base extends Controller
{
  protected $_login_form = '';

  public function before()
  {
    if ( Uri::segment(1) === 'login' )
    {
      $this->_login_form = self::_get_login_form(array('form_class' => 'form'));
    }
    else
    {
      $this->_login_form = self::_get_login_form();
    }

    if ( Auth::check() ) {
      return;
    }

    if ( Input::post() )
    {
      Auth::logout();
      if ($this->_login_form->validation()->run())
      {
        $auth = Auth::instance();
        if ( $auth->login(Input::post('username'), Input::post('password')) )
        {
          Session::set_flash('info', 'ログインに成功しました！こんにちわ');
          Response::redirect(Uri::current());
        }
        else
        {
          Session::set_flash('error', 'ログインに失敗しました');
          $this->_login_form->repopulate();
        }
      }
    }
  }

  public function after($res)
  {
    $res->body->env = Fuel::$env;
    return $res;
  }

  static public function _get_login_form ($cond = array())
  {
    $form_name  = isset($cond['form_name'])  ? $cond['form_name']  : 'login';
    $form_class = isset($cond['form_class']) ? $cond['form_class'] : 'navbar-form navbar-right';

    // login form
    $form = Fieldset::forge('login', array(
      'form_attributes' => array(
        'class' => 'form',
        'role'  => 'search',
      ),
    ));

    $form->add('username', '', array('class' => 'form-control', 'placeholder' => 'Account'))
      ->add_rule('required')
      ->add_rule('max_length', 40);

    $form->add('password', '', array('type' => 'password', 'class' => 'form-control', 'placeholder' => 'Password'))
      ->add_rule('required')
      ->add_rule('min_length', 8)
      ->add_rule('max_length', 250);

    $form->add('login', '', array('type' => 'submit', 'value' => 'Login', 'class' => 'btn btn-success'));

    return $form;
  }
}
