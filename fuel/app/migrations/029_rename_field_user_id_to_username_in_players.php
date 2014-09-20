<?php

namespace Fuel\Migrations;

class Rename_field_user_id_to_username_in_players
{
  public function up()
  {
    \DBUtil::modify_fields('players', array(
        'user_id' => array('name' => 'username', 'type' => 'int unsigned')
    ));
  }

  public function down()
  {
    \DBUtil::modify_fields('players', array(
        'username' => array('name' => 'user_id', 'type' => 'int unsigned')
    ));
  }
}
