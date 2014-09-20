<?php

namespace Fuel\Migrations;

class Rename_field_statsu_to_status_in_teams
{
  public function up()
  {
    \DBUtil::modify_fields('teams', array(
        'statsu' => array('name' => 'status', 'type' => 'varchar', 'constraint' => 8)
    ));
  }

  public function down()
  {
    \DBUtil::modify_fields('teams', array(
        'status' => array('name' => 'statsu', 'type' => 'varchar', 'constraint' => 8)
    ));
  }
}