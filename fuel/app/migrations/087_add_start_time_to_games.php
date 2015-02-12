<?php

namespace Fuel\Migrations;

class Add_start_time_to_games
{
	public function up()
	{
		\DBUtil::add_fields('games', array(
			'start_time' => array('type' => 'time'),

		));
	}

	public function down()
	{
		\DBUtil::drop_fields('games', array(
			'start_time'

		));
	}
}