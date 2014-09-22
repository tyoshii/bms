<?php

namespace Fuel\Migrations;

class Add_bottom_status_to_games
{
	public function up()
	{
		\DBUtil::add_fields('games', array(
			'bottom_status' => array('constraint' => 11, 'type' => 'int'),

		));
	}

	public function down()
	{
		\DBUtil::drop_fields('games', array(
			'bottom_status'

		));
	}
}