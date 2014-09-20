<?php

namespace Fuel\Migrations;

class Add_status_to_players
{
  public function up()
  {
    \DBUtil::add_fields('players', array(
        'status' => array('constraint' => 11, 'type' => 'int'),

    ));
  }

  public function down()
  {
    \DBUtil::drop_fields('players', array(
        'status'

    ));
  }
}