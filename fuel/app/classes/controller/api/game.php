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
    $status = Model_Game::get_game_status($ids['game_id'], $ids['team_id']);
    if ($action !== 'updateStatus' and $action !== 'updateOther')
    {
      if ($status == 2)
      {
        throw new Exception('既に成績入力を完了している試合です');
      }
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

    if ( ! $ret)
      throw new Exception('ステータスのアップデートに失敗しました');

    Session::set_flash('info', '試合ステータスを更新しました。');
    return "OK";
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

    echo 'OK';
  }

  // 出場選手
  public function post_updatePlayer()
  {
    // 権限チェック
    if ( ! Auth::has_access('game.editall'))
      return Response::forge('出場選手を編集する権限がありません', 403);

    $ids = self::_getIds();

    // stats_metaへの登録
    $players = Input::post('stats');
    Model_Stats_Player::registPlayer($ids, $players);

    // status update
    Model_Game::update_status_minimum($ids['game_id'], 1);

    echo 'OK';
  }

  public function post_updatePitcher()
  {
    $ids = self::_getIds();

    // stats_pitchingsへのinsert
    $pitcher = Input::post('stats');
    $status = Input::post('complete') === 'true' ? 1 : 0;

    if (Auth::has_access('admin.admin'))
    {
      Model_Stats_Pitching::replaceAll($ids, $pitcher, $status);
    } else
    {
      Model_Stats_Pitching::regist($ids, $pitcher, $status);
    }

    echo 'OK';
  }

  public function post_updateBatter()
  {
    $ids = self::_getIds();

    // satasへの登録
    $batter = Input::post('stats');
    $status = Input::post('complete') === 'true' ? 1 : 0;

    if (Auth::has_access('admin.admin'))
    {
      Model_Stats_Hitting::replaceAll($ids, $batter, $status);
    } else
    {
      Model_Stats_Hitting::regist($ids, $batter, $status);
    }

    echo 'OK';
  }

  public function post_updateOther()
  {
    // 権限チェック
    if ( ! Auth::has_access('game.editall'))
      return Response::forge('編集する権限がありません', 403);

    $ids = self::_getIds();

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

    echo 'OK';
  }
}
