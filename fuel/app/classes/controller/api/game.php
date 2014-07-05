<?php

class Controller_Api_Game extends Controller_Rest
{
  public function before()
  {
    parent::before();
  }

  // get post data ( and validation )
  private static function _getIds()
  {
    $val = Validation::forge();
    $val->add('game_id', 'game_id')->add_rule('required');
    $val->add('team_id', 'team_id')->add_rule('required');

    if ( ! $val->run() ) {
      throw new Exception($val->show_errors());
    }

    $ids = $val->validated();

    // check acl if no admin
    if ( ! Auth::has_access('admin.admin') and Auth::has_access('moderator.moderator') )
    {
      // has Moderators ?
      if ( ! Auth::member('50') )
      {
        throw new Exception('権限がありません');
      }

      // Moderatorsだとして、自分のチームの試合ですか？
      if ( $ids['team_id'] !== Model_Player::getMyTeamId() )
      {
        throw new Exception('権限がありません');
      } 
    }

    // check game status
    $action = Request::main()->action;
    $status = Model_Game::get_game_status($ids['game_id'], $ids['team_id']);
    if ( $action !== 'updateStatus' and $status == 2 )
    {
      throw new Exception('既に成績入力を完了している試合です'); 
    }

    return $ids;
  }

  public function post_updateStatus()
  {
    $ids = self::_getIds();

    $ret = Model_Game::update_status(
      $ids['game_id'],
      $ids['team_id'],
      Input::post('status')
    );

    if ( ! $ret )
      throw new Exception('ステータスのアップデートに失敗しました');
  
    return "OK";
  }

  public function post_updateScore()
  {
    // 権限チェック
    if ( ! Auth::has_access('game.editall') )
      return Response::forge('スコアを編集する権限がありません', 403);

    $form = Fieldset::forge('score');
    $form->add_model(Model_Games_Runningscore::forge());

    $val = $form->validation();

    if ( ! $val->run() )
      return Response::forge('NG', 400);
  
    $score = Model_Games_Runningscore::find( Input::post('game_id') );
    $score->set(Input::post());
    $score->save();

    echo 'OK';
  }

  // 出場選手
  public function post_updatePlayer()
  {
    // 権限チェック
    if ( ! Auth::has_access('game.editall') )
      return Response::forge('出場選手を編集する権限がありません', 403);

    $ids = self::_getIds();

    // json登録(old)
    $players = Input::post('players');

    $game = Model_Games_Stat::query()
              ->where(array($ids))
              ->get_one();

    $game->players = json_encode($players); 
    $game->save();

    // stats_metaへの登録
    Model_Stats_Player::registPlayer($ids, $players);

    // status update
    Model_Game::update_status_minimum($ids['game_id'], 1);

    echo 'OK';
  }

  public function post_updatePitcher()
  {
    $ids = self::_getIds();

    // insert (json形式
    // - TODO いつか消す
    $pitcher = Input::post('stats');

    $game = Model_Games_Stat::query()
              ->where(array($ids))
              ->get_one();
    $game->pitchers = json_encode($pitcher); 
    $game->save();

    // stats_pitchingsへのinsert
    $status = Input::post('complete') ? 1 : 0;
    if ( Auth::has_access('admin.admin') )
    {
      Model_Stats_Pitching::replaceAll($ids, $pitcher, $status);
    }
    else
    {
      Model_Stats_Pitching::regist($ids, $pitcher, $status);
    }

    echo 'OK';
  }

  public function post_updateBatter()
  {
    $ids = self::_getIds();

    // insert (json形式
    // - TODO いつか消す
    $batter = Input::post('batter');

    $game = Model_Games_Stat::query()
            ->where(array($ids))
            ->get_one();

    $game->batters = json_encode($batter); 
    $game->save();

    // satasへの登録
    $status = Input::post('complete') ? 1 : 0;

    if ( Auth::has_access('admin.admin') )
    {
      Model_Stats_Hitting::replaceAll($ids, $batter, $status);
    }
    else
    {
      Model_Stats_Hitting::regist($ids, $batter, $status);
    }

    echo 'OK';
  }

  public function post_updateOther()
  {
    // 権限チェック
    if ( ! Auth::has_access('game.editall') )
      return Response::forge('編集する権限がありません', 403);

    $ids = self::_getIds();

    // insert
    $other = Input::post('other');

    $game = Model_Games_Stat::query()
              ->where('game_id', $ids['game_id'])
              ->where('team_id', $ids['team_id'])
              ->get_one();

    $game->others = json_encode($other); 
    $game->save();

    echo 'OK';
  }
}
