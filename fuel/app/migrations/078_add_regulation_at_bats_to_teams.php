<?php

namespace Fuel\Migrations;

class Add_regulation_at_bats_to_teams
{
	public function up()
	{
		\DBUtil::add_fields('teams', array(
			'regulation_at_bats' => array('constraint' => 4, 'type' => 'varchar', 'default' => '2.0'),

		));
	}

	public function down()
	{
		\DBUtil::drop_fields('teams', array(
			'regulation_at_bats'

		));
	}
}
