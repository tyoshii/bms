<?php

class Controller_Register extends Controller
{
  public function action_index()
  {
    // ログイン中だったらトップへ飛ばす
    if ( Auth::check() and ! Auth::has_access('admin.admin') )
      Response::redirect(Uri::create('/'));

    $view = View::forge('register.twig');

    $form = self::_get_register_form();

    $val = $form->validation();

    if ( Input::post() && $val->run() )
    {
      if ( Model_User::regist() )
      {
        // 成功した場合は、loginページへリダイレクト
        Session::set_flash('info', 'ユーザー登録に成功しました。ログインしてください。');
        Response::redirect(Uri::create('/login'));
      }
    }
    else
    {
      Session::set_flash('error', $val->show_errors());
    }

    $form->repopulate();
    $view->set_safe('form', $form->build(Uri::current()) );

    return Response::forge($view);
  }

  public function _get_register_form()
  {
    $form = Common_Form::forge('regist_user');

    $form->username()
         ->password()
         ->confirm()
         ->name()
         ->email()
         ->submit('登録'); 

    $form = $form->form;

    // 必須の表示
    $form->set_config('required_mark', '<span class="red">*</span>');

    return $form;
  }

}
