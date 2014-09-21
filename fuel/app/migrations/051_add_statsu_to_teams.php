<?php

namespace Fuel\Migrations;

class Add_statsu_to_teams
{
	public function up()
	{
		\DBUtil::add_fields('teams', array(
			'statsu' => array('constraint' => 8, 'type' => 'varchar'),

		));
	}

	public function down()
	{
		\DBUtil::drop_fields('teams', array(
			'statsu'

		));
	}
}