<?php

class Controller_Team_Game extends Controller_Team
{
	public $_game = array();

	public function before()
	{
		parent::before();

		// game 情報
		if ($game_id = $this->param('game_id'))
		{
			if ( ! $this->_game = Model_Game::find($game_id))
			{
				Session::set_flash('error', '試合情報が取得できませんでした。');
				return Response::redirect('team/'.self::$_team->url_path);
			}
		}

		// 試合概要のURL
		if ($this->_game)
		{
			$this->_game->href = $this->_team->href.'/game/'.$this->_game->id;
		}

		// set global
		$this->set_global('game', $this->_game);
	}

	/**
	 * 試合一覧
	 */
	public function action_index()
	{
		$view = View::forge('team/game/index.twig');

		$view->games = Model_Game::get_info_by_team_id($this->_team->id);

		return Response::forge($view);
	}

	public function action_add()
	{
		$view = View::forge('team/game/add.twig');

		$form = self::_addgame_form();

		if (Input::post())
		{
			$val = $form->validation();
			if ($val->run())
			{
				$props = Input::post() + array('team_id' => $this->_team->id);
				if (Model_Game::regist($props))
				{
					Session::set_flash('info', '新規ゲームを追加しました');
					return Response::redirect('team/'.$this->_team->url_path);
				}
				else
				{
					Session::set_flash('error', 'システムエラーが発生しました。');
				}
			}
			else
			{
				Session::set_flash('error', $val->show_errors());
			}

			$form->repopulate();
		}

		$view->set_safe('form', $form->build());

		return Response::forge($view);
	}

	public function action_detail()
	{
		$view = View::forge('team/game/detail.twig');

		// チーム情報
		$games_teams = $this->_game->games_teams;

		if ($games_teams->order === 'top')
		{
			$view->team_top = $this->_team->name;
			$view->team_bottom = $games_teams->opponent_team_name;
		}
		else
		{
			$view->team_top = $games_teams->opponent_team_name;
			$view->team_bottom = $this->_team->name;
		}

		// score
		$view->score = $this->_game->games_runningscores;

		if ($view->score->last_inning < 7)
		{
			$view->score->last_inning = 7;
		}

		// stats
		$view->stats = array(
			'hitting'  => array(
				'players' => Model_Stats_Hitting::get_stats_by_playeds($this->_game->id, $this->_team->id),
				'total'   => Model_Stats_Hitting::get_stats_total($this->_game->id, $this->_team->id),
			),
			'pitching' => array(
				'players' => Model_Stats_Pitching::get_stats_by_playeds($this->_game->id, $this->_team->id),
				'total'   => array(),
			),
		);

		return Response::forge($view);
	}

	public function action_edit()
	{
		$game_id = $this->_game->id;
		$team_id = $this->_team->id;
		$kind = $this->param('kind');
		$type = Input::get('type');

		// playerが捕れない場合はログインさせる
		if ( ! $this->_player)
		{
			return Response::redirect('/login?url='.Uri::current());
		}

		// kind validation
		if ( ! in_array($kind, array('score', 'player', 'other', 'batter', 'pitcher')))
		{
			Session::set_flash('error', '不正なURLです。');
			return Response::redirect($this->_team->href);
		}

		// team_admin 権限チェック
		if (in_array($kind, array('score', 'player', 'other')) and ! $this->_team_admin)
		{
			Session::set_flash('error', '権限がありません。');
			return Response::redirect($this->_game->href);
		}

		if ($type === 'all' and ! $this->_team_admin)
		{
			Session::set_flash('error', '権限がありません。');
			return Response::redirect($this->_game->href);
		}

		// view load
		$view = Theme::instance()->view('team/game/edit/'.$kind.'.twig');

		// 出場選手
		$view->playeds = Model_Stats_Player::get_starter($game_id, $team_id);

		// stats data
		switch ($kind)
		{
			case 'score':
				// award
				$view->awards = Model_Stats_Award::find_by_game_id($this->_game->id);

				// score
				$score = $this->_game->games_runningscores;

				// 初回は必ず必要
				$view->scores = array(
					array(
						'top'    => $score->t1,
						'bottom' => $score->b1,
					),
				);

				// ２回以降
				for ($i = 2; $i <= 18; $i++)
				{
					if ($score['t'.$i] === null and $score['b'.$i] === null)
						break;

					$view->scores[] = array(
						'top'    => $score['t'.$i],
						'bottom' => $score['b'.$i],
					);
				}

				// 合計
				$this->_game->tsum = $score->tsum;
				$this->_game->bsum = $score->bsum;
			break;

			// case 'player':
			// break;
			// case 'other':
			// break;
			case 'batter':
				// 出場選手と成績
				if ($type === 'all')
				{
					$view->batters = Model_Stats_Hitting::get_stats_by_playeds(
						$game_id, $team_id);
				}
				else
				{
					$view->batters = Model_Stats_Hitting::get_stats_by_playeds(
						$game_id, $team_id, $this->_player->id);
				}

				// 打席結果一覧
				$view->results = Model_Batter_Result::get_all();
			break;

			case 'pitcher':
				// 出場選手と成績
				if ($type === 'all')
				{
					$view->pitchers = Model_Stats_Pitching::get_stats_by_playeds(
						$game_id, $team_id);
				}
				else
				{
					$view->pitchers = Model_Stats_Pitching::get_stats_by_playeds(
						$game_id, $team_id, $this->_player->id);
				}
			break;

			default:
				// no logic
			break;
		}

		// 所属選手
		$view->players = Model_Player::get_players($team_id);

		// 対戦相手
		$view->games_teams = $this->_game->games_teams;

		return Response::forge($view);
	}

	static private function _addgame_form()
	{
		$config = array('form_attributes' => array('class' => 'form'));
		$form = Fieldset::forge('addgame', $config);

		// 試合実施日
		$form->add('date', '試合実施日', array(
			'type'             => 'text',
			'class'            => 'form-control form-datepicker',
			'value'            => date('Y-m-d'),
			'data-date-format' => 'yyyy-mm-dd',
		))
			->add_rule('required')
			->add_rule('trim');

		// - 試合開始時間
		$form->add('start_time', '試合開始時間', array(
			'type'  => 'hidden', // 未実装
			'class' => 'form-control',
		))
			->add_rule('trim');

		// - 対戦チーム名
		$form->add('opponent_team_name', '対戦チーム名', array(
			'type'  => 'text',
			'class' => 'form-control',
		))
			->add_rule('required')
			->add_rule('trim');

		// - 先攻/後攻
		$form->add('order', '先攻/後攻', array(
			'type'    => 'select',
			'class'   => 'form-control',
			'options' => array('top' => '先攻', 'bottom' => '後攻'),
		))
			->add_rule('required')
			->add_rule('in_array', array('top', 'bottom'));

		// - 球場
		$form->add('stadium', '球場', array(
			'type'  => 'text',
			'class' => 'form-control',
		))
			->add_rule('trim');

		// - メモ
		$form->add('memo', '試合コメント/メモ', array(
			'type'  => 'textarea',
			'class' => 'form-control',
		))
			->add_rule('trim');

		// submit
		$form->add('submit', '', array(
			'type'  => 'submit',
			'value' => '登録',
			'class' => 'btn btn-success',
		));

		return $form;
	}
}
