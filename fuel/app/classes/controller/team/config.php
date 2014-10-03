<?php

class Controller_Team_Config extends Controller_Team
{
	/**
	 * indexアクション。すべてのteam/configリクエストはここを通る
	 * 
	 * param string kind
	 *  - team_admin
	 *    - admin
	 *    - info
	 *    - delete
	 *  - belong_player
	 *    - leave
	 *  - both
	 *    - player
	 */
	public function action_index()
	{
		// 選手情報が無い
		if ( ! $this->_player)
		{
			Session::set_flash('error', '権限がありません');
			return Response::redirect($this->_team->href);
		}

		// 特定のconfigはチーム管理者専門
		$kind = $this->param('kind');
		if (in_array($kind, array('info', 'delete', 'admin')))
		{
			if ( ! $this->_team_admin)
			{
				Session::set_flash('error', '権限がありません');
				return Response::redirect($this->_team->href);
			}
		}

		// action
		$action = 'action_'.$kind;
		if ( method_exists($this, $action) )
		{
			return $this->$action();
		}

		Session::set_flash('error', '存在しないURLです');
		return Response::redirect($this->_team->href);
	}

	/**
	 * 選出追加
	 */
	public function action_player()
	{
		$view = View::forge('team/config/player.twig');
		$view->subtitle = '選出追加';

		// form
		$form = Model_Player::get_form(array('submit'   => '登録'));
		$form->field('username')->delete_rule('required');

		// player_idが遅れらて来ると、更新
		if ($player_id = $this->param('player_id'))
		{
			// 自分自身か、チーム管理者でなかったら、権限なし
			if ($this->_player->id !== $player_id and ! $this->_team_admin)
			{
				Session::get_error('権限がありません');
				return Response::redirect($this->_team->href);
			}

			// subtitle
			$view->subtitle = '選手情報更新';

			// player object
			$player = Model_Player::find($player_id);

			// formに初期値セット
			$form->field('name')->set_value($player->name);		
			$form->field('number')->set_value($player->number);		
			$form->field('username')->set_value($player->username);		

			// チーム管理者以外は、usernameはreadonly
			if ( ! $this->_team_admin)
			{
				$form->field('username')->set_attribute('readonly', 'readonly');
			}
		}

		// post request
		if (Input::post())
		{
			$val = $form->validation();

			if ($val->run())
			{
				$props = array(
					'team_id'  => $this->_team->id,
					'name'     => Input::post('name'),
					'number'   => Input::post('number'),
					'username' => Input::post('username'),
				);

				if (Model_Player::regist($props, $this->param('player_id')))
				{
					Session::set_flash('info', '選手情報を登録しました。');
					return Response::redirect(Uri::current());
				}
				else
				{
					Session::set_flash('error', '選手データ保存でシステムエラーが発生しました。');
				}
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
	 * チーム基本情報の設定
	 */
	public function action_info()
	{
		$view = View::forge('team/config/info.twig');

		// Fieldset
		$config = array('form_attribute' => array('class' => 'form'));
		$form = Fieldset::forge('team_config_info', $config);

		// add_model
		$form->add_model(Model_Team::forge());

		// set value
		$form->field('name')->set_value($this->_team->name);
		$form->field('url_path')->set_value($this->_team->url_path);

		$regulation_at_bats = array();
		for ($i = 0; $i <= 3; $i++)
		{
			for ($j = 0; $j <= 9; $j++)
			{
				$v = $i.'.'.$j;
				$regulation_at_bats[$v] = $v;
			}
		}
		$form->field('regulation_at_bats')->set_options($regulation_at_bats);
		$form->field('regulation_at_bats')->add_rule('in_array', $regulation_at_bats);
		$form->field('regulation_at_bats')->set_value($this->_team->regulation_at_bats);

		// hidden url_path
		$form->field('url_path')->set_type('hidden');

		// add submit
		$form->add('submit', '', array(
			'type'  => 'submit',
			'value' => '更新',
			'class' => 'btn btn-success',
		));

		// 更新処理
		if (Input::post())
		{
			$val = $form->validation();

			if ($val->run())
			{
				$this->_team->name = Input::post('name');
				$this->_team->regulation_at_bats = Input::post('regulation_at_bats');
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
	 * 管理者設定
	 */
	public function action_admin()
	{
		if ($player_id = Input::get('player_id') and $role = Input::get('role'))
		{
			if (Model_Player::update_role($this->_team->id, $player_id, $role))
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
	 * チーム脱退
	 */
	public function action_leave()
	{
		$view = View::forge('team/config/leave.twig');
		return Response::forge($view);
	}
}
