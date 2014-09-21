<?php

namespace Fuel\Migrations;

class Add_opponent_team_name_to_games_teams
{
	public function up()
	{
		\DBUtil::add_fields('games_teams', array(
				'opponent_team_name' => array('constraint' => 64, 'type' => 'varchar'),

		));
	}

	public function down()
	{
		\DBUtil::drop_fields('games_teams', array(
				'opponent_team_name'

		));
	}
}