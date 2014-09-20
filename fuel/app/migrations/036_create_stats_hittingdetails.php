<?php

namespace Fuel\Migrations;

class Create_stats_hittingdetails
{
  public function up()
  {
    \DBUtil::create_table('stats_hittingdetails', array(
        'id'         => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
        'player_id'  => array('constraint' => 11, 'type' => 'int', 'unsigned' => true),
        'game_id'    => array('constraint' => 11, 'type' => 'int', 'unsigned' => true),
        'bat_times'  => array('type' => 'tinyint', 'unsigned' => true),
        'direction'  => array('type' => 'tinyint', 'unsigned' => true),
        'kind'       => array('type' => 'tinyint', 'unsigned' => true),
        'result_id'  => array('type' => 'tinyint', 'unsigned' => true),
        'created_at' => array('constraint' => 11, 'type' => 'int', 'null' => true),
        'updated_at' => array('constraint' => 11, 'type' => 'int', 'null' => true),

    ), array('id'));
  }

  public function down()
  {
    \DBUtil::drop_table('stats_hittingdetails');
  }
}