<?php

class Controller_Api_Game extends Controller_Rest
{
  public function post_updateStatus()
  {
    $game = Model_Game::find(Input::post('id'));
    $game->game_status = Input::post('status');
    $game->save();

    return "OK";
  }

  public function post_updatePlayer()
  {
    // parameter check
    $order = Input::post('order');
    $game_id = Input::post('game_id');

    if ( ! $order or ! $game_id )
    {
      return Response::forge('NG', 400);
    }

    // stamen 登録
    $players = Input::post('players');

    $game = Model_Games_Stat::query()
              ->where('game_id', $game_id)
              ->where('order', $order)
              ->get_one();

    $game->players = json_encode($players); 
    $game->save();

    echo 'OK';
  }

  public function post_updatePitcher()
  {
    // parameter check
    $order = Input::post('order');
    $game_id = Input::post('game_id');

    if ( ! $order or ! $game_id )
    {
      return Response::forge('NG', 400);
    }

    $pitcher = Input::post('pitcher');

    $game = Model_Games_Stat::query()
              ->where('game_id', $game_id)
              ->where('order', $order)
              ->get_one();
    $game->pitchers = json_encode($pitcher); 
    $game->save();

    echo 'OK';
  }

  public function post_updateBatter()
  {
    // parameter check
    $order = Input::post('order');
    $game_id = Input::post('game_id');

    if ( ! $order or ! $game_id )
    {
      return Response::forge('NG', 400);
    }

    $batter = Input::post('batter');

    $game = Model_Games_Stat::query()
              ->where('game_id', $game_id)
              ->where('order', $order)
              ->get_one();

    $game->batters = json_encode($batter); 
    $game->save();

    echo 'OK';
  }

  public function post_updateOther()
  {
    // parameter check
    $order   = Input::post('order');
    $game_id = Input::post('game_id');

    if ( ! $order or ! $game_id )
      return Response::forge('NG', 400);

    // insert
    $other = Input::post('other');

    $game = Model_Games_Stat::query()
              ->where('game_id', $game_id)
              ->where('order', $order)
              ->get_one();

    $game->others = json_encode($other); 
    $game->save();

    echo 'OK';
  }

}
