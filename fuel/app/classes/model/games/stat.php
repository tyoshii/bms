<?php

class Model_Games_Stat extends \Orm\Model
{
  protected static $_properties = array(
      'id',
      'game_id',
      'order',
      'team_id',
      'players',
      'pitchers',
      'batters',
      'others',
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
  protected static $_table_name = 'games_stats';

  public static function createNewGame($game_id, $top, $bottom)
  {
    $default_players = array();
    for ($i = 1; $i <= 9; $i++)
    {
      $default_players[] = array(
          'order'     => $i,
          'member_id' => 0,
          'position'  => array(0, 0, 0, 0, 0, 0),
      );
    }

    $props = array(
        'game_id'  => $game_id,
        'players'  => json_encode($default_players),
        'pitchers' => '',
        'batters'  => '',
        'others'   => '',
    );

    foreach (array('top' => $top, 'bottom' => $bottom) as $order => $team_id)
    {
      if (!$team_id or $team_id == 0) continue;

      $stat = self::forge($props);

      $stat->order = $order;
      $stat->team_id = $team_id;

      $stat->save();
    }

  }
}
