<?php

class Model_Stats_Meta extends \Orm\Model
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
	protected static $_table_name = 'stats_meta';

  public static function getStarter( $game_id, $team_id )
  {
    $query = DB::select()->from(array(self::$_table_name, 'meta'));

    $query->join('players', 'LEFT')->on('meta.player_id', '=', 'players.id');

    $query->where(array(
      'meta.game_id' => $game_id,
      'meta.team_id' => $team_id,
    ));

    $query->order_by('meta.disp_order');

    return $query->execute()->as_array();
  }

  // - TODO metaから取るのおかしいので修正する
  // Starter情報はコントローラー側で持っているので
  // 野手成績と同様のロジックとする。
  // （player_idをキーとして配列を返して、twigにplayerとstatsの２つを渡す
  public static function getPitchingStats( $game_id, $team_id )
  {
    $query = DB::select()->from(array(self::$_table_name, 'meta'));

    $query->join('players', 'LEFT')->on('meta.player_id', '=', 'players.id');
    $query->join('stats_pitchings', 'LEFT')
          ->on('meta.player_id', '=', 'stats_pitchings.player_id')
          ->on('meta.game_id',   '=', 'stats_pitchings.game_id')
          ->on('meta.team_id',   '=', 'stats_pitchings.team_id');

    $query->where(array(
      'meta.game_id' => $game_id,
      'meta.team_id' => $team_id,
    ));
    $query->where('meta.position', 'LIKE', '%1%');

    $query->order_by('meta.disp_order');

    return $query->execute()->as_array();
  }

  public static function registPlayer($ids, $players)
  {
    DB::start_transaction();

    try {

      // clean data
      DB::delete(self::$_table_name)
        ->where($ids)
        ->execute();

      // regist new data
      foreach ( $players as $disp_order => $player )
      {
        if ( ! $player ) continue;

        $meta = self::forge($ids + array(
          'disp_order' => $disp_order,
          'player_id'  => $player['player_id'],
          'order'      => $player['order'] ?: 0,
          'position'   => implode(',', $player['position']),
        ));

        $meta->save();
      }

      DB::commit_transaction();
    } catch ( Exception $e ) {
      DB::rollback_transaction();
      throw new Exception();
    }
  }
}
