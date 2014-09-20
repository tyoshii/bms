<?php

namespace Fuel\Migrations;

class Rename_field_starting_member_to_players_in_games
{
  public function up()
  {
    \DBUtil::modify_fields('games', array(
        'starting_member' => array('name' => 'players', 'type' => 'text')
    ));
  }

  public function down()
  {
    \DBUtil::modify_fields('games', array(
        'players' => array('name' => 'starting_member', 'type' => 'text')
    ));
  }
}