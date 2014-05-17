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

  public function action_team()
  {
    $form = self::_get_team_form();
    
    $view = View::forge('user.twig');
    $view->set_safe('form', $form->build(Uri::current()));
    
    return Response::forge($view);
  }

  public function post_team()
  {
    $form = self::_get_team_form();
    $val = $form->validation();

    if ( $val->run() )
    {
      $id    = Input::post('member_id');
      $props = array(
        'team'     => Input::post('team'),
        'number'   => Input::post('number'),
        'name'     => Common::get_dispname(),
        'username' => Auth::get_screen_name(),
      );

      // idが送られてくれば更新
      if ( $player = Model_Player::find($id) )
      {
        // player_idの書き換えチェック
        if ( $player->username != Auth::get_screen_name() )
        {
          Session::set_flash('error', '不正な処理が行われました。');
          Response::redirect();
        }

        $player->set($props);
        $player->save();
        
        Session::set_flash('info', '所属チームの更新が成功しました。');
        Response::redirect(Uri::current());
      }
      else // idが無ければ新規登録
      {
        // かぶりチェック
        $already = Model_Player::query()
          ->where('team', $props['team'])
          ->where('number', $props['number'])
          ->get_one();

        if ( $already && $already->username )
        {
          Session::set_flash('error', 'その背番号はすでに使われています');
        }
        else
        {
          // user_id 取得
          // 新規選手登録
          $player = Model_Player::forge($props);
          $player->save();
          
          Session::set_flash('info', '新たに所属チームに登録されました。');
          Response::redirect(Uri::current());
        }
      }
    }
    else
    {
      Session::set_flash('error', $val->show_errors());
    }

    $form->repopulate();

    $view = View::forge('user.twig');
    $view->set_safe('form', $form->build(Uri::current()));

    return Response::forge($view);
  }

  public function action_info()
  {
    $form = self::_get_info_form();

    $view = View::forge('user.twig');
    $view->set_safe('form', $form->build(Uri::current()));

    return Response::forge($view);
  }

  public function post_info()
  {
    $form = self::_get_info_form();

    $val = $form->validation();

    if ( $val->run() )
    {
      // user情報更新
      Common::update_user(array(
        'email'    => Input::post('email'),
        'dispname' => Input::post('dispname'),
      ));

      // player情報更新
      if ( $player = Model_Player::find_by_username(Auth::get_screen_name()) )
      {
        $player->name = Input::post('dispname');
        $player->save();
      }

      Session::set_flash('info', 'ユーザー情報を更新しました');
      Response::redirect(Uri::current());
    }
    else
    {
      Session::set_flash('error', $val->show_errors());
      $form->repopulate();
    }

    $view = View::forge('user.twig');
    $view->set_safe('form', $form->build(Uri::current()));

    return Response::forge($view);
  }

  public function action_password()
  {
    $form = self::_get_password_form();

    $view = View::forge('user.twig');
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
    $view->set_safe('form', $form->build(Uri::current()));

    return Response::forge($view);
  }

  public function _get_team_form()
  {
    $form = Fieldset::forge('team', array(
      'form_attributes' => array(
        'class' => 'form',
      ),
    ));

    // デフォルト
    $team = '';
    $number = '';

    // アカウントと選手が既に紐付けられているかどうか
    $player = Model_Player::find_by_username(Auth::get_screen_name());

    if ( $player )
    {
      $team   = $player->team;
      $number = $player->number;

      // player_id を type=hiddenでセット
      $form->add('member_id', '', array(
        'type' => 'hidden',
        'value' => $player->id,
      ))
        ->add_rule('required')
        ->add_rule('trim')
        ->add_rule('valid_string', array('numeric'))
        ->add_rule('match_value', array($player->id));
    }

    // 所属チーム
    $default = array('' => '');
    $teams = Model_Team::getTeams();

    $form->add('team', '所属チーム', array(
      'type' => 'select',
      'options' => $default + $teams,
      'value' => $team,
      'class' => 'form-control chosen-select',
      'data-placeholder' => 'Select Team',
    ))
      ->add_rule('in_array', array_keys($teams));

    // 背番号
    $form->add('number', '背番号', array(
      'type' => 'number',
      'value' => $number,
      'class' => 'form-control',
      'min' => '0',
    ))
      ->add_rule('trim')
      ->add_rule('valid_string', array('numeric'))
      ->add_rule('required');

    $form->add('submit', '', array('type' => 'submit', 'class' => 'btn btn-warning', 'value' => '更新'));

    return $form;
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

    $form->add('username', '', array(
      'value' => Auth::get_screen_name(),
      'type' => 'hidden'
    ))
      ->add_rule('required')
      ->add_rule('match_value', array(Auth::get_screen_name()));

    $form->add('email', '', array(
      'value' => Auth::get_email(), 
      'type' => 'hidden'
    ))
      ->add_rule('required')
      ->add_rule('valid_email')
      ->add_rule('match_value', array(Auth::get_email()));

    $form->add('dummy-username', 'ユーザーID', array(
      'value' => Auth::get_screen_name(),
      'class' => 'form-control',
      'disabled' => 'disabled',
    ));

    $form->add('dummy-email', 'Eメール', array(
      'value' => Auth::get_email(), 
      'class' => 'form-control',
      'disabled' => 'disabled',
    ));

    $form->add('dispname', '表示名/選手名', array('value' => $dispname, 'maxlength' => 16, 'class' => 'form-control'))
      ->add_rule('required')
      ->add_rule('max_length', 8);

    $form->add('submit', '', array('type' => 'submit', 'class' => 'btn btn-warning', 'value' => '更新'));

    return $form;
  }
}
