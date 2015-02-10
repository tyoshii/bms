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
			$this->_game->href = '/game/'.$this->_game->id;
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

	/**
	 * add new game
	 */
	public function action_add()
	{
		$view = View::forge('team/game/add.twig');

		$form = Model_Game::get_regist_form();

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

	/**
	 * game detail
	 */
	public function action_detail()
	{
		$view = View::forge('team/game/detail.twig');

		// チーム情報
		$games_team = $this->_game->games_team;

		if ($games_team->order === 'top')
		{
			$view->team_top = $this->_team->name;
			$view->team_bottom = $games_team->opponent_team_name;
		}
		else
		{
			$view->team_top = $games_team->opponent_team_name;
			$view->team_bottom = $this->_team->name;
		}

		// score
		$view->score = $this->_game->games_runningscore;

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

	/**
	 * game stats input page
	 */
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

		// 所属しているチームかどうか
		if ( ! Model_Player::is_belong($this->_team->id))
		{
			Session::set_flash('error', 'そのチームの権限がありません');
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

		// view load and set
		$view = Theme::instance()->view('team/game/edit/'.$kind.'.twig');

		// 出場選手
		$view->playeds = Model_Stats_Player::get_starter($game_id, $team_id);
		// 所属選手
		$view->players = Model_Player::get_players($team_id);
		// 対戦相手
		$view->games_team = $this->_game->games_team;
		// type（保存・完了ボタンの出し分け）
		$view->type = $type;

		// stats data
		switch ($kind)
		{
			case 'score':
				// award
				$view->awards = Model_Stats_Award::find_by_game_id($this->_game->id);

				// score
				$score = $this->_game->games_runningscore;

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

		return Response::forge($view);
	}
}
