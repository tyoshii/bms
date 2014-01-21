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
    $view->active_info = 'active';
    $view->set_safe('form', $form->build(Uri::current()));

    return Response::forge($view);
  }

  public function post_info()
  {
    $form = self::_get_info_form();

    $val = $form->validation();

    if ( $val->run() )
    {
      $props = array(
        'email' => Input::post('email'),
      );
      Auth::update_user($props, Auth::get_screen_name());

      $info = Auth::get_profile_fields();
      $info['dispname'] = Input::post('dispname');
      Auth::update_user($info, Auth::get_screen_name());

      Session::set_flash('info', 'ユーザー情報を更新しました');
      Response::redirect(Uri::current());
    }
    else
    {
      Session::set_flash('error', $val->show_errors());
      $form->repopulate();
    }

    $view = View::forge('user.twig');
    $view->active_info = 'active';
    $view->set_safe('form', $form->build(Uri::current()));

    return Response::forge($view);
  }

  public function action_password()
  {
    $form = self::_get_password_form();

    $view = View::forge('user.twig');
    $view->active_password = 'active';
    $view->set_safe('form', $form->build(Uri::current()));

    return Response::forge($view);
  }

  public function post_password()
  {
    $form = self::_get_password_form();
    $val = $form->validation();

    if ( $val->run() )
    {
      $p1 = Input::post('password1');
      $p2 = Input::post('password2');

      if ( $p1 !== $p2 )
      {
        Session::set_flash('error', '確認用パスワードが違います');
        $form->repopulate();
      }
      else
      {
        $data = Auth::Instance()->get_user_array();
        auth::change_password(Input::post('original'), $p1, $data['screen_name']);
        Session::set_flash('info', 'パスワードを変更しました。再ログインしてください。');
        Session::set('redirect_to', Uri::current());

        Auth::logout();
        Response::redirect(Uri::create('/login'));
      }
    }
    else
    {
      Session::set_flash('error', $val->show_errors());
      $form->repopulate();
    }

    $view = View::forge('user.twig');
    $view->active_password = 'active';
    $view->set_safe('form', $form->build(Uri::current()));

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

    $form->add('original', '今のパスワード', array('type' => 'password', 'class' => 'form-control', 'placeholder' => 'Password'))
      ->add_rule('required')
      ->add_rule('min_length', 8)
      ->add_rule('max_length', 250);

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

    $info = Auth::get_profile_fields();

    $dispname = isset($info['dispname']) ? $info['dispname'] : Auth::get_screen_name();

    $form->add('username', 'ユーザーID', array(
      'value' => Auth::get_screen_name(),
      'maxlength' => 8,
      'class' => 'form-control',
    ))
      ->add_rule('required')
      ->add_rule('max_length', 8);

    $form->add('dispname', '表示名', array('value' => $dispname, 'maxlength' => 16, 'class' => 'form-control'))
      ->add_rule('required')
      ->add_rule('max_length', 8);

    $form->add('email', 'Eメール', array(
      'value' => Auth::get_email(), 
      'class' => 'form-control',
    ))
      ->add_rule('required')
      ->add_rule('valid_email');

    $form->add('submit', '', array('type' => 'submit', 'class' => 'btn btn-success', 'value' => '更新'));

    return $form;
  }
}
