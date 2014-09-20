<?php

class Controller_Team_Config extends Controller_Team
{
	public function before()
	{
		parent::before();
	}

	/**
	 * 設定アクション
	 */
	public function action_index()
	{
		$kind = $this->param('kind');

		// 特定のconfigはチーム管理者専門
		if ( in_array($kind, array('info', 'player', 'delete')) )
		{
			if ( ! $this->_team_admin )
			{
				Session::get_flash('error', '権限がありません');
				return Response::forge('/team/'.$this->_team->url_path);
			}
		}

		// profile編集はチーム参加者本人とチーム管理者のみ
		if ( $kind === 'profile' or $kind === 'leave' )
		{
			if ( ! $this->_player and ! $this->_team_admin )
			{
				Session::get_flash('error', '権限がありません');
				return Response::forge('/team/'.$this->_team->url_path);
			}
		}

		// action
		$action = 'action_'.$kind;
		return $this->$action();
	}

	/**
	 * チーム基本情報の設定
	 */
	public function action_info()
	{
		$view = View::forge('team/config/info.twig');

		// Fieldset
		$config = array('form_attribute' => array('class' => 'form'));
		$form   = Fieldset::forge('team_config_info', $config);

		// add_model
		$form->add_model(Model_Team::forge());

		// set value
		$form->field('name')->set_value($this->_team->name);
		$form->field('url_path')->set_value($this->_team->url_path);

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
				// 今はチーム名だけ編集可能
				$this->_team->name = Input::post('name');
				$this->_team->save();

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

	/**
	 * 選手管理（今使ってない
   */
	public function action_player()
	{
		$view = View::forge('team/config/player.twig');

		$view->players = Model_Player::query()->where(array(
  		array('team_id', $this->_team->id),
  		array('status', '!=', -1),
  	))->order_by(DB::expr('CAST(number as SIGNED)'))->get();

		return Response::forge($view);
	}

  /**
   * 管理者設定
   */
  public function action_admin()
  {
		if ( $player_id = Input::get('player_id') and $role = Input::get('role') )
		{
			if ( Model_Player::update_role($this->_team->id, $player_id, $role) )
			{
				Session::set_flash('info', '権限を更新しました。');
			}
			else
			{
				Session::set_flash('error', '権限の更新に失敗しました');
			}

			return Response::redirect(Uri::current());
		}

    $view = View::forge('team/config/admin.twig');

		$view->players = Model_Player::query()->where(array(
			array('team_id', $this->_team->id),
			array('status', '!=', -1),
		))->order_by(DB::expr('CAST(number as SIGNED)'))->get();

    return Response::forge($view);
  }

	/**
	 * チーム削除
	 */
	public function action_delete()
	{
		$view = View::forge('team/config/delete.twig');
		return Response::forge($view);
	}

	/**
	 * プロフィール編集
	 */
	public function action_profile()
	{
		$view = View::forge('team/config/profile.twig');
		return Response::forge($view);
	}
	
	/**
	 * チーム脱退
	 */
	public function action_leave()
	{
		$view = View::forge('team/config/leave.twig');
		return Response::forge($view);
	}
}
