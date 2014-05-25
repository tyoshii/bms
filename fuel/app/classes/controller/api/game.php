<?php

class Controller_Api_Game extends Controller_Rest
{
  public function before()
  {
    parent::before();
  }

  // game_id/team_id
  private static function _getIds()
  {
    $val = Validation::forge();
    $val->add('game_id')->add_rule('required');
    $val->add('team_id')->add_rule('required');

    if ( ! $val->run() ) {
      throw new Exception();
    }

    $ids = $val->validated();

    // check acl if no admin
    if ( ! Auth::has_access('admin.admin') )
    {
      // has Moderators ?
      if ( ! Auth::member('50') )
      {
        throw new Exception('権限がありません');
      }

      // Moderatorsだとして、自分のチームの試合ですか？
      if ( $ids['game_id'] !== Model_Player::getMyTeamId() )
      {
        throw new Exception('権限がありません');
      } 
    }

    return $ids;
  }

  public function post_updateStatus()
  {
    $game = Model_Game::find(Input::post('id'));
    $game->game_status = Input::post('status');
    $game->save();

    return "OK";
  }

  public function post_updateScore()
  {
    // 権限チェック
    if ( ! Auth::has_access('game.editall') )
      return Response::forge('出場選手を編集する権限がありません', 403);

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
    $pitcher = Input::post('pitcher');

    $game = Model_Games_Stat::query()
              ->where(array($ids))
              ->get_one();
    $game->pitchers = json_encode($pitcher); 
    $game->save();

    // stats_pitchingsへのinsert
    if ( Auth::has_access('admin.admin') )
    {
      Model_Stats_Pitching::replaceAll($ids, $pitcher);
    }
    else
    {
      Model_Stats_Pitching::regist($ids, $pitcher);
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
    if ( Auth::has_access('admin.admin') )
    {
      Model_Stats_Hitting::replaceAll($ids, $batter);
    }
    else
    {
      Model_Stats_Hitting::regist($ids, $batter);
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
