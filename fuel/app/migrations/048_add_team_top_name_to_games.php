<?php

namespace Fuel\Migrations;

class Add_team_top_name_to_games
{
	public function up()
	{
		\DBUtil::add_fields('games', array(
			'team_top_name' => array('constraint' => 128, 'type' => 'varchar'),

		));
	}

	public function down()
	{
		\DBUtil::drop_fields('games', array(
			'team_top_name'

		));
	}
}