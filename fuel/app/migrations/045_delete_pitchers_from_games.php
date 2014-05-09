<?php

namespace Fuel\Migrations;

class Delete_pitchers_from_games
{
	public function up()
	{
		\DBUtil::drop_fields('games', array(
			'pitchers'

		));
	}

	public function down()
	{
		\DBUtil::add_fields('games', array(
			'pitchers' => array('type' => 'text'),

		));
	}
}