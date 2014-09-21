<?php

namespace Fuel\Migrations;

class Add_team_bottom_name_to_games
{
	public function up()
	{
		\DBUtil::add_fields('games', array(
				'team_bottom_name' => array('constraint' => 128, 'type' => 'varchar'),

		));
	}

	public function down()
	{
		\DBUtil::drop_fields('games', array(
				'team_bottom_name'

		));
	}
}