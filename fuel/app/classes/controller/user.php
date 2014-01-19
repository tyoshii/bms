<?php

class Controller_User extends Controller_Base
{
  public function before()
  {
    parent::before();

    if ( ! Auth::check() )
    {
      Session::set('redirect_to', Uri::current());
      Response::redirect(Uri::create('/login'));
    }
  }

  public function action_info()
  {
    $form = self::_get_info_form();

    $view = View::forge('user.twig');

    return Response::forge($view);
  }

  public function action_password()
  {
    $form = self::_get_info_form();

    $view = View::forge('user.twig');

    return Response::forge($view);
  }

  public function _get_password_form()
  {
    $form = Fieldset::forge('password', array(
      'form_attributes' => array(
        'class' => 'form',
        'role'  => 'search',
      ),
    ));

    $form->add('password1', '新しいパスワード', array('type' => 'password', 'class' => 'form-control', 'placeholder' => 'Password'))
      ->add_rule('required')
      ->add_rule('min_length', 8)
      ->add_rule('max_length', 250);
    
    $form->add('password2', '同じものを', array('type' => 'password', 'class' => 'form-control', 'placeholder' => 'Password'))
      ->add_rule('required')
      ->add_rule('min_length', 8)
      ->add_rule('max_length', 250);

    $form->add('submit', '', array('type' => 'submit', 'class' => 'btn btn-warning', 'value' => '変更'));

    return $form;
  }

  public function _get_info_form()
  {
    $form = Fieldset::forge('user', array(
      'form_attributes' => array(
        'class' => 'form',
        'role'  => 'search',
      ),
    ));

    $form->add('username', '', array('maxlength' => 8, 'class' => 'form-control', 'placeholder' => 'ユーザーID'))
      ->add_rule('required')
      ->add_rule('max_length', 8);

    $form->add('dispname', '', array('maxlength' => 16, 'class' => 'form-control', 'placeholder' => '表示名'))
      ->add_rule('required')
      ->add_rule('max_length', 8);

    $form->add('submit', '', array('type' => 'submit', 'class' => 'btn btn-success', 'value' => '更新'));

    return $form;
  }
}
