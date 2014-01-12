<?php

class Controller_Top extends Controller
{

	public function action_index()
	{
    $view = View::forge('top/index.twig');


    $form = self::_get_login_form();

    // ログイン認証
    $auth = Auth::instance();

    if ( Input::post() )
    {
      Auth::logout();
      if ($form->validation()->run())
      {
        if ( $auth->login(Input::post('username'), Input::post('password')) )
        {
          echo "login success";
        }
        else
        {
          echo "login failed";
          // Response::redirect('hoge/fuga');
        }
      }
    }
    else
    {
      Auth::logout(); 
      $form->repopulate();
      $view->set_safe('login', $form->build(Uri::create('/')));
    }


		return $view;
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
