<?php

namespace Fuel\Migrations;

class Add_role_to_players
{
	public function up()
	{
		\DBUtil::add_fields('players', array(
			'role' => array('constraint' => '"user","admin"', 'type' => 'enum'),

		));
	}

	public function down()
	{
		\DBUtil::drop_fields('players', array(
			'role'

		));
	}
}