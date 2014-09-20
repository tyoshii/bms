<?php

namespace Fuel\Migrations;

class Rename_field_number_to_number_in_players
{
  public function up()
  {
    \DBUtil::modify_fields('players', array(
        'number' => array('name' => 'number', 'type' => 'varchar', 'constraint' => 8)
    ));
  }

  public function down()
  {
    \DBUtil::modify_fields('players', array(
        'number' => array('name' => 'number', 'type' => 'int', 'constraint' => 11)
    ));
  }
}
