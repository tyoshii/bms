<?php

class Controller_Register extends Controller
{
  public function action_index()
  {
    return Response::forge(View::forge('register.twig'));
  }

  public function post_index()
  {
    $view = View::forge('register.twig');

    $input_name			= Input::post('input_name');
    $input_mail			= Input::post('input_mail');
    $input_password = Input::post('input_password');
    $input_confirm  = Input::post('input_confirm_password');
    
    $view->input_name = $input_name;
    $view->input_mail = $input_mail;

    // post data define check
    if ( ! isset($input_name)     or
         ! isset($input_mail)     or
         ! isset($input_password) or
         ! isset($input_confirm) )
    {
      Session::set_flash('error', '入力必須項目に値がありません');
      return Response::forge( $view );
    }

    // username
    if ( strlen($input_name) > 50 )
    {
      Session::set_flash('error', 'アカウントは50文字以下です。');
      return Response::forge( $view );
    }

    // password confirm
    if ( $input_password !== $input_confirm )
    {
      Session::set_flash('error', 'パスワードと確認用パスワードが等しくありません。');
      return Response::forge( $view );
    }

    // password length
    if ( strlen($input_password) < 8 or strlen($input_password) > 250 )
    {
      Session::set_flash('error', 'パスワードは8文字以上,250文字以下で設定してください。');
      return Response::forge( $view );
    }
      
    try {
      $result = Auth::create_user(
        $input_name,
        $input_password,
        $input_mail,
        1
      );
          
      if ( $result === false )
        throw new Exception('Internal Error');

      // 成功した場合は、loginページへリダイレクト
      Session::set_flash('info', 'アカウントの作成に成功しました。ログインしてください。');
      Response::redirect(Uri::create('/login'));

    } catch ( Exception $e ) {
      Session::set_flash('error', 'アカウントの作成に失敗しました');
    }

    return Response::forge( $view );
  }
}
