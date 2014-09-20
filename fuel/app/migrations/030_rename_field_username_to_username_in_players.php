<?php

namespace Fuel\Migrations;

class Rename_field_username_to_username_in_players
{
  public function up()
  {
    \DBUtil::modify_fields('players', array(
        'username' => array('name' => 'username', 'constraint' => 50, 'type' => 'varchar')
    ));
  }

  public function down()
  {
    \DBUtil::modify_fields('players', array(
        'username' => array('name' => 'username', 'type' => 'int unsigned')
    ));
  }
}
