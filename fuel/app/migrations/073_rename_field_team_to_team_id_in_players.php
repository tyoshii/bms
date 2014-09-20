<?php

namespace Fuel\Migrations;

class Rename_field_team_to_team_id_in_players
{
  public function up()
  {
    \DBUtil::modify_fields('players', array(
        'team' => array('name' => 'team_id', 'type' => 'int', 'constraint' => 11)
    ));
  }

  public function down()
  {
    \DBUtil::modify_fields('players', array(
        'team_id' => array('name' => 'team', 'type' => 'int', 'constraint' => 11)
    ));
  }
}