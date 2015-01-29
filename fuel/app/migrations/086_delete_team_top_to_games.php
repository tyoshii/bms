<?php

namespace Fuel\Migrations;

class Delete_team_top_to_games
{
	public function up()
	{
		\DBUtil::drop_fields('games', array(
			'team_top',
			'team_top_name',
			'team_bottom',
			'team_bottom_name'
		));
	}

	public function down()
	{
		\DBUtil::add_fields('', array(
			'team_top' => array('constraint' => 11, 'type' => 'int'),
			'team_top_name' => array('constraint' => 128, 'type' => 'varchar'),
			'team_bottom' => array('constraint' => 11, 'type' => 'int'),
			'team_bottom_name' => array('constraint' => 128, 'type' => 'varchar'),
		));
	}
}
