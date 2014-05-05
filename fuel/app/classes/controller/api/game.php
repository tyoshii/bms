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

    // stats_metaへの登録
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

    // stats_pitchingsへのinsert
    foreach ( $pitcher as $player_id => $pitch )
    {
      if ( ! $pitch )
        continue;

      $p = Model_Stats_Pitching::query()->where(array(
             'game_id'   => $ids['game_id'],
             'player_id' => $player_id,
           ))->get_one()
           ?:
           Model_Stats_Pitching::forge(array(
             'game_id'   => $ids['game_id'],
             'player_id' => $player_id,
           ));

      $p->set(array(
        'W'   => $pitch['result'] == 'win'  ?  1 : 0,
        'L'   => $pitch['result'] == 'lose' ?  1 : 0,
        'HLD' => $pitch['result'] == 'hold' ?  1 : 0,
        'SV'  => $pitch['result'] == 'save' ?  1 : 0,
        'IP'  => $pitch['inning_int'] + $pitch['inning_frac'],
        'H'   => $pitch['hianda'],
        'SO'  => $pitch['sanshin'],
        'BB'  => $pitch['shishikyuu'],
        'HB'  => 0,
        'ER'  => $pitch['earned_runs'],
        'R'   => $pitch['runs']
      ));

      $p->save();
    }

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

    // satasへの登録
    foreach ( $batter as $player_id => $bat )
    {
      if ( ! $bat )
        continue;

      // hittingsへのinsert
      $hit = Model_Stats_Hitting::query()->where(array(
               'game_id' => $ids['game_id'],
               'player_id' => $player_id,
             ))->get_one()
             ?:
             Model_Stats_Hitting::forge(array(
               'game_id' => $ids['game_id'],
               'player_id' => $player_id,
             ));

      $hit->set(array(
        'TPA' => $bat['seiseki']['daseki'],
        'AB'  => $bat['seiseki']['dasuu'],
        'H'   => $bat['seiseki']['anda'],
        '2B'  => $bat['seiseki']['niruida'],
        '3B'  => $bat['seiseki']['sanruida'],
        'HR'  => $bat['seiseki']['honruida'],
        'SO'  => $bat['seiseki']['sanshin'],
        'BB'  => $bat['seiseki']['yontama'],
        'HBP' => $bat['seiseki']['shikyuu'],
        'SAC' => $bat['seiseki']['gida'],
        'SF'  => $bat['seiseki']['gihi'],
        'RBI' => $bat['seiseki']['daten'],
        'R'   => $bat['seiseki']['tokuten'],
        'SB'  => $bat['seiseki']['steal'],
      ));

      $hit->save();

      // fieldingsへのinsert
      $field = Model_Stats_Fielding::query()->where(array(
                 'game_id' => $ids['game_id'],
                 'player_id' => $player_id,
               ))->get_one()
               ?:
               Model_Stats_Fielding::forge(array(
                 'game_id' => $ids['game_id'],
                 'player_id' => $player_id,
               ));

      $field->set(array(
        'E' => $bat['seiseki']['error'],
      ));

      $field->save();

      // hittingdetailsへのinsert
      foreach ( $bat['detail'] as $bat_times => $data )
      {
        $detail = Model_Stats_Hittingdetail::query()->where(array(
                    'game_id'   => $ids['game_id'],
                    'player_id' => $player_id,
                    'bat_times' => $bat_times + 1,
                  ))->get_one()
                  ?:
                  Model_Stats_Hittingdetail::forge(array(
                    'game_id'   => $ids['game_id'],
                    'player_id' => $player_id,
                    'bat_times' => $bat_times + 1,
                  ));

        $detail->set(array(
          'direction' => $data['direction'],
          'kind'      => $data['kind'],
          'result_id' => $data['result'],
        ));

        $detail->save();
      }

    } 

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
