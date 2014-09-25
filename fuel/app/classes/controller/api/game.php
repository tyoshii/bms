<?php

class Controller_Api_Game extends Controller_Rest
{
	public function before()
	{
		parent::before();
	}

	public function post_updateStatus()
	{
		$ids = self::_get_ids();

		$ret = Model_Game::update_status($ids['game_id'], Input::post('status'));

		if ( ! $ret)
			throw new Exception('ステータスのアップデートに失敗しました');

		Session::set_flash('info', '試合ステータスを更新しました。');

		return $this->_success();
	}

	public function post_updateScore()
	{
		// 権限チェック
		if ( ! Auth::has_access('game.editall'))
			return Response::forge('スコアを編集する権限がありません', 403);

		$form = Fieldset::forge('score');
		$form->add_model(Model_Games_Runningscore::forge());

		$val = $form->validation();

		// TODO: スマホ版の実装でstatsのキーでポストしている
		// PCもいつかそっちによせる
		$stats = Input::post('stats') ? : Input::post();

		if ( ! $val->run($stats, true))
			return Response::forge($val->show_errors(), 400);

		Model_Games_Runningscore::regist(Input::post('game_id'), $stats);

		return $this->_success();
	}

	// 出場選手
	public function post_updatePlayer()
	{
		// 権限チェック
		if ( ! Auth::has_access('game.editall'))
			return Response::forge('出場選手を編集する権限がありません', 403);

		$ids = self::_get_ids();

		// stats_metaへの登録
		$players = Input::post('stats');
		Model_Stats_Player::regist_player($ids, $players);

		// status update
		Model_Game::update_status_minimum($ids['game_id'], 1);

		return $this->_success();
	}

	public function post_updatePitcher()
	{
		$ids = self::_get_ids();

		// stats_pitchingsへのinsert
		$pitcher = Input::post('stats');
		$status = Input::post('complete') === 'true' ? 1 : 0;

		// 複数登録できるのはチーム管理者だけ
		self::_validate_stats_count($pitcher, $ids['team_id']);

		// 複数登録の場合は、ステータスの更新はしない
		if (count($pitcher) > 1)
		{
			$status = null;
		}

		// regist
		Model_Stats_Pitching::regist($ids, $pitcher, $status);

		return $this->_success();
	}

	public function post_updateBatter()
	{
		$ids = self::_get_ids();

		// satasへの登録
		$batter = Input::post('stats');
		$status = Input::post('complete') === 'true' ? 1 : 0;

		// 複数登録できるのはチーム管理者だけ
		self::_validate_stats_count($batter, $ids['team_id']);

		// 複数登録の場合は、ステータスの更新はしない
		if (count($batter) > 1)
		{
			$status = null;
		}

		// regist
		Model_Stats_Hitting::regist($ids, $batter, $status);

		$this->_success();
	}

	public function post_updateOther()
	{
		// 権限チェック
		if ( ! Auth::has_access('game.editall'))
			return Response::forge('編集する権限がありません', 403);

		$ids = self::_get_ids();

		// stats
		$stats = Input::post('stats');

		// update games(stadium/memo)
		// TODO: stadiumとmemoのvalidation
		$game = Model_Game::find($ids['game_id']);
		$game->stadium = $stats['stadium'];
		$game->memo = $stats['memo'];
		$game->save();

		// update award(mvp)
		$stats = array(
			'mvp_player_id'        => $stats['mvp'],
			'second_mvp_player_id' => $stats['second_mvp'],
		);
		Model_Stats_Award::regist($ids['game_id'], $ids['team_id'], $stats);

		return $this->_success();
	}

	/**
	 * get post game_id/team_id and each validation
	 */
	private static function _get_ids()
	{
		$val = Validation::forge();
		$val->add('game_id', 'game_id')->add_rule('required');
		$val->add('team_id', 'team_id')->add_rule('required');

		if ( ! $val->run())
		{
			throw new Exception($val->show_errors());
		}

		$ids = $val->validated();

		// check acl if no admin
		if ( ! Auth::has_access('admin.admin') and Auth::has_access('moderator.moderator'))
		{
			// has Moderators ?
			if ( ! Auth::member('50'))
			{
				throw new Exception('権限がありません');
			}

			// Moderatorsだとして、自分のチームの試合ですか？
			if ($ids['team_id'] !== Model_Player::get_my_team_id())
			{
				throw new Exception('権限がありません');
			}
		}

		// check game status
		$action = Request::main()->action;
		if ($action !== 'updateStatus' and $action !== 'updateOther')
		{
			$game = Model_Game::find($ids['game_id']);
			if ( ! $game or $game->game_status === '2')
			{
				throw new Exception('既に成績入力を完了している試合です');
			}
		}

		return $ids;
	}


	/**
	 * statsに複数の成績が送られてきたとき、チーム管理者の権限があるかどうかをチェック
	 *
	 * @param array  stats
	 * @param string team_id
	 *
	 * @return true / Exception
	 */
	private static function _validate_stats_count($stats, $team_id)
	{
		if (count($stats) > 1)
		{
			if ( ! Model_Player::has_team_admin($team_id))
			{
				throw new Exception('複数の成績登録はチーム管理者のみが許可されています');
			}
		}

		return true;
	}

	/**
	 * 正常レスポンス
	 */
	private function _success()
	{
		return $this->response(array(
			'status' => 200,
			'message' => 'OK',
		));
	}

}
