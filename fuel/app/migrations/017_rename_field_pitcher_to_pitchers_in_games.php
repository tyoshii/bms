<?php

namespace Fuel\Migrations;

class Rename_field_pitcher_to_pitchers_in_games
{
  public function up()
  {
    \DBUtil::modify_fields('games', array(
        'pitcher' => array('name' => 'pitchers', 'type' => 'text')
    ));
  }

  public function down()
  {
    \DBUtil::modify_fields('games', array(
        'pitchers' => array('name' => 'pitcher', 'type' => 'text')
    ));
  }
}