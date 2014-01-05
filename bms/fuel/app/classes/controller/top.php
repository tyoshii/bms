<?php

class Controller_Top extends Controller
{

	public function action_index()
	{
    $view = View::forge('top/index.twig');

    // login form
    $form = Fieldset::forge('login', array(
      'form_attributes' => array(
        'class' => 'navbar-form navbar-right',
        'role'  => 'form',
      ),
    ));

    $form->add('username', 'アカウント', array('maxlength' => 8, 'class' => 'form-control'))
      ->add_rule('required')
      ->add_rule('max_length', 8);

    $form->add('password', 'パスワード', array('type' => 'password', 'class' => 'form-control'))
      ->add_rule('required')
      ->add_rule('max_length', 8);

    $form->add('submit', '', array('type' => 'submit', 'value' => 'ログイン', 'class' => 'btn btn-success'));

    $form->repopulate();

    // ログイン認証
    $auth = Auth::instance();
    Auth::logout(); 

    $view->set_safe('login_form', $form->build(Uri::create('/')));

		return $view;
	}
}
