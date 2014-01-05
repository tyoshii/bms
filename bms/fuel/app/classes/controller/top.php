<?php

class Controller_Top extends Controller
{

	public function action_index()
	{
    $view = View::forge('top/index.twig');

    // login form
    $form = Fieldset::forge();

    $form->add('username', 'アカウント', array('maxlength' => 8))
      ->add_rule('required')
      ->add_rule('max_length', 8);

    $form->add('password', 'パスワード', array('type' => 'password'))
      ->add_rule('required')
      ->add_rule('max_length', 8);

    $form->add('submit', '', array('type' => 'submit', 'value' => 'ログイン'));

    $form->repopulate();

    // ログイン認証
    $auth = Auth::instance();
    Auth::logout(); 

    $view->set_safe('login_form', $form->build(Uri::create('/')));

		return $view;
	}
}
