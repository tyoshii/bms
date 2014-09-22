<?php

namespace Fuel\Migrations;

class Add_stadium_to_games
{
	public function up()
	{
		\DBUtil::add_fields('games', array(
			'stadium' => array('constraint' => 64, 'type' => 'varchar'),

		));
	}

	public function down()
	{
		\DBUtil::drop_fields('games', array(
			'stadium'

		));
	}
}