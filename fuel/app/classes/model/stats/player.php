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

    $result = $query->execute()->as_array();

    foreach ( $result as $index => $res )
    {
      if ( $res['position'] == '' )
      {
        $result[$index]['position'][] = '';
      }
      else
      {
        // テンプレート表示の際に、次のselectボックスを出すために、
        // 配列の最後にから配列を入れる。
        $temp   = explode(',', $res['position']);
        $temp[] = '';
        $result[$index]['position'] = $temp;
      }
    }

    return $result;
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
    Mydb::begin();

    try {

      // clean data
      Common::db_clean(self::$_table_name, $ids);

      // regist new data
      foreach ( $players as $disp_order => $player )
      {
        if ( ! $player ) continue;

        // player_id, order, position
        extract($player);

        $player = self::forge($ids + array(
          'disp_order' => $disp_order,
          'player_id'  => $player_id,
          'order'      => $order ?: 0,
          'position'   => isset($position) ? implode(',', $position ) : '',
        ));

        $player->save();
      }

      Mydb::commit();

    } catch ( Exception $e ) {
      Mydb::rollback();
      throw new Exception($e->getMessage());
    }
  }
}
