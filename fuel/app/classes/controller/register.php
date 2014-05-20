<?php

class Controller_Register extends Controller
{
  public function action_index()
  {
    $view = View::forge('register.twig');

    $form = self::_get_register_form();

    $val = $form->validation();

    if ( Input::post() && $val->run() )
    {
      try {
        // already check
        if ( Model_User::find_by_username(Input::post('username')) ) 
          throw new Exception('そのユーザー名は既に存在します。');
             
        if ( Model_User::find_by_email(Input::post('email')) )
          throw new Exception('そのメールアドレスは既に登録済みです。');
 
        // user create
        $result = Auth::create_user(
          Input::post('username'),
          Input::post('password'),
          Input::post('email'),
          1,
          array('dispname' => Input::post('name'))
        );
            
        if ( $result === false )
          throw new Exception('Internal Error');
  
        // 成功した場合は、loginページへリダイレクト
        Session::set_flash('info', 'ユーザー登録に成功しました。ログインしてください。');
        Response::redirect(Uri::create('/login'));
  
      } catch ( SimpleUserUpdateException $e ) {
        Session::set_flash('error', 'アカウントの作成に失敗しました：'.$e->getMessage());
      } catch ( Exception $e ) {
        Session::set_flash('error', 'アカウントの作成に失敗しました：'.$e->getMessage());
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
    $form = Fieldset::forge('register', array(
      'form_attributes' => array(
        'class' => 'form form-horizontal',
        'role'  => 'form',
      ),
    ));

    // 必須の表示
    $form->set_config('required_mark', '<span class="red">*</span>');

    $form->add('username', 'ユーザー名', array(
      'type'  => 'text',
      'class' => 'form-control',
      'description' => '半角英数 / 50字以内',
    ), array())
      ->add_rule('required')
      ->add_rule('trim')
      ->add_rule('valid_string', array('alpha', 'numeric'))
      ->add_rule('max_length', 50);

    $form->add('password', 'パスワード', array(
      'type' => 'password',
      'class' => 'form-control',
      'description' => '半角英数と「. , ! ? : ;」が使用可能 / 8字以上 / 250字以内',
    ), array())
      ->add_rule('required')
      ->add_rule('min_length', 8)
      ->add_rule('max_length', 250)
      ->add_rule('valid_string', array('alpha', 'numeric', 'punctuation'))
      ->add_rule('match_field', 'confirm');

    $form->add('confirm', '確認用パスワード', array(
      'type' => 'password',
      'class' => 'form-control',
    ), array())
      ->add_rule('required');

    $form->add('name', '名前', array(
      'type' => 'text',
      'class' => 'form form-control',
      'description' => '60字以内',
    ), array())
      ->add_rule('required')
      ->add_rule('max_length', 64);

    $form->add('email', 'メールアドレス', array(
      'type' => 'email',
      'class' => 'form-control',
    ), array())
      ->add_rule('required')
      ->add_rule('valid_email');

    $form->add('submit', '', array(
      'type' => 'submit',
      'class' => 'btn btn-success',
      'value' => '登録',
    ), array());

    return $form;
  }

}
