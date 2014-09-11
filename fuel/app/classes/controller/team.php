<?php

class Controller_Team extends Controller_Base
{
	public static $_team;
	public static $_player;
	public static $_team_admin;

	public function before()
	{
		parent::before();

		// team 情報
		if ( $url_path = $this->param('url_path') )
		{
			if ( ! self::$_team = Model_Team::find_by_url_path($url_path) )
			{
				Session::set_flash('error', '正しいチーム情報が取得できませんでした。');
				return Response::redirect('error/404');
			}

			View::set_global('team', self::$_team);
		}

		// チーム管理者権限があるかどうか
		if ( Model_Player::has_team_admin(self::$_team->id) )
		{
			self::$_team_admin = true;
			View::set_global('team_admin', true);
		}

		// ログイン中ユーザーの選手情報
		self::$_player = Model_Player::query()->where(array(
			array('team_id', self::$_team->id),
			array('username', Auth::get('username')),
		))->get_one();
		View::set_global('player', self::$_player);
	}

	/**
	 * チームページトップ
   */
	public function action_index()
	{
		$view = View::forge('team/index.twig');

		// set view
		$view->games   = Model_Game::get_info_by_team_id(self::$_team->id);
		$view->players = Model_Player::query()->where('team_id', self::$_team->id)->get();

		return Response::forge($view);
	}

	/**
	 * チーム検索画面
   */
	public function action_search()
	{
		$view = View::forge('team/search.twig');

		$query = Model_Team::query()->order_by('created_at');

		if ( $q = Input::get('query') )
		{
			$query->where('name', 'LIKE', '%'.$q.'%');
		}

		$view->teams = $query->get();

		return Response::forge($view);
	}

	/**
	 * チーム、新規登録
   */
	public function action_regist()
	{
		$view = View::forge('team/regist.twig');

		// form
		$form = self::_regist_form();
		$form->repopulate();

		if ( Input::post() )
		{
			$val = $form->validation();

			if ( $val->run() )
			{
				if ( Model_Team::regist(Input::post()) )
				{
					Session::set_flash('info', '新しくチームを作成しました。');
					return Response::redirect(Uri::create('/team/'.Input::post('url_path')));
				}
			}
			else
			{
				Session::set_flash('error', $val->show_errors());
			}
		}

		$view->set_safe('form', $form->build(Uri::current()));

		return Response::forge($view);
	}

	/**
   * 新規チーム登録フォーム
   */
	private static function _regist_form()
	{
		$config = array('form_attribute' => array('class' => 'form'));
		$form   = Fieldset::forge('team_regist', $config);

		$form->add_model(Model_Team::forge());

		// placeholder 追加
		$form->field('url_path')->set_attribute('placeholder', Uri::base(false).'team/XXXX');

		// submit
		$form->add('regist', '', array(
			'type'  => 'submit',
			'value' => '新規チーム登録',
			'class' => 'btn btn-success',
		));

		return $form;
	}

	/**
	 * 設定アクション
	 */
	public function action_config()
	{
		$kind = $this->param('kind');

		// 特定のconfigはチーム管理者専門
		if ( in_array($kind, array('info', 'player', 'delete')) )
		{
			if ( ! self::$_team_admin )
			{
				Session::get_flash('error', '権限がありません');
				return Response::forge('/team/'.self::$_team->url_path);
			}
		}

		// profile編集はチーム参加者本人とチーム管理者のみ
		if ( $kind === 'profile' )
		{
			if ( ! self::$_player and ! self::$_team_admin )
			{
				Session::get_flash('error', '権限がありません');
				return Response::forge('/team/'.self::$_team->url_path);
			}
		}

		// action
		$action = 'action_config_'.$kind;
		return $this->$action();
	}

	/**
	 * チーム基本情報の設定
	 */
	public function action_config_info()
	{
		$view = View::forge('team/config/info.twig');

		// Fieldset
		$config = array('form_attribute' => array('class' => 'form'));
		$form   = Fieldset::forge('team_config_info', $config);

		// add_model
		$form->add_model(Model_Team::forge());

		// set value
		$form->field('name')->set_value(self::$_team->name);
		$form->field('url_path')->set_value(self::$_team->url_path);

		// hidden url_path
		$form->field('url_path')->set_type('hidden');

		// add submit
		$form->add('submit', '', array(
			'type'  => 'submit',
			'value' => '更新',
			'class' => 'btn btn-success',
		));

		// 更新処理
		if ( Input::post() )
		{
			$val = $form->validation();

			if ( $val->run() )
			{
				self::$_team->name = Input::post('name');
				self::$_team->save();

				Session::set_flash('info', 'チーム情報を更新しました');
				return Response::redirect(Uri::current());
			}
			else
			{
				Session::set_flash('error', $val->show_errors());
			}

			$form->repopulate();
		}

		// set view
		$view->set_safe('form', $form->build());

		return Response::forge($view);
	}
}
