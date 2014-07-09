<?php

class Controller_Base extends Controller
{
  protected $_login_form = '';

  public function before()
  {
    $this->_login_form = self::_get_login_form();

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

          $redirect_to = Session::get('redirect_to', '/');
          Session::delete('redirect_to');

          Response::redirect($redirect_to);
        }
      }
          
      Session::set_flash('error', 'ログインに失敗しました');
      $this->_login_form->repopulate();
    }
  }

  public function after($res)
  {
    $res = parent::after($res);

    $res->body->usericon = Common::get_usericon_url();

    return $res;
  }

  static public function _get_login_form ()
  {
    // login form
    $form = Fieldset::forge('login', array(
      'form_attributes' => array(
        'class' => 'form',
        'role'  => 'login',
      ),
    ));

    $form->add('username', 'ユーザー名', array('class' => 'form-control', 'placeholder' => 'Username'))
      ->add_rule('required')
      ->add_rule('max_length', 40);

    $form->add('password', 'パスワード', array('type' => 'password', 'class' => 'form-control', 'placeholder' => 'Password'))
      ->add_rule('required')
      ->add_rule('min_length', 8)
      ->add_rule('max_length', 250);

    $form->add('login', '', array('type' => 'submit', 'value' => 'Login', 'class' => 'btn btn-success'));

    return $form;
  }
}
