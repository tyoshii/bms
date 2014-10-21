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

		// login
		$this->_login_form = self::_get_login_form();

		if (Auth::check())
		{
			return;
		}

		if (Input::post())
		{
			Auth::logout();
			if ($this->_login_form->validation()->run())
			{
				$auth = Auth::instance();
				if ($auth->login(Input::post('username'), Input::post('password')))
				{
					Session::set_flash('info', 'ログインに成功しました！');

					$redirect_to = Session::get('redirect_to', '/');
					Session::delete('redirect_to');

					Response::redirect($redirect_to);
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
	 * login form Fieldset::forge()
	 */
	static public function _get_login_form()
	{
		// login form
		$form = Fieldset::forge('login', array(
			'form_attributes' => array(
				'class' => 'form',
				'role'  => 'login',
			),
		));

		$form->add('username', 'ユーザー名', array('class' => 'form-control'))
			->add_rule('required')
			->add_rule('max_length', 40);

		$form->add('password', 'パスワード', array('type' => 'password', 'class' => 'form-control'))
			->add_rule('required')
			->add_rule('min_length', 8)
			->add_rule('max_length', 250);

		$form->add('login', '', array('type' => 'submit', 'value' => 'ログイン', 'class' => 'btn btn-success'));

		return $form;
	}
}
