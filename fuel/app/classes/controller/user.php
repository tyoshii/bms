<?php

class Controller_User extends Controller_Base
{
    public function before()
    {
        parent::before();

        if (!Auth::check()) {
            Session::set('redirect_to', Uri::current());
            Response::redirect(Uri::create('/login'));
        }
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

        if ($val->run()) {
            // user情報更新
            Common::update_user(array(
                'email' => Input::post('email'),
                'dispname' => Input::post('dispname'),
            ));

            // player情報更新
            if ($player = Model_Player::find_by_username(Auth::get_screen_name())) {
                $player->name = Input::post('dispname');
                $player->save();
            }

            Session::set_flash('info', 'ユーザー情報を更新しました');
            Response::redirect(Uri::current());
        } else {
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

        if ($val->run()) {
            $username = Auth::get('username');

            if (Auth::get_profile_fields('regist_by_openid') == 1) {
                $old = Auth::reset_password($username);
            } else {
                $old = Input::post('original');
            }

            // change password
            if (Auth::change_password($old, Input::post('password1'), $username)) {
                // update meta
                Auth::update_user(array('regist_by_openid' => 0));

                // message
                Session::set('redirect_to', Uri::current());
                Session::set_flash('info', 'パスワードを変更しました。再ログインしてください。');

                // logout and redirect to /login
                Auth::logout();

                return Response::redirect(Uri::create('/login'));
            } else {
                Session::set_flash('error', 'パスワードの登録に失敗しました');
            }
        } else {
            Session::set_flash('error', $val->show_errors());
        }

        $form->repopulate();

        $view = View::forge('user.twig');
        $view->set_safe('form', $form->build(Uri::current()));

        return Response::forge($view);
    }

    public function _get_team_form()
    {
        $form = Fieldset::forge('team', array(
            'form_attributes' => array('class' => 'form'),
        ));

        // デフォルト
        $team = '';
        $number = '';

        // アカウントと選手が既に紐付けられているかどうか
        $player = Model_Player::find_by_username(Auth::get_screen_name());

        if ($player) {
            $team_id = $player->team_id;
            $number = $player->number;

            // player_id を type=hiddenでセット
            $form->add('player_id', '', array(
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
        $teams = Model_Team::get_teams_key_value();

        $form->add('team_id', '所属チーム', array(
            'type' => 'select',
            'options' => $default + $teams,
            'value' => $team_id,
            'class' => 'select2',
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
            ),
        ));

        if (Auth::get_profile_fields('regist_by_openid') == 0) {
            $form->add('original', '現在のパスワード', array(
                'type' => 'password',
                'class' => 'form-control',
                'placeholder' => 'Password',
            ))
                ->add_rule('required')
                ->add_rule('min_length', 8)
                ->add_rule('max_length', 250);
        }

        $form->add('password1', '新しいパスワード', array('type' => 'password', 'class' => 'form-control', 'placeholder' => 'Password'))
            ->add_rule('required')
            ->add_rule('min_length', 8)
            ->add_rule('max_length', 250);

        $form->add('password2', '新しいパスワード（確認）', array('type' => 'password', 'class' => 'form-control', 'placeholder' => 'Password'))
            ->add_rule('required')
            ->add_rule('match_field', 'password1');

        $form->add('submit', '', array(
            'type' => 'submit',
            'class' => 'btn btn-info',
            'value' => '変更',
        ));

        return $form;
    }

    public function _get_info_form()
    {
        $form = Fieldset::forge('user', array(
            'form_attributes' => array(
                'class' => 'form',
            ),
        ));

        $form->add('username', '', array(
            'value' => Auth::get_screen_name(),
            'type' => 'hidden',
        ))
            ->add_rule('required')
            ->add_rule('match_value', array(Auth::get_screen_name()));

        $form->add('email', '', array(
            'value' => Auth::get_email(),
            'type' => 'hidden',
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

        $form->add('dispname', '表示名', array(
            'type' => 'text',
            'value' => Auth::get_profile_fields('fullname'),
            'class' => 'form-control',
        ))
            ->add_rule('required')
            ->add_rule('max_length', 128);

        $form->add('submit', '', array(
            'type' => 'submit',
            'class' => 'btn btn-info',
            'value' => '更新',
        ));

        return $form;
    }
}
