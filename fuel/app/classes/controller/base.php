<?php

class Controller_Base extends Controller
{
    public $_global = array();
    protected $_login_form = '';

    public function before()
    {
        // global value
        View::set_global('fuel_env', Fuel::$env);
        View::set_global('usericon', Common::get_usericon_url());
        View::set_global('is_mobile', Agent::is_mobiledevice());

        // default view
        $path = implode('/', Request::main()->route->segments);
        $path .= '.twig';

        if (is_file(APPPATH.DS.'views'.DS.$path)) {
            $this->view = View::forge($path);
        }

        // login
        $this->_login_form = self::_get_login_form();

        if (Auth::check()) {
            return;
        }

        if (Input::post()) {
            Auth::logout();
            if ($this->_login_form->validation()->run()) {
                $auth = Auth::instance();
                if ($auth->login(Input::post('email'), Input::post('password'))) {
                    Session::set_flash('info', 'ログインに成功しました！');
                    return Response::redirect(Common::get_url_redirect_after_login());
                }
            }

            Session::set_flash('error', 'ログインに失敗しました');
            $this->_login_form->repopulate();
        }
    }

    public function after($res)
    {
        // set global method
        View::set_global('global', $this->_global);

        // trace log
        $status = $res ? $res->status : '999';
        Log::trace($status);

        return $res;
    }

    public function get_global($key)
    {
        return array_key_exists($key, $this->_global) ? $this->_global[$key] : null;
    }

    public function set_global($key, $val)
    {
        $this->_global[$key] = $val;
    }

    /**
     * game object set to global.
     *
     * @param string game_id
     *
     * @return bool
     */
    public function set_global_game_object($game_id = false)
    {
        $game_id = $game_id ?: $this->param('game_id');

        if (!$game_id) {
            return false;
        }

        if (!$this->game = Model_Game::find($game_id)) {
            Session::set_flash('error', '試合情報が取得できませんでした。');

            return Response::redirect('/');
        }

        // set global
        $this->set_global('game', $this->game);

        return true;
    }

    /**
     * login form Fieldset::forge().
     */
    public static function _get_login_form()
    {
        // login form
        $form = Fieldset::forge('login', array(
            'form_attributes' => array(
                'class' => 'form',
                'role' => 'login',
            ),
        ));

        $form->add('email', 'メールアドレス', array(
            'type' => 'email',
            'class' => 'form-control',
        ))
            ->add_rule('required');

        $form->add('password', 'パスワード', array('type' => 'password', 'class' => 'form-control'))
            ->add_rule('required');

        $form->add('login', '', array('type' => 'submit', 'value' => 'ログイン', 'class' => 'btn btn-success'));

        return $form;
    }
}
