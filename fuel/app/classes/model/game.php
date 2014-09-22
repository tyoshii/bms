<?php

class Model_Game extends \Orm\Model
{
	protected static $_primary_key = array('id');

	protected static $_properties = array(
		'id',
		'date',
		'stadium'          => array('default' => ''),
		'memo'             => array('default' => ''),
		'game_status'      => array('default' => 0),
		'top_status'       => array('default' => 1),
		'bottom_status'    => array('default' => 1),
		// TODO:削除
		'team_top'         => array('default' => 0),
		'team_top_name'    => array('default' => 0),
		'team_bottom'      => array('default' => 0),
		'team_bottom_name' => array('default' => 0),
		'created_at',
		'updated_at',
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
		'games_runningscores' => array(
			'model_to'       => 'Model_Games_Runningscore',
			'key_from'       => 'id',
			'key_to'         => 'game_id',
			'cascade_save'   => false,
			'cascade_delete' => false,
		),
		'games_teams' => array(
			'model_to'       => 'Model_Games_Team',
			'key_from'       => 'id',
			'key_to'         => 'game_id',
			'cascade_save'   => false,
			'cascade_delete' => false,
		),
	);

	protected static $_has_many = array(
		'stats_players' => array(
			'model_to'       => 'Model_Stats_Player',
			'key_from'       => 'id',
			'key_to'         => 'game_id',
			'cascade_save'   => false,
			'cascade_delete' => false,
		),
	);

	public static function regist($posts)
	{
		try {
			Mydb::begin();

			// gamesテーブルへの保存
			$game = self::forge(array(
				'date'       => $posts['date'],
				'start_time' => $posts['start_time'],
				'stadium'    => $posts['stadium'],
				'memo'       => $posts['memo'],
			));
			$game->save();

			// games_runningscores
			Model_Games_Runningscore::regist($game->id);

			// games_teamsへの保存
			// TODO : team_id / opponent_team_id / opponent_team_name のパラメータをPOSTしてもらう
			if ( ! Model_Games_Team::regist($posts + array('game_id' => $game->id)) )
			{
				throw new Exception('新規ゲーム登録に失敗しました。');
			}

			// stats_players(starter)
			Model_Stats_Player::create_new_game($game->id, $posts['team_id']);

			// opponent_team_idがteamsに登録されているものであればこちらも登録
			// TODO: conventionが実装されたら
			if ( array_key_exists('opponent_team_id', $posts) )
			{
/*
				// games_teamsへの保存
				Model_Games_Team::regist(
					$game->id,
					$posts['opponent_team_id'],
					$posts['team_id']
				);

				// stats_players(starter)
				Model_Stats_Player::create_new_game($game->id, $posts['opponent_team_id']);
*/
			}

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

	public static function get_info()
	{
		// base query
		$query = self::_get_info_query();

		// 対戦相手
		$query->related('games_teams');

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
			$score = $res->games_runningscores;

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

	public static function get_info_by_team_id($team_id = null)
	{
		if ( ! $team_id ) return array();

		// base query
		$query  = self::_get_info_query();
 
		// related games_teams
		$query->related('games_teams', array(
			'where' => array(
				array('team_id', '=', $team_id),
			),
		));

		$games = $query->get();

		foreach ( $games as $id => $game )
		{
			// scoreの合計を結果に
			$score = $game->games_runningscores;
			$game->tsum = $score->tsum;
			$game->bsum = $score->bsum;

			// games_teams
			$team = $game->games_teams;

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

	public static function get_game_status($game_id, $team_id = null)
	{
		$game = self::find($game_id);

		// game_idが無効
		if ( ! $game) return null;

		// 管理者権限
		if (Auth::has_access('admin.admin'))
			return $game->game_status;

		// チームIDの指定がない
		if ( ! $team_id) return null;

		$games_teams = $game->games_teams;
		if ( ! $games_teams ) return null;

		// 先攻 or 後攻
		if ( $games_teams->order === 'top' )
			return $game->top_status;

		if ( $games_teams->order === 'bottom' )
			return $game->bottom_status;

		// 該当なし
		return null;
	}

	public static function update_status($game_id, $team_id, $status )
	{
		$game = self::find($game_id);
		
		if ( ! $game ) return false;

		// 各種ステータス update
		if ( Auth::has_access('admin.admin') )
		{
			$game->game_status   = $status;
			$game->top_status    = $status;
			$game->bottom_status = $status;
		}
		else
		{
			if ( $game->team_top    == $team_id )  $game->top_status    = $status;
			if ( $game->team_bottom == $team_id )  $game->bottom_status = $status;
		
			// 両チームのステータスが同じだったらgame_statusもそれに合わせる
			if ( $game->team_top === $game->team_bottom )
				$game->game_status = $game->team_top;
		}

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
			if ( self::get_game_status($game_id, $team_id) !== '1' )
			{
				continue;
			}

			// 野手成績の入力が完了しているかどうか
			if ( Model_Stats_Hitting::get_status($game_id, $player_id) === '0' )
			{ 
				$alert_games[] = array('kind' => 'batter') + $data;
				continue; 
			}
			
			// 投手成績のアラート
			if ( strstr($data['position'], '1') )
			{
				if ( Model_Stats_Pitching::get_status($game_id, $player_id) === '0' )
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
		// played member
		$players = Model_Stats_Player::getStarter($game_id, $team_id);

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
			if ( Model_Stats_Hitting::get_status($game_id, $player_id) === '0' )
			{ 
				$paths[] = "game/{$game_id}/batter/{$team_id}";
			}

			// 投手成績のアラート
			if ( in_array('1', $player['position'] ) )
			{
				if ( Model_Stats_Pitching::get_status($game_id, $player_id) === '0' )
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
	private static function _get_info_query()
	{
		$query = self::query()
			->where('game_status', '!=', -1)
			->order_by('date', 'desc');

		$query->related('games_runningscores', array(
			'select' => array('tsum', 'bsum')
		));

		return $query;
	}
}
