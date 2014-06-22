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

  public function action_forget_password()
  {
    $view = View::forge('forget_password.twig');

    $form = Common_Form::forge()
              ->email()
              ->submit('送信')
              ->get_object();

    $view->set_safe('form', $form->build(Uri::current()));

    return Response::forge($view);
  }

  public function post_forget_password()
  {
    // get form / validation object
    $form = Common_Form::forge()
              ->email()
              ->submit('送信')
              ->get_object();
    $val  = $form->validation();

    // valid
    if ( ! $val->run() )
    {
      Session::set_flash('error', $val->show_errors());
    }
    else
    {
      // 入力されたメールアドレスが存在するかどうか
      $user = Model_User::find_by_email(Input::post('email'));
      if ( ! $user )
      {
        Session::set_flash('error', '存在しないメールアドレスです');
      }
      else
      {
        $time  = time();
        $crypt = Crypt::encode($time.$user->username);

        Common_Email::reset_password($user->username, $user->email, $time, $crypt);
        Session::set_flash('info', 'パスワードリセットのメールを登録メールに送付しました。');

        Response::redirect('/');
      }
    }
  }

  public function get_reset_password()
  {
    $username = Input::get('u');
    $time     = Input::get('t');
    $crypt    = Input::get('c');

    // 時間切れ
    if ( time() - $time > 60 * 60 )
    {
      Session::set_flash('error', '有効期限切れのリンクです');
      Response::redirect('/');
    } 

    // cryptチェック
    if ( Crypt::decode($crypt) !== $time.$username )
    {
      Session::set_flash('error', '不正なアクセスです。');
      Response::redirect('/');
    } 

    $view = View::forge('reset_password.twig');
    $view->new_password = Auth::reset_password($username);

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
