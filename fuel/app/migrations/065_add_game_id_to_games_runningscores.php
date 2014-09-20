<?php

namespace Fuel\Migrations;

class Add_game_id_to_games_runningscores
{
  public function up()
  {
    \DBUtil::add_fields('games_runningscores', array(
        'game_id' => array('constraint' => 11, 'type' => 'int', 'unsigned' => true),

    ));
  }

  public function down()
  {
    \DBUtil::drop_fields('games_runningscores', array(
        'game_id'

    ));
  }
}