<?php

namespace Fuel\Migrations;

class Create_games_stats
{
  public function up()
  {
    \DBUtil::create_table('games_stats', array(
        'id'         => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
        'game_id'    => array('constraint' => 11, 'type' => 'int', 'unsigned' => true),
        'order'      => array('constraint' => 8, 'type' => 'varchar'),
        'team_id'    => array('constraint' => 11, 'type' => 'int', 'unsigned' => true),
        'players'    => array('type' => 'text'),
        'pitchers'   => array('type' => 'text'),
        'batters'    => array('type' => 'text'),
        'others'     => array('type' => 'text'),
        'created_at' => array('constraint' => 11, 'type' => 'int', 'null' => true),
        'updated_at' => array('constraint' => 11, 'type' => 'int', 'null' => true),

    ), array('id'));
  }

  public function down()
  {
    \DBUtil::drop_table('games_stats');
  }
}