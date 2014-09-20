<?php

class Controller_Admin extends Controller_Base
{
	public function before()
	{
		parent::before();

		if ( ! Auth::has_access('moderator.moderator'))
			return Response::redirect(Uri::create('/'));
	}

	public function after($response)
	{
		$response = parent::after($response);

		$kind = Uri::segment(2);

		$response->body->active = array($kind => 'active');

		return $response;
	}

	public function action_index()
	{
		return Response::forge(View::forge('layout/admin.twig'));
	}

	public function action_user_detail($id)
	{
		$form = $this->_get_user_form($id);

		if (Input::post())
		{
			$val = $form->validation();
			if ($val->run())
			{
				if (Model_User::update_group(Input::post('username'), Input::post('group')))
				{
					Session::set_flash('info', 'ユーザー情報の更新に成功しました。');
					Response::redirect(Uri::create('admin/user'));
				}
			} else
			{
				Session::set_flash('error', $val->show_errors());
			}
		}

		$view = View::forge('admin/user_detail.twig');
		$view->set_safe('form', $form->build(Uri::current()));

		return Response::forge($view);
	}

	public function action_user()
	{
		$view = View::forge('admin/user.twig');

		// get user list
		if (Auth::has_access('admin.admin'))
			$view->users = Model_User::find('all');
		else
			$view->users = Model_User::get_users_only_my_team();

		// form
		$form = $this->_get_user_form();
		$view->set_safe('form', $form->build(Uri::current()));

		return Response::forge($view);
	}

	public function post_user()
	{
		if ( ! Auth::has_access('admin.admin'))
			Response::redirect(Uri::current());

		$form = $this->_get_user_form();

		$val = $form->validation();
		if ($val->run())
		{
			if (Input::post('submit') == '登録')
			{
				if (Model_User::regist())
				{
					Session::set_flash('info', 'ユーザーを追加しました。');
					Response::redirect(Uri::create('admin/user'));
				}
			} else if (Input::post('submit') == '更新')
			{
				if (Model_User::update_group(Input::post('username'), Input::post('group')))
				{
					Session::set_flash('info', 'ユーザー情報の更新に成功しました。');
					Response::redirect(Uri::create('admin/user'));
				}
			}
		} else // ! $val->run()
		{
			// ユーザーを無効/最有効にするボタンはvalidationが別
			if (Input::post('submit') === '無効')
			{
				if (Model_User::disable(Input::post('username')))
				{
					Session::set_flash('info', Input::post('username').'を無効にしました。');
					Response::redirect(Uri::create('admin/user'));
				}
			} else if (Input::post('submit') === '最有効')
			{
				if (Model_user::update_group(Input::post('username'), 1))
				{
					Session::set_flash('info', Input::post('username').'を有効にしました。');
					Response::redirect(Uri::create('admin/user'));
				}
			} else
			{
				// validation error
				if ($error = $val->show_errors())
					Session::set_flash('error', $error);
				else
					Session::set_flash('error', 'システムエラーが発生しました。');
			}
		}

		$form->repopulate();

		// view set
		$view = View::forge('admin/user.twig');

		$view->set_safe('form', $form->build(Uri::current()));
		$view->users = Model_User::find('all');

		return Response::forge($view);
	}

	public function post_playerinfo($id)
	{
		$form = self::_get_playerinfo_form($id);
		$val = $form->validation();

		if ($val->run())
		{
			$props = array(
					'name'     => Input::post('name'),
					'number'   => Input::post('number'),
					'team_id'  => Input::post('team_id'),
					'username' => Input::post('username') ? : '',
			);

			if (Model_Player::regist($props, Input::post('id')))
			{
				Session::set_flash('info', '選手情報の更新に成功しました');
				Response::redirect(Uri::create('admin/player'));
			}
		} else
		{
			Session::set_flash('error', $val->show_errors());
		}

		$form->repopulate();

		// view set
		$view = View::forge('admin/playerinfo.twig');
		$view->set_safe('form', $form->build(Uri::current()));

		return Response::forge($view);
	}

	public function action_playerinfo($id)
	{
		$view = View::forge('admin/playerinfo.twig');

		$form = self::_get_playerinfo_form($id);
		$view->set_safe('form', $form->build(Uri::current()));

		return Response::forge($view);
	}

	public function action_player()
	{
		$view = View::forge('admin/player.twig');

		// regist form
		$form = $this->_get_regist_player_form();
		$view->set_safe('form', $form->build(Uri::current()));

		// adminであればすべての情報を取得
		// moderatorであれば自分のチームのみ
		if (Auth::has_access('admin.admin'))
		{
			$view->players = Model_Player::get_players();
		} else if (Auth::has_access('moderator.moderator.'))
		{
			$team_id = Model_Player::get_my_team_id();
			$view->players = Model_Player::get_players($team_id);
		}

		return Response::forge($view);
	}

	public function post_player()
	{
		// 無効
		if (Input::post('id'))
		{
			if (Model_Player::disable(Input::post('id')))
			{
				Session::set_flash('info', '選手の無効化に成功しました');
			}

			Response::redirect(Uri::current());
		}

		$form = $this->_get_regist_player_form();

		$val = $form->validation();
		if ($val->run())
		{
			$props = array(
					'name'     => Input::post('name'),
					'number'   => Input::post('number'),
					'team_id'  => Input::post('team_id'),
					'username' => Input::post('username') ? : '',
			);

			if (Model_Player::regist($props))
			{
				Session::set_flash('info', '新しく選手を登録しました。');
				Response::redirect(Uri::current());
			}
		} else
		{
			Session::set_flash('error', $val->show_errors());
		}

		$form->repopulate();

		// view set
		$view = View::forge('admin/player.twig');

		$view->set_safe('form', $form->build(Uri::current()));
		$view->players = Model_Player::get_players();

		return Response::forge($view);
	}

	public function action_team()
	{
		$view = View::forge('admin/team.twig');
		$view->teams = Model_Team::get_teams();

		$form = $this->_get_team_form();
		$view->set_safe('form', $form->build(Uri::current()));

		return Response::forge($view);
	}

	public function post_team()
	{
		if ( ! Auth::has_access('admin.admin'))
			Response::redirect(Uri::current());

		// bann
		if (Input::post('id'))
		{
			try
			{

				$team = Model_Team::find(Input::post('id'));
				$team->status = -1;
				$team->save();

				Session::set_flash('info', 'チームステータスを無効にしました。');
				Response::redirect(Uri::current());

			} catch (Exception $e)
			{
				throw new Exception($e->getMessage());
			}
		}

		$form = self::_get_team_form();

		$val = $form->validation();
		if ($val->run())
		{
			$team = Model_Team::forge();
			$team->name = Input::post('name');
			$team->save();

			Response::redirect(Uri::current());
		} else
		{
			Session::set_flash('error', $val->show_errors());
			$form->repopulate();

			// view set
			$view = View::forge('admin/team.twig');
			$view->set_safe('form', $form->build(Uri::current()));
			$view->set_safe('teams', Model_Team::find('all'));

			return Response::forge($view);
		}
	}

	public function action_league()
	{
		$view = View::forge('admin/league.twig');
		$view->leagues = Model_League::find('all');

		$form = $this->_get_addleague_form();
		$view->set_safe('form', $form->build(Uri::current()));

		return Response::forge($view);
	}

	public function post_league()
	{
		$form = $this->_get_addleague_form();

		$val = $form->validation();
		if ($val->run())
		{
			try
			{
				$league = Model_League::forge();
				$league->name = Input::post('name');
				$league->save();

				Session::set_flash('info', '新規リーグを登録しました。');
				Response::redirect(Uri::current());
			} catch (Exception $e)
			{
				Session::set_flash('error', $e->getMessage());
			}
		} else
		{
			Session::set_flash('error', $val->show_errors());
		}

		$form->repopulate();

		// view set
		$view = View::forge('admin/league.twig');

		$view->set_safe('form', $form->build(Uri::current()));
		$view->leagues = Model_League::find('all');

		return Response::forge($view);
	}

	public static function _get_playerinfo_form($id)
	{
		$player = Model_Player::find($id);

		$form = self::_get_regist_player_form();

		// default value
		$form->field('name')->set_value($player->name);
		$form->field('number')->set_value($player->number);
		$form->field('team_id')->set_value($player->team_id);
		$form->field('submit')->set_value('更新');

		if (Auth::has_access('admin.admin'))
		{
			$form->field('username')->set_value($player->username);
		}

		// id
		$form->add('id', 'プレイヤーID', array(
				'type'  => 'hidden',
				'value' => $id,
		))
				->add_rule('required')
				->add_rule('trim')
				->add_rule('match_value', array($id))
				->add_rule('valid_string', array('numeric'));

		return $form;
	}

	static private function _get_addleague_form()
	{
		$form = Fieldset::forge('league', array(
				'form_attributes' => array(
						'class' => 'form',
						'role'  => 'search',
				),
		));

		$form->add('name', '', array('class' => 'form-control', 'placeholder' => 'League Name'))
				->add_rule('required')
				->add_rule('max_length', 64);

		$form->add('submit', '', array('type' => 'submit', 'value' => 'Add League', 'class' => 'btn btn-success'));

		return $form;

	}

	static private function _get_team_form()
	{
		$form = Fieldset::forge('regist_team', array(
				'form_attributes' => array(
						'class' => 'form',
						'role'  => 'regist',
				),
		));

		$form->add('name', 'チーム名', array(
				'class'       => 'form-control',
				'placeholder' => 'TeamName',
				'description' => '60文字以内',
		))
				->add_rule('required')
				->add_rule('max_length', 60);

		$form->add('submit', '', array(
				'type'  => 'submit',
				'value' => '登録',
				'class' => 'btn btn-success',
		));

		return $form;
	}

	private function _get_user_form($id = null)
	{
		$form = '';

		if ($id)
			$form = self::_get_user_update_form($id);
		else
			$form = self::_get_user_regist_form();

		// 利用者の観点からは選手名は必須ではない
		$form->field('name')->delete_rule('required', true);

		// 必須項目のHTML変更
		$form->set_config('required_mark', '<span class="red">*</span>');

		return $form;
	}

	private static function _get_user_regist_form()
	{
		$form = Common_Form::forge('regist_user', array(
				'form_attributes' => array(
						'class' => 'form'
				)
		));

		// 項目
		$form->username()
				->password()
				->confirm()
				->email()
				->name()
				->group()
				->submit('登録');

		return $form->form;
	}

	static private function _get_user_update_form($id)
	{
		$form = Common_Form::forge('update_user');
		$form->id($id);

		// user info
		$info = Model_User::find($id) ? : Model_User::forge();
		$name = Model_Player::get_name_by_username($info->username);

		// form
		$form->username($info->username)
				->email($info->email)
				->name($name)
				->group($info->group)
				->submit('更新');

		$form = $form->form;

		// username / email は変更不可
		$form->field('username')->set_attribute(array('readonly' => 'readonly'));
		$form->field('email')->set_attribute(array('readonly' => 'readonly'));
		$form->field('name')->set_attribute(array('readonly' => 'readonly'));

		return $form;
	}

	static private function _get_regist_player_form()
	{
		$form = Common_Form::forge('regist_player');

		$form->name()
				->number()
				->team_id()
				->submit('登録');

		$form = $form->form;

		// moderatorの場合、teamは自チーム固定
		if ( ! Auth::has_access('admin.admin'))
		{
			$team_id = Model_Player::get_my_team_id();
			$team_name = Model_Player::get_my_team_name();

			$team_field = $form->field('team_id');

			$team_field->set_options(array($team_id => $team_name), '', true);
			$team_field->set_value($team_id);
			$team_field->add_rule('match_value', $team_id, true);
		}

		// 紐付けユーザー(admin.adminのみ
		if (Auth::has_access('admin.admin'))
		{
			$users = array('' => '') + Model_User::get_username_list();

			$form->add_before('username', '紐づけるユーザー名', array(
					'type'    => 'select',
					'options' => $users,
					'class'   => 'select2',
			), array(), 'submit')
					->add_rule('in_array', array_keys($users));
		}

		// required
		$form->set_config('required_mark', '<span class="red">*</span>');

		return $form;
	}
}
