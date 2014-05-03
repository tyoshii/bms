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

    return $val->validated();
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
    $ids = self::_getIds();

    // stamen 登録(old type/json)
    $players = Input::post('players');

    $game = Model_Games_Stat::query()
              ->where(array($ids))
              ->get_one();

    $game->players = json_encode($players); 
    $game->save();

    // stats_meta登録
    foreach ( $players as $player )
    {
      $meta = Model_Stats_Meta::query()
                ->where('game_id', $game_id)
                ->where('player_id', $player['player_id'])
                ->get_one()
              ?:
              Model_Stats_Meta::forge(array(
                'game_id' => $game_id,
                'player_id' => $player['player_id'],
              ));

      $meta->order    = $player['order'] ?: 0;
      $meta->position = implode(',', $player['position']);
    
      $meta->save();
    }

    echo 'OK';
  }

  public function post_updatePitcher()
  {
    $ids = self::_getIds();

    // insert
    $pitcher = Input::post('pitcher');

    $game = Model_Games_Stat::query()
              ->where(array($ids))
              ->get_one();
    $game->pitchers = json_encode($pitcher); 
    $game->save();

    echo 'OK';
  }

  public function post_updateBatter()
  {
    $ids = self::_getIds();

    // insert
    $batter = Input::post('batter');

    $game = Model_Games_Stat::query()
            ->where(array($ids))
            ->get_one();

    $game->batters = json_encode($batter); 
    $game->save();

    echo 'OK';
  }

  public function post_updateOther()
  {
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
