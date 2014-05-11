<?php

class Model_Stats_Player extends \Orm\Model
{
	protected static $_properties = array(
		'id',
		'game_id',
    'team_id',
		'player_id',
		'order',
		'position',
    'disp_order',
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
	protected static $_table_name = 'stats_players';

  public static function getStarter( $game_id, $team_id )
  {
    $query = DB::select()->from(array(self::$_table_name, 'player'));

    $query->join('players', 'LEFT')->on('player.player_id', '=', 'players.id');

    $query->where(array(
      'player.game_id' => $game_id,
      'player.team_id' => $team_id,
    ));

    $query->order_by('player.disp_order');

    return $query->execute()->as_array();
  }

  // - TODO playerから取るのおかしいので修正する
  // Starter情報はコントローラー側で持っているので
  // 野手成績と同様のロジックとする。
  // （player_idをキーとして配列を返して、twigにplayerとstatsの２つを渡す
  public static function getPitchingStats( $game_id, $team_id )
  {
    $query = DB::select()->from(array(self::$_table_name, 'player'));

    $query->join('players', 'LEFT')->on('player.player_id', '=', 'players.id');
    $query->join('stats_pitchings', 'LEFT')
          ->on('player.player_id', '=', 'stats_pitchings.player_id')
          ->on('player.game_id',   '=', 'stats_pitchings.game_id')
          ->on('player.team_id',   '=', 'stats_pitchings.team_id');

    $query->where(array(
      'player.game_id' => $game_id,
      'player.team_id' => $team_id,
    ));
    $query->where('player.position', 'LIKE', '%1%');

    $query->order_by('player.disp_order');

    return $query->execute()->as_array();
  }

  public static function createNewGame($game_id, $team_id)
  {
    if ( ! $team_id ) return false;

    $ids = array(
      'game_id' => $game_id,
      'team_id' => $team_id,
    );

    $default = array();
    for ( $i = 1; $i < 10; $i++ )
    {
      $default[] = array(
        'player_id' => 0,
        'order'     => $i,
        'position'  => array(0,0,0,0,0,0),
      );
    }

    self::registPlayer( $ids, $default );
  }

  public static function registPlayer($ids, $players)
  {
    MyDB::begin();

    try {

      // clean data
      Common::db_clean(self::$_table_name, $ids);

      // regist new data
      foreach ( $players as $disp_order => $player )
      {
        if ( ! $player ) continue;

        $player = self::forge($ids + array(
          'disp_order' => $disp_order,
          'player_id'  => $player['player_id'],
          'order'      => $player['order'] ?: 0,
          'position'   => implode(',', $player['position']),
        ));

        $player->save();
      }

      MyDB::commit();

    } catch ( Exception $e ) {
      MyDB::rollback();
      throw new Exception($e->getMessage());
    }
  }
}
