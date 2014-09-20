<?php

namespace Fuel\Migrations;

class Delete_league_from_teams
{
  public function up()
  {
    \DBUtil::drop_fields('teams', array(
        'league'

    ));
  }

  public function down()
  {
    \DBUtil::add_fields('teams', array(
        'league' => array('constraint' => 11, 'type' => 'int'),

    ));
  }
}