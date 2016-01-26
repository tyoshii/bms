<?php

class Model_Game extends \Orm\Model
{
	protected static $_primary_key = array('id');

	protected static $_properties = array(
		'id',
		'date' => array(
			'type' => 'varchar',
			'label' => '試合日',
			'form' => array(
				'type' => 'text',
				'class' => 'form-control form-datepicker',
				'data-date-format' => 'yyyy-mm-dd',
			),
			'validation' => array(
				'required',
				'valid_date' => array('Y-m-d'),
			),
		),
		'start_time' => array(
			'type' => 'varchar',
			'label' => '開始時間',
			'form' => array(
				'type' => 'time',
				'class' => 'form-control',
			),
		),
		'stadium' => array(
			'default' => '',
			'type' => 'varchar',
			'label' => '球場',
			'form' => array(
				'type' => 'text',
				'class' => 'form-control',
			),
			'validation' => array(
				'required',
				'max_length' => array(64),
			),
		),
		'memo' => array(
			'default' => '',
			'type' => 'varchar',
			'label' => 'メモ',
			'form' => array(
				'type' => 'textarea',
				'class' => 'form-control',
			),
			'validation' => array(
				'max_length' => array(256),
			),
		),
		'game_status'   => array('default' => 0, 'form' => array('type' => false)),
		'top_status'    => array('default' => 0, 'form' => array('type' => false)),
		'bottom_status' => array('default' => 0, 'form' => array('type' => false)),
		'created_at'    => array('default' => 0, 'form' => array('type' => false)),
		'updated_at'    => array('default' => 0, 'form' => array('type' => false)),
	);

	protected static $_observers = array(
		'Orm\Observer_CreatedAt' => array(
			'events'          => array('before_insert'),
			'mysql_timestamp' => false,
		),
		'Orm\Observer_UpdatedAt' => array(
			'events'          => array('before_update'),
			'mysql_timestamp' => false,
		),
	);
	protected static $_table_name = 'games';

	protected static $_has_one = array(
		'games_runningscore' => array(
			'model_to'       => 'Model_Games_Runningscore',
			'key_from'       => 'id',
			'key_to'         => 'game_id',
			'cascade_save'   => false,
			'cascade_delete' => false,
		),
		'games_team' => array(
			'model_to'       => 'Model_Games_Team',
			'key_from'       => 'id',
			'key_to'         => 'game_id',
			'cascade_save'   => false,
			'cascade_delete' => false,
		),
	);

	protected static $_has_many = array(
		'games_teams' => array(
			'model_to'       => 'Model_Games_Team',
			'key_from'       => 'id',
			'key_to'         => 'game_id',
			'cascade_save'   => false,
			'cascade_delete' => false,
		),
		'stats_players' => array(
			'model_to'       => 'Model_Stats_Player',
			'key_from'       => 'id',
			'key_to'         => 'game_id',
			'cascade_save'   => false,
			'cascade_delete' => false,
		),
	);
	
	protected static $_belongs_to = array(
		'stats_players' => array(
			'model_to'       => 'Model_Stats_Player',
			'key_from'       => 'id',
			'key_to'         => 'game_id',
			'cascade_save'   => false,
			'cascade_delete' => false,
		),
		'stats_hittings' => array(
			'model_to'       => 'Model_Stats_Hitting',
			'key_from'       => 'id',
			'key_to'         => 'game_id',
			'cascade_save'   => false,
			'cascade_delete' => false,
		),
		'stats_pitchings' => array(
			'model_to'       => 'Model_Stats_Pitching',
			'key_from'       => 'id',
			'key_to'         => 'game_id',
			'cascade_save'   => false,
			'cascade_delete' => false,
		),
		'conventions_game' => array(
			'model_to'       => 'Model_Conventions_Game',
			'key_from'       => 'id',
			'key_to'         => 'game_id',
			'cascade_save'   => false,
			'cascade_delete' => false,
		),
	);

	/**
	 * 試合追加のフォーム
	 */
	private static function _get_regist_form()
	{
		$form = Fieldset::forge('regist_game', array('form-attribute' => array(
			'class' => 'form',
		)))->add_model(static::forge());

		// start_time : PCサイトでは時間と分の入力に分ける
		if ( ! Agent::is_mobiledevice())
		{
			// start_hour / start_minの追加
			// TODO: Fieldsetのテーブルだと表示がいまいちなので、start_timeのままにしている

			$field = $form->field('start_time');

			$field->set_type('select');
			$field->set_attribute('class', 'select2');

			// start_timeのoption追加
			$options = array();
			for ($hour = 0; $hour < 24; $hour++)
			{
				for ($time = 0; $time <= 45; $time += 15)
				{
					$value = sprintf('%02d:%02d', $hour, $time);
					$options[$value] = $value;
					$form->field('start_time')->set_options($value, $value);
				}
			}
	
			$form->field('start_time')->add_rule('in_array', array_keys($options));
		}
		
		// submit
		$form->add('submit', '', array(
			'type' => 'submit',
			'class' => 'form-control btn btn-success',
			'value' => '追加',
		));

		return $form;
	}

	/**
	 * 登録されている試合情報から、実施された"年"の情報を抽出
	 *
	 */
	public static function get_distinct_year()
	{
		$query = DB::select(DB::expr('DATE_FORMAT(date, "%Y") as year'))->from(static::$_table_name)->distinct(true);
		$result = $query->execute()->as_array('year');
		$result[date('Y')] = 1;

		$years = array_unique(array_keys($result));
		arsort($years);

		return $years;
	}

	/**
	 * 所属チームにおける試合追加のフォーム
	 * 
	 * 対戦相手はテキストで入力など
	 */
	public static function get_regist_form()
	{
		$form = static::_get_regist_form();

		// 対戦チーム
		$form->add_before('opponent_team_name', '対戦チーム名',
			array(
				'type' => 'text',
				'class' => 'form-control',
			),
			array(
				'required',
				'trim',
			),
			'stadium'
		);

		// order
		$options = array('top' => '先攻', 'bottom' => '後攻');
		$form->add_before('order', '先攻/後攻',
			array(
				'type' => 'select',
				'class' => 'form-control',
				'options' => $options,
			),
			array(
				'required',
				'in_array' => array(array_keys($options)),
			),
			'stadium'
		);

		return $form;
	}

	/**
	 * 大会における試合追加のフォーム
	 *
	 * BMSに登録してあるチーム同士の試合追加
	 */
	public static function get_regist_form_convention($convention_id)
	{
		$form = static::_get_regist_form();

		// 大会参加チーム
		$results = Model_Conventions_Team::get_entried_teams($convention_id);
		$teams   = array();

		foreach ($results as $result)
		{
			$teams[$result->team_id] = $result->team->name;
		}

		// 対戦チーム
		$form->add_before('top', '先攻',
			array(
				'type' => 'select',
				'class' => 'form-control',
				'options' => $teams,
				'required',
			),
			array(),'memo'
		)
			->add_rule('required')
			->add_rule('in_array', array_keys($teams));

		$form->add_before('bottom', '後攻',
			array(
				'type' => 'select',
				'class' => 'form-control',
				'options' => $teams,
				'required',
			),
			array(),'memo'
		)
			->add_rule('required')
			->add_rule('in_array', array_keys($teams));


		return $form;
	}

	/**
	 * 試合の追加
	 */
	public static function regist($props = false)
	{
		$props = $props ?: Input::post();
		extract($props);

		try {
			Mydb::begin();

			// gamesテーブルへの保存
			$game = static::forge(array(
				'date'       => $date,
				'start_time' => $start_time,
				'stadium'    => $stadium,
				'memo'       => $memo,
			));
			$game->save();

			// games_runningscoresテーブルへの登録
			Model_Games_Runningscore::regist($game->id);

			// games_teamsテーブルへの保存
			$values = array();
			if (isset($order))
			{
				// 先攻or後攻が指定されていれば、チームでの試合登録と判断
				$values[] = array(
					'game_id' => $game->id,
					'team_id' => $team_id,
					'order'   => $order,
					'opponent_team_id'   => 0,
					'opponent_team_name' => $opponent_team_name,
				);
			}
			else
			{
				// 大会における試合登録
				// games 1 : 2 games_teams のレコード数になる
				$values[] = array(
					'game_id' => $game->id,
					'team_id' => $top,
					'order'   => 'top',
					'opponent_team_id'   => $bottom,
					'opponent_team_name' => Model_Team::find($bottom)->name,
				);
				$values[] = array(
					'game_id' => $game->id,
					'team_id' => $bottom,
					'order'   => 'bottom',
					'opponent_team_id'   => $top,
					'opponent_team_name' => Model_Team::find($top)->name,
				);
			}

			foreach ($values as $value)
			{
				if ( ! Model_Games_Team::regist($value)) 
				{
					throw new Exception('新規ゲーム登録に失敗しました。');
				}
			}

			// stats_players(starter)
			if (isset($team_id))
			{
				Model_Stats_Player::create_new_game($game->id, $team_id);
			}
			else
			{
				Model_Stats_Player::create_new_game($game->id, $top);
				Model_Stats_Player::create_new_game($game->id, $bottom);
			}

			// success
			Mydb::commit();
			return $game;
		}
		catch (Exception $e)
		{
			Mydb::rollback();
			Log::error('内部処理エラー:'.$e->getMessage());
			return false;
		}
	}

	public static function create_new_game($data)
	{
		try
		{
			Mydb::begin();

			// init
			// TODO: create_new_gameの見直しのときに一緒に
			if ( ! array_key_exists('top_name', $data)) $data['top_name'] = null;
			if ( ! array_key_exists('bottom_name', $data)) $data['bottom_name'] = null;

			// チーム名
			$team_top_name = $data['top_name'] ? : Model_Team::find($data['top'])->name;
			$team_bottom_name = $data['bottom_name'] ? : Model_Team::find($data['bottom'])->name;
			// games insert
			$game = self::forge(array(
				'date'             => $data['date'],
				'team_top'         => $data['top_name'] ? 0 : $data['top'],
				'team_top_name'    => $team_top_name,
				'team_bottom'      => $data['bottom_name'] ? 0 : $data['bottom'],
				'team_bottom_name' => $team_bottom_name,
			));

			$game->save();

			// other table default value
			Model_Games_Runningscore::regist($game->id);
			Model_Stats_Player::create_new_game($game->id, $data['top']);
			Model_Stats_Player::create_new_game($game->id, $data['bottom']);

			// json data のデフォルト値
			// - TODO なくしたい
			Model_Games_Stat::create_new_game($game->id, $data['top'], $data['bottom']);

			Mydb::commit();
		}
		catch (Exception $e)
		{
			Mydb::rollback();
			Session::set_flash('error', '内部処理エラー:'.$e->getMessage());
			return false;
		}

		return true;
	}

	/**
	 * (descript TBD)
	 * @return object Model_Game
	 */
	public static function get_info()
	{
		// base query
		$query = self::_get_info_query();

		// 対戦相手
		$query->related('games_team');

		// 自分が出場している試合かどうかをサブクエリで取得する
		// defaultがleft joinなので、join_onに条件追加
		// 出場していない場合はnullとなる。
		$query->related('stats_players', array(
			'join_on' => array(
				array('player_id', '=', Model_Player::get_my_player_id())
			),
		));

		// execute
		$result = $query->get();

		// add value
		foreach ( $result as $index => $res )
		{
			// 自分が出場している試合
			if ( $stats = $res->stats_players )
			{
				$result[$index]['play'] = true;
			}

			// ログインしている場合、自分のチームの試合にflag
			// - 加えて、game.statusをセット
			$result[$index]['own'] = false;

			if ( Auth::has_access('admin.admin') )
			{
				$result[$index]['own']    = 'admin';
				$result[$index]['status'] = $result[$index]['game_status'];
			}

			if ( $team_id = Model_Player::get_my_team_id() )
			{
/*
				if ( $res['team_top'] == $team_id ) 
				{
					$result[$index]['own']    = 'top';
					$result[$index]['status'] = $result[$index]['top_status'];
				}
				else if ( $res['team_bottom'] == $team_id )
				{
					$result[$index]['own']    = 'bottom';
					$result[$index]['status'] = $result[$index]['bottom_status'];
				}
*/
			}
			
			// 試合結果を配列に付与
			$score = $res->games_runningscore;

			// 合計
			$result[$index]['tsum'] = $score['tsum'];
			$result[$index]['bsum'] = $score['bsum'];

			if ( $score['tsum'] > $score['bsum'] )
			{
				$result[$index]['top_result'] = '○';
				$result[$index]['bottom_result'] = '●';
					
				$result[$index]['result'] = $result[$index]['own'] === 'top' ? 'win' : 'lose';
			}
			else if ( $score['tsum'] < $score['bsum'] )
			{
				$result[$index]['top_result'] = '●';
				$result[$index]['bottom_result'] = '○';

				$result[$index]['result'] = $result[$index]['own'] === 'top' ? 'lose' : 'win';
			}
			else
			{
				$result[$index]['top_result'] = '△';
				$result[$index]['bottom_result'] = '△';

				$result[$index]['result'] = 'even';
			}

		}

		return $result;
	}

	public static function get_info_by_team_id($team_id = null, $year = null)
	{
		if ( ! $team_id ) return array();

		// $year default
		if (! $year)
			$year = date('Y');

		// base query
		$query  = self::_get_info_query($year);

		// related games_teams
		// has_manyのrelationで取得して、あとで配列から戻す
		// 配列から戻すのは、変更前との互換性のため
		$query->related('games_teams')
			->where('games_teams.team_id', $team_id);

		$games = $query->get();

		foreach ( $games as $id => $game )
		{
			// scoreの合計を結果に
			$score = $game->games_runningscore;
			$game->tsum = $score->tsum;
			$game->bsum = $score->bsum;

			// games_team
			$team = array_shift($game->games_teams);

			// games_teamへコピー（後方互換性
			$game->games_team = $team;

			// result
			if ($team->order === 'top')
			{
				$my_score = $score->tsum;
				$oppo_score = $score->bsum;
			}
			else
			{
				$my_score = $score->bsum;
				$oppo_score = $score->tsum;
			}

			if ($my_score === $oppo_score) $game->result = '△';
			if ($my_score > $oppo_score) $game->result = '○';
			if ($my_score < $oppo_score) $game->result = '●';
		}

		return $games;
	}

	public static function update_status_minimum($game_id, $status)
	{
		$game = self::find($game_id);

		if ($game->game_status < $status)
			$game->game_status = $status;

		if ($game->top_status < $status)
			$game->top_status = $status;

		if ($game->bottom_status < $status)
			$game->bottom_status = $status;

		$game->save();
	}

	public static function update_status($game_id, $status)
	{
		$game = self::find($game_id);
		if ( ! $game ) return false;

		// 大会の場合は、大会管理者だけが出来るようにする。

		$game->game_status = $status;
		$game->save();

		return true;
	}

	// player_idが出場した試合一覧
	public static function get_play_game($player_id)
	{
		$query = DB::select()->distinct(true)->from('stats_players');
		$query->join('games', 'LEFT')->on('games.id', '=', 'stats_players.game_id');
		$query->where('stats_players.player_id', $player_id);
		$query->where('games.game_status', '!=', -1);

		$query->order_by('date');

		return $query->execute()->as_array('game_id');
	}

	public static function get_incomplete_gameids($player_id)
	{
		$team_id = Model_Player::find($player_id)->team_id;
		$play_game_ids = self::get_play_game($player_id);

		$alert_games = array();
		foreach ( $play_game_ids as $game_id => $data )
		{
			// game_statusのチェック
			// 成績入力中のものに関してのみアラートを表示する
			if ( $data['game_status'] !== '1' )
			{
				continue;
			}

			// 野手成績の入力が完了しているかどうか
			if (Model_Stats_Hitting::get_input_status($game_id, $player_id) !== 'complete')
			{ 
				$alert_games[] = array('kind' => 'batter') + $data;
				continue; 
			}
			
			// 投手成績のアラート
			if ( strstr($data['position'], '1') )
			{
				if (Model_Stats_Pitching::get_input_status($game_id, $player_id) !== 'complete')
				{ 
					$alert_games[] = array('kind' => 'pitcher') + $data;
					continue; 
				}
			}
		} 

		return $alert_games;
	}

	public static function remind_mail( $game_id, $team_id )
	{
		// played player
		$players = Model_Stats_Player::get_participate_players($game_id, $team_id);

		foreach ( $players as $index => $player )
		{
			$player_id = $player['player_id'];
			$paths = array();

			// player_idが0だったらスキップ（スタメン未登録
			if ( $player['player_id'] === '0' )
			{
				continue;
			}

			// statusをチェックして、未完了であればメール送信
			if (Model_Stats_Hitting::get_input_status($game_id, $player_id) !== 'complete')
			{ 
				$paths[] = "game/{$game_id}/batter/{$team_id}";
			}

			// 投手成績のアラート
			if ( in_array('1', $player['position'] ) )
			{
				if (Model_Stats_Pitching::get_input_status($game_id, $player_id) !== 'complete')
				{ 
					$paths[] = "game/{$game_id}/pitcher/{$team_id}";
				}
			}

			// 対象があればメール
			if (count($paths) !== 0)
				Common_Email::remind_game_stats($player_id, $paths);
		}
	}

	/**
	 * private function : return query for get game info
	 *
	 * @return : Queyry Builder object
	 */
	private static function _get_info_query($year = null)
	{
		// $year default
		if (! $year)
			$year = date('Y');

		$query = self::query()
			->where('game_status', '!=', -1)
			->where('date', 'between', array("$year-01-01", "$year-12-31"))
			->order_by('date', 'desc');

		$query->related('games_runningscore', array(
			'select' => array('tsum', 'bsum')
		));

		return $query;
	}
}
