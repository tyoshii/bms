<?php

class Model_Game extends \Orm\Model
{
	protected static $_properties = array(
		'id',
		'date',
    'stadium' => array(
      'default' => '',
    ),
    'memo' => array(
      'default' => '',
    ),
		'team_top',
		'team_top_name',
		'team_bottom',
		'team_bottom_name',
		'game_status' => array(
      'default' => 0,
    ),
    'top_status' => array(
      'default' => 1,
    ),
    'bottom_status' => array(
      'default' => 1,
    ),
		'created_at',
		'updated_at',
	);

	protected static $_observers = array(
		'Orm\Observer_CreatedAt' => array(
			'events' => array('before_insert'),
			'mysql_timestamp' => false,
		),
		'Orm\Observer_UpdatedAt' => array(
			'events' => array('before_update'),
			'mysql_timestamp' => false,
		),
	);
	protected static $_table_name = 'games';

  protected static $_has_many = array(
    'games_runningscores' => array(
      'model_to' => 'Model_Games_Runningscore',
      'key_from' => 'id',
      'key_to' => 'game_id',
      'cascade_save' => true,
      'cascade_delete' => false,
    ),
    'stats_players' => array(
      'model_to' => 'Model_Stats_Player',
      'key_from' => 'id',
      'key_to' => 'game_id',
      'cascade_save' => true,
      'cascade_delete' => false,
    ),
  );

  public static function createNewGame($data)
  {
    try {
      DB::start_transaction();

      // チーム名
      $team_top_name    = $data['top_name'] ?: Model_Team::find($data['top'])->name;
      $team_bottom_name = $data['bottom_name'] ?: Model_Team::find($data['bottom'])->name;
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
      Model_Stats_Player::createNewGame($game->id, $data['top']);
      Model_Stats_Player::createNewGame($game->id, $data['bottom']);

      // json data のデフォルト値
      // - TODO なくしたい
      Model_Games_Stat::createNewGame($game->id, $data['top'], $data['bottom']);
  
      DB::commit_transaction();

    } catch ( Exception $e ) {
      DB::rollback_transaction();
      Session::set_flash('error', '内部処理エラー:'.$e->getMessage() );
      return false;
    }

    return true;
  }

  public static function get_info()
  {
    // base query
    $query = self::_get_info_query();

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
      }
      
      // 試合結果を配列に付与
      $score = $res->games_runningscores;
      $score = $score[$index];

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

  public static function get_info_by_team($team_id = null)
  {
    if ( ! $team_id ) return array();

    // base query
    $query  = self::_get_info_query();
 
    // add where : 先攻後攻どちらかがチームIDだったら
    $query->where_open();
    $query->or_where('team_top', $team_id);
    $query->or_where('team_bottom', $team_id);
    $query->where_close();

    $games = $query->get();

    // scoreの合計を結果に
    foreach ( $games as $id => $game )
    {
      $score = $game->games_runningscores;
      $game->tsum = $score[$id]->tsum;
      $game->bsum = $score[$id]->bsum;
    }

    return $games;
  }

  public static function update_status_minimum($game_id, $status)
  {
    $game = self::find($game_id);    

    if ( $game->game_status < $status )
      $game->game_status = $status;

    if ( $game->top_status < $status )
      $game->top_status = $status;

    if ( $game->bottom_status < $status )
      $game->bottom_status = $status;
    
    $game->save();
  }

  public static function get_game_status($game_id, $team_id = null)
  {
    $game = self::find($game_id);
  
    // game_idが無効
    if ( ! $game ) return null;

    // 管理者権限
    if ( Auth::has_access('admin.admin') )
      return $game->game_status;

    // チームIDの指定がない
    if ( ! $team_id ) return null;

    // 先攻 or 後攻
    if ( $game->team_top == $team_id )
      return $game->top_status;

    if ( $game->team_bottom == $team_id )
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
    $team_id = Model_Player::find($player_id)->team;
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
    $query = DB::select()->from(self::$_table_name);

    $query->join('games_runningscores')->on('games.id', '=', 'games_runningscores.game_id');

    $query->where('game_status', '!=', -1);
    $query->order_by('date', 'desc');

    return $query;
  }
}
