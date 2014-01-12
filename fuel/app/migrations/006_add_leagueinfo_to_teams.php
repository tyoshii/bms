<?php

namespace Fuel\Migrations;

class Add_leagueinfo_to_teams
{
	public function up()
	{
		\DBUtil::add_fields('teams', array(
			'league' => array('constraint' => 11, 'type' => 'int'),

		));
	}

	public function down()
	{
		\DBUtil::drop_fields('teams', array(
			'league'

		));
	}
}
