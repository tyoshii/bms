<?php

class Controller_Team_Game extends Controller_Team
{
	public $_game = array();

	public function before()
	{
		parent::before();

		// game 情報
		if ( $game_id = $this->param('game_id') )
		{
			if ( ! $this->_game = Model_Game::find($game_id) )
			{
				Session::set_flash('error', '試合情報が取得できませんでした。');
				return Response::redirect('team/'.self::$_team->url_path);
			}
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
    return Response::forge($view);
  }

	public function action_add()
	{
		$view = View::forge('team/game/add.twig');

		$form = self::_addgame_form();

		if ( Input::post() )
		{
			$val = $form->validation();
			if ( $val->run() )
			{
				$props = Input::post() + array('team_id' => $this->_team->id);
				if ( Model_Game::regist($props) )
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
		$games_teams = reset($this->_game->games_teams);

		if ( $games_teams->order === 'top' )
		{
			$view->team_top    = $this->_team->name;
			$view->team_bottom = $games_teams->opponent_team_name;
		}
		else
		{
			$view->team_top    = $games_teams->opponent_team_name;
			$view->team_bottom = $this->_team->name;
		}

		$view->score       = reset($this->_game->games_runningscores);
		$view->stats       = array(
			'player'   => Model_Stats_Player::getStarter($this->_game->id, $this->_team->id),
			'hitting'  => ModeL_Stats_Hitting::get_stats($this->_game->id, $this->_team->id),
			'pitching' => Model_Stats_Pitching::get_stats($this->_game->id, $this->_team->id),
		);

		return Response::forge($view);
	}

	public function action_edit()
	{
		$view = View::forge('team/game/edit.twig');

		$game_id = $this->_game->id;
		$team_id = $this->_team->id;
		$kind    = $this->param('kind');

		// kind validation
		if ( ! in_array($kind, array('score', 'player', 'other', 'batter', 'pitcher')) )
		{
			Session::set_flash('error', '不正なURLです。');
			return Response::redirect('team/'.$this->_team->url_path);
		}

		// 所属選手
		$view->players = Model_Player::get_player($team_id);

		// 出場選手
		$view->playeds = Model_Stats_Player::getStarter($game_id, $team_id);

		// game_status
		// TODO: input_status に切り替える
		$view->game_status = Model_Game::get_game_status($game_id, $team_id);

		// stats data
		switch ( $kind )
		{
			case 'score':
				$view->score = reset($this->_game->games_runningscores);

			break;

			case 'player':
			break;
			case 'other':
			break;
			case 'batter':
			break;
			case 'pitcher':
			break;
			default:
			break;
		}

		return Response::forge($view);
	}

  static private function _addgame_form()
  {
    $config = array('form_attributes' => array('class' => 'form',));
    $form   = Fieldset::forge('addgame', $config);

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
