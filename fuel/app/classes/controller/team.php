<?php

class Controller_Team extends Controller_Base
{
	public $_team       = array();
	public $_player     = array();
	public $_team_admin = false;

	public function before()
	{
		parent::before();

		// team 情報
		if ( $url_path = $this->param('url_path') )
		{
			if ( ! $this->_team = Model_Team::find_by_url_path($url_path) )
			{
				Session::set_flash('error', '正しいチーム情報が取得できませんでした。');
				return Response::redirect('error/404');
			}
		}

		// チーム管理者権限があるかどうか
		if ( Model_Player::has_team_admin($this->_team->id) )
		{
			$this->_team_admin = true;
		}

		// ログイン中ユーザーの選手情報
		$this->_player = Model_Player::query()->where(array(
			array('team_id', $this->_team->id),
			array('username', Auth::get('username')),
		))->get_one();

		// set_global
		$this->set_global('team', $this->_team);
		$this->set_global('team_admin', $this->_team_admin);
		$this->set_global('player', $this->_player);
	}

	/**
	 * チームページトップ
   */
	public function action_index()
	{
		$view = View::forge('team/index.twig');

		// set view
		$view->games   = Model_Game::get_info_by_team_id($this->_team->id);
		$view->players = Model_Player::query()->where('team_id', $this->_team->id)->get();

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
}
