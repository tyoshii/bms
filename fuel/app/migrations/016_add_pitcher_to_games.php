<?php

namespace Fuel\Migrations;

class Add_pitcher_to_games
{
	public function up()
	{
		\DBUtil::add_fields('games', array(
			'pitcher' => array('type' => 'text'),

		));
	}

	public function down()
	{
		\DBUtil::drop_fields('games', array(
			'pitcher'

		));
	}
}