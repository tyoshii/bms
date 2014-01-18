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

  public function action_index($user)
  {
    $form = self::_get_user_form();

    $view = View::forge('user.twig');

    return Response::forge($view);
  }

  public function _get_user_form()
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
