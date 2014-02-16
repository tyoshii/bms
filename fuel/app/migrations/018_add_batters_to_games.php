<?php

namespace Fuel\Migrations;

class Add_batters_to_games
{
	public function up()
	{
		\DBUtil::add_fields('games', array(
			'batters' => array('type' => 'text'),

		));
	}

	public function down()
	{
		\DBUtil::drop_fields('games', array(
			'batters'

		));
	}
}